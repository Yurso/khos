<?php 
Class Email {

	public $to = '';
	public $subject = '';
	public $message = '';
	public $headers = '';
	public $from = '';
	public $reply_to = '';

	public function send() {		
		
		$config = Registry::get('config');
		$result = false;

		if (empty($this->to)) {
			Main::setMessage("Не указан получатель почты. Сообщение не отправлено.");
			return $result;
		}

		// headers params
		if (empty($this->headers)) {        	

			$this->headers  = 'From: '.$config->SiteName.' <'.$config->SiteMail.'>' . "\r\n";
			if (!empty($this->from)) {
				$this->headers  = 'From: '.$this->from . "\r\n";
			}       	

            $this->headers .= 'Reply-To: '.$config->SiteMail . "\r\n";
            if (!empty($this->reply_to)) {
				$this->headers  .= 'Reply-To: '.$this->reply_to . "\r\n";
			} 

        }
        
        // send_to configuration
        if (gettype($this->to) == 'array') {
        	$to = implode(",", $this->to);
        } else {
        	$to = $this->to;
        }

        // try to send mail
        try {
			$result = mail($to, $this->subject, $this->message, $this->headers);
		} catch (Exception $e) {		    
		    Main::setMessage("Произошла ошибка при отправке сообщения для ".$this->to.":".$e->getMessage());
		    $result = false;
		}

		return $result;
	}

}