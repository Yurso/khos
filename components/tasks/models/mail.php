<?php
Class TasksMailModel Extends ModelBase {

	// IMAP MAIL FUNCTIONS

	private function connect_to_imap() {

		$config = Registry::get('config');

		$hostname = $config->imap_hostname;
		$username = $config->imap_username;
		$password = $config->imap_password;

		$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to server: ' . imap_last_error());

		return $inbox;

	}

	private function decode_overview($overview) {

		if (isset($overview[0]->subject)) {
        	$overview[0]->subject = htmlspecialchars(imap_utf8($overview[0]->subject));
    	} else {
    		$overview[0]->subject = htmlspecialchars("<без темы>");
    	}

    	if (isset($overview[0]->from)) {
        	$overview[0]->from = imap_utf8($overview[0]->from);
        	$overview[0]->from_name = preg_replace("! <(.*?)>!si","",$overview[0]->from);        	
        	//$overview[0]->from_name = preg_replace("!<(.*?)>!si","",$overview[0]->from);
    	}

    	if (isset($overview[0]->to)) {
        	$overview[0]->to = htmlspecialchars(imap_utf8($overview[0]->to));
    	} 

    	if (isset($overview[0]->date)) {
        	$parts = explode(' ', $overview[0]->date);        	
        	$overview[0]->date = $parts[1].' '.$parts[2];
    	} 

    	return $overview; 

	}

	private function structure_encoding($encoding, $msg_body){

		switch((int) $encoding){

			case 4:
				$body = imap_qprint($msg_body);
				break;

			case 3:
				$body = imap_base64($msg_body);
				break;

			case 2:
				$body = imap_binary($msg_body);
				break;

			case 1:
				$body = imap_8bit($msg_body);
				break;

			case 0:
				$body = $msg_body;
				break;
			
			default:
				$body = "";
				break;
		}

		return $body;
	}

	private function encode_message_body($part_structure, $body) {

		$body = $this->structure_encoding($part_structure->encoding, $body);	
		// parse part parameters for charset and convert encoding if it's not utf-8
		foreach ($part_structure->parameters as $parameter) {
			if ($parameter->attribute == 'CHARSET' && $parameter->value != 'utf-8') {
				$body = mb_convert_encoding($body, 'UTF-8', $parameter->value);
			} 
		}

		return $body;

	}

	public function getMessages($criteria = "NEW") {

		$result = array();

		$inbox = $this->connect_to_imap();

		$emails = imap_search($inbox, $criteria);		

		if ($emails) {

			rsort($emails);

		    foreach ($emails as $email_number) {
		        
		        $overview = imap_fetch_overview($inbox,$email_number,0);

		        $overview = $this->decode_overview($overview);

		    	$result[] = $overview[0];

		    }

		}

		imap_close($inbox);

		return $result;

	}

	public function getMessage($msgno, $fetch_attachments = false) {

		$filespath = Params::getParamValue('tasks_files_path', 'tmp/');

		$msgno = intval($msgno);

		$message = new stdClass;
		$message->header = '';
		$message->body = '';
		$message->attachments = array();

		// init connection to imap server
		$inbox = $this->connect_to_imap();	

		// fetching and decode message
		$overview = imap_fetch_overview($inbox,$msgno,'0');

		$overview = $this->decode_overview($overview);  

    	// save overview to message header
    	$message->header = $overview[0];		

    	// fetching parts of message (collect body and attachments)
		$st = imap_fetchstructure($inbox, $msgno);

		if (!empty($st->parts)) {
			
			foreach ($st->parts as $key1 => $part1) {

				if ($part1->type == 0) {

					$current_part = $key1;

					$message->body = $this->encode_message_body($part1, imap_fetchbody($inbox, $msgno, $current_part));

				} elseif ($part1->type == 1) {

					foreach ($part1->parts as $key2 => $part2) {

						$current_part = $key1 + 1;
						$current_part .= '.';
						$current_part .= $key2 + 1;							
						
						if ($part2->subtype == 'PLAIN') {									
			 				// fetching and ecnoding body message
			 				$message->body = $this->encode_message_body($part2, imap_fetchbody($inbox, $msgno, $current_part));	
			 			}

					}

				} else {

					foreach ($part1->parameters as $key => $param) {
						if ($param->attribute == 'NAME') {
							$param->value = imap_utf8($param->value);
							$part1->filename = $param->value;
						}
					}

					if ($part1->ifdparameters) {
						foreach ($part1->dparameters as $key => $param) {
							if ($param->attribute == 'FILENAME') {
								$param->value = imap_utf8($param->value);
							}
						}
					}

					if ($part1->ifdescription) {
						$part1->description	= imap_utf8($part1->description);
					}

					if ($fetch_attachments) {

						$part1->filename = $msgno.'-'.$key1.'-'.Main::filename2translit($part1->filename);   
		                $part1->fullpath = SITE_PATH . $filespath . $part1->filename;

		                if (!is_file($part1->fullpath)) {
							// fetch and ecnode attachment contents
		                    $contents = $this->structure_encoding($part1->encoding, imap_fetchbody($inbox, $msgno, $key1+1));   

			                if (empty($contents)) {	continue; }

			                file_put_contents($part1->fullpath, $contents);

			            }

			        }

		            $message->attachments[] = $part1;					

				}
				
			}

		} else {

			$message->body = $this->encode_message_body($st, imap_body($inbox, $msgno));

		}

		imap_close($inbox);

		return $message;

	}

	public function getTasksItemByMsgno($msgno) {

		$dbh = Registry::get('dbh'); 

        $query="SELECT 
                    i.id,
                    i.title,
                    c.name AS customer_name,
                    t.title AS type_title
                FROM 
                    `#__tasks_items` AS i 
                LEFT JOIN `#__tasks_customers` AS c
                    ON i.customer_id = c.id
                LEFT JOIN `#__tasks_types` AS t
                    ON i.type_id = t.id
                WHERE
                	i.message_msgno = :msgno";	

        $sth = $dbh->prepare($query);

        $params = array();
        $params['msgno'] = $msgno;

        $sth->execute($params);

        return $sth->fetch(PDO::FETCH_OBJ);

	}

	public function unFlagMessage($msgno) {

		$inbox = $this->connect_to_imap();	

		$result = imap_clearflag_full($inbox, $msgno,"\Flagged");
		$result = imap_setflag_full($inbox, $msgno, "\Seen \Answered");

		imap_close($inbox);

		return $result;

	}

	public function setMessageFlag($msgno, $flags) {

		$inbox = $this->connect_to_imap();	
		
		$result = imap_setflag_full($inbox, $msgno, $flags);

		imap_close($inbox);

		return $result;

	}

	// SMTP MAIL FUNCTIONS

	public function connect_to_smtp() {

		$config = Registry::get('config');
		/**
		 * This example shows settings to use when sending via Google's Gmail servers.
		 */
		//SMTP needs accurate times, and the PHP time zone MUST be set
		//This should be done in your php.ini, but this is how to do it if you don't have access to that		
		require SITE_PATH . 'includes/PHPMailer/class.phpmailer.php';
		require SITE_PATH . 'includes/PHPMailer/class.smtp.php';
		//Create a new PHPMailer instance
		$mail = new PHPMailer;
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = $config->smtp_hostname;
		// use
		// $mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6
		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = $config->smtp_port;
		//Set the encryption system to use - ssl (deprecated) or tls
		$mail->SMTPSecure = 'tls';
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication - use full email address for gmail
		$mail->Username = $config->smtp_username;
		//Password to use for SMTP authentication
		$mail->Password = $config->smtp_password;

		return $mail;
	}

	public function send_message($from, $to, $subject, $msg) {

		$config = Registry::get('config');
		//Init smtp connection
		$mail = $this->connect_to_smtp();
		//Set who the message is to be sent from
		$mail->setFrom($config->smtp_email, $config->smtp_name);
		//Set an alternative reply-to address
		$mail->addReplyTo($config->smtp_email, $config->smtp_name);
		//Set who the message is to be sent to
		$mail->addAddress($to);
		//Set the subject line
		$mail->Subject = $subject;
		$mail->CharSet = 'UTF-8';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->isHTML(false); 

		$mail->Body = $msg;
		//Replace the plain text body with one created manually
		//$mail->AltBody = $msg;
		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');
		//send the message, check for errors
		if (!$mail->send()) {
		    echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
		    echo "Message sent!";
		}
	}

}