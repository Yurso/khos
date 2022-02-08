<?php
Class TasksMailController Extends ControllerBase {

	public function index() {

		$model = $this->getModel('mail');
		$messages = $model->getMessages('FLAGGED');

		echo stripslashes(json_encode($messages, JSON_UNESCAPED_UNICODE));		

	}

	public function flagged() {

		$model = $this->getModel('mail');

		$messages = $model->getMessages('FLAGGED');

		foreach ($messages as $message) {
			$item = $model->getTasksItemByMsgno($message->msgno);	
			if (isset($item->id) && $item->id > 0) {
				$message->item = $item;
			}
		}

		echo stripslashes(json_encode($messages, JSON_UNESCAPED_UNICODE));		

	}

	public function message() {

		$args = Registry::get('route')->args;	

		$message = '';

    	if (isset($args[0])) {

    		$msgno = intval($args[0]);

			$model = $this->getModel('mail');
			
			$message = $model->getMessage($msgno);

		}

		echo stripslashes(json_encode($message, JSON_UNESCAPED_UNICODE));

    }

    public function unflag() {

    	$args = Registry::get('route')->args;

    	$result = array('status' => 0, 'message' => '');

    	if (isset($args[0])) {

    		$msgno = intval($args[0]);

    		$model = $this->getModel('mail');

    		$result['status'] = $model->unFlagMessage($msgno);

    	}

    	echo stripslashes(json_encode($result, JSON_UNESCAPED_UNICODE));

    }

    public function attachments() {

		$args = Registry::get('route')->args;	

		$message = '';

    	if (isset($args[0])) {

    		$msgno = intval($args[0]);

			$model = $this->getModel('mail');
			
			$message = $model->getMessage($msgno, true);

		}

		//print_r($message->attachments);

		echo json_encode($message->attachments, JSON_UNESCAPED_UNICODE);

    }

    public function send() {

    	

    }

    public function hello() {

    	$loaded = 0;

    	// load models
    	$mail_model = $this->getModel('mail');
    	$customers_model = $this->getModel('customers');
    	$items_model = $this->getModel('items');
    	$types_model = $this->getModel('types');    	
    	$params_model = $this->getModel('params');

    	// get types list
    	$types = $types_model->getItems();
    	
    	// get last viewed unix date
    	$mail_last_udate = intval($params_model->getParamValue('mail_last_udate'));
    	
    	// set since date value
    	$since_date = date("d-M-Y", $mail_last_udate);
    	echo $mail_last_udate . "\n";
    	echo $since_date . "\n";
    	
    	// search messsages
    	$items = $mail_model->getMessages('SINCE "'.$since_date.'"');
    	
    	echo "Found " . count($items) . " messages" . "\n";

    	$last_udate = $mail_last_udate;

    	foreach ($items as $item) {
    		// Continue if message date less than stored date
    		if (intval($item->udate) <= $mail_last_udate) continue;
    		// Store maximum message date
    		if (intval($item->udate) > $last_udate) $last_udate = $item->udate;
    		// Found email
    		preg_match("~<(.*)>~", $item->from, $matches);
    		$from_email = $matches[1];
    		// Get customer id
    		$item->customer_id = $customers_model->getCustomerByEmail($from_email);

    		if ($item->customer_id == 0) continue;

    		$message = $mail_model->getMessage($item->msgno);

    		$description = $message->body;    		

    		if (count($message->attachments)) {
				$message_attachments = $message->attachments;
				$description .= "\n\n---attachments---\n";
				foreach ($message->attachments as $attachment) {				
					$description .= $attachment->filename."\n";
				}
			}    		

    		$params = array();

    		$params['date'] = date("Y-m-d H:i:s");
			$params['title'] = $message->header->subject;
			$params['type_id'] = $types[1]->id;
			$params['state'] = 0;
			$params['customer_id'] = $item->customer_id;
			$params['count'] = 1;
			$params['price'] = $types[1]->default_price;
			$params['url'] = '';
			$params['paid'] = 0;
			$params['comment'] = date("Y-m-d H:i:s") . ' mail robot';
			$params['description'] = $description;
			$params['message_msgno'] = $item->msgno;
			$params['message_id'] = $message->header->message_id;
			$params['message_subject'] = $message->header->subject;
			$params['message_reply_to'] = $from_email;			
			//$params['message_attachments'] = $message_attachments;
			
			if ($items_model->SaveNewItem($params)) {
				echo "I'm load new task from email with subject " . $message->header->subject . "\n";
				$loaded++;
				if ($item->seen == 0) {
					$mail_model->setMessageFlag($item->msgno, "\\Seen");
				}				
			}

    	}

    	echo "Loaded " . $loaded . " messages";

    	$params_model->setParamValue('mail_last_udate', $last_udate);

    	print_r($items);

    }

}
