<?php 

Class RealtyController extends ControllerBase {

    public function index() {}

    public function autoarchivate() {

    	$model = $this->getModel('realty');
    	$config = Registry::get('config');

    	$date_from = "2001-01-01 00:00:00";
    	$date_before = date("Y-m-d H:i:s", strtotime('-'.$config->realty_autoarchivate_days.' days'));

    	echo 'Autoarchivate objects before: '.$date_before . "\r\n";

		$items = $model->getItemsByDate($date_from, $date_before);

		$messages = array();		

		// Update items and generate messages
		foreach ($items as $key => $item) {

			// Params to update items
			$params = array();
			$params['archive'] = 1;

			// Saving item
			if ($model->SaveItem($item->id, $params)) {

				echo 'Объект '.$item->id.' успешно помещен в архив'."\r\n";

				// Group items by author
				if (!isset($messages[$item->author_id])) {
					
					$message = new stdClass;
					$message->author_email = $item->email;
					$message->author_name = $item->name;
					$message->items = array();
					
					$messages[$item->author_id] = $message;

				}

				$messages[$item->author_id]->items[] = $item;

			}

		}

		// Sending emails
		foreach ($messages as $message) {
			
			$to       = $message->author_email;
			$subject  = $config->SiteName.' - ваш объект помещен в архив';
			$text  = 'Некоторые из ваших объектов автоматически помещены в архив по истечению '.$config->realty_autoarchivate_days.' дней.' . "\r\n";				
			$text .= 'Вы можете восстановить их в разделе "Архив объектов".' . "\r\n";								
			$text .= 'http://'.$config->BaseURL.'/admin/realty/objects/archive' . "\r\n\r\n";

			$text .= 'Список объектов:' . "\r\n\r\n";

			foreach ($message->items as $item) {
					
				$text .= $item->adress . ' (http://'.$config->BaseURL.'/admin/realty/objects/edit/'.$item->id.')'."\r\n";								

			}

			mail($to, $subject, $text);

		}

		echo 'End of autoarchivate' . "\r\n";

    }

    function autochecknew() {
		// get model
		$model = $this->getModel('realty_requests');
		// get site configuration
		$config = Registry::get('config');		
		// get requests list
		$model->setFilter('archive', '=', 0);
		$items = $model->getItems();
		// for all requests
		foreach ($items as $item) {
			// get new objects list
			$objects = $model->getNewRequestObjects($item->id);
			// if request have new objects
			if (!count($objects)) {
				continue;
			}
			// generating message text
			$message = "В базе beescom.ru появились новые объекты, которые подходят по вашему запросу:\n\n";
			// for all new objects
			$new_objects_count = 0;
			foreach ($objects as $object) {
				$message .= $object->adress . "\n";
				$model->setNewRequestObjectViwed($item->id, $object->id);
				$new_objects_count++;
			}
			// save new objects count to request item
			$data = array();
			$data['new_objects_count'] = $new_objects_count;
			$model->SaveItem($item->id, $data);
			// complite message text
			$message .= "\n";
			$message .= "Перейдите по ссылке ниже, чтобы посмотреть новые объекты:\n";
			$message .= $config->BaseURL."/admin/realty/requests/view/".$item->id."\n\n";
			// email configuration			
			$email = new Email;
			
			$email->to = array();
			$email->to[] = $item->user_email;
			$email->to[] = 'y.yurso@gmail.com';

			$email->subject = 'Новые объекты по вашему запросу';
			$email->message = $message;
			// email sending
			$email->send();
			// show message
			echo $message;
		}

	}

}