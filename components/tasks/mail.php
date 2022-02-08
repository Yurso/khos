<?php
Class TasksMailController Extends ControllerBase {

	public function index() {}

    public function check_for_new_tasks() {

    	$loaded = 0;

    	// load models
    	$mail_model = $this->getModel('mail');
    	$customers_model = $this->getModel('customers');
    	$items_model = $this->getModel('items');
    	$types_model = $this->getModel('types');    	
        $files_model = $this->getModel('files');    
        $messages_model = $this->getModel('messages');
        $logger = Logger::getLogger('tasks_mail_robot', null, true);    
    	//$params_model = $this->getModel('params');

    	// get types list
    	$types = $types_model->getItems();
    	
    	// get last viewed unix date
    	$mail_last_udate = intval(Params::getParamValue('tasks_mail_last_udate'));
    	
    	// set since date value
    	$since_date = date("d-M-Y", $mail_last_udate);
        $logger->log("-----------------------");
    	$logger->log($mail_last_udate);
    	$logger->log($since_date);
    	
    	// search messsages
    	$items = $mail_model->getMessages('SINCE "'.$since_date.'"');
    	
    	$logger->log("Found " . count($items) . " messages");

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

    		$message = $mail_model->getMessage($item->msgno, true);

            $description  = 'From: '.$item->from."\n";
            $description .= 'Date: '.date("d.m.Y H:i:s", $item->udate)."\n";
            $description .= 'Subject: '.$message->header->subject."\n\n";

            $description .= $message->body;    		

    		if (count($message->attachments)) {				
				$description .= "\n---attachments---\n";
				foreach ($message->attachments as $attachment) {				
					$description .= $attachment->filename."\n";
				}
			}    		

    		$params = array(
        		'date' => date("Y-m-d H:i:s"),
    			'title' => preg_replace('/([\[\(] *)?(RE|FWD?) *([-:;)\]][ :;\])-]*|$)|\]+ *$/i','',$message->header->subject),
                'type_id' => $types[1]->id,
    			'status' => 'new',
    			'customer_id' => $item->customer_id,
    			'count' => 1,
    			'price' => $types[1]->default_price,
    			'url' => '',
    			'comment' => date("Y-m-d H:i:s") . ' mail robot',
    			'description' => $description,
    			'message_msgno' => $item->msgno,
    			'message_id' => $message->header->message_id,
    			'message_subject' => $message->header->subject,
    			'message_reply_to' => $from_email,
                'author_name' => $item->from,
            );

            $item_id = $items_model->SaveNewItem($params);
			
			if ($item_id) {

                // Load attachments            
                $i = 0;
                foreach ($message->attachments as $attachment) {

                    $files_model->SaveNewItem(array(
                        'task_id' => $item_id,
                        'filename' => $attachment->filename,
                        'ordering' => $i
                    ));

                    $i++;                    
                }   

				$logger->log("New task was loaded from email with subject " . $message->header->subject);
				$loaded++;
				if ($item->seen == 0) {
					$mail_model->setMessageFlag($item->msgno, "\\Seen \\Flagged");
				}				
			} else {
                $logger->log("There is some problem with saving task");
                $logger->log($params);
            }

    	}

    	$logger->log("Loaded " . $loaded . " messages");

        if ($loaded == 1) {
            Pushbullet::sendPush(
                'Khos: создана новая задача', 
                "Тема: ".$params['title']
                ."\n\n"
                ."http://khos.ru/admin/tasks/items/edit/$item_id?ref=/admin/tasks"
            );
        } elseif ($loaded > 1) {
            Pushbullet::sendPush(
                "Khos: созданы новые задачи ($loaded)", 
                "http://khos.ru/admin/tasks"
            );
        }

    	Params::setParamValue('tasks_mail_last_udate', $last_udate);

    	//print_r($items);

    }

}
