<?php
Class ForumMessagesController Extends ControllerBase {

	function index() {}

	function view() {

		$args = Registry::get('route')->args;

		$model = $this->getModel('messages');
		
		$model->initUserOrdering();		

		$files_model = $this->getModel('files');

		$user = User::getUserData();

    	if (isset($args[0])) {

    		$topic_id = intval($args[0]);

    		$topic = $model->getTopicInfo($topic_id);
    		$topic->subscription = $model->checkSubscribtion($topic_id, $user->id);

    		$model->setFilter('m.topic_id', '=', $topic_id);

    		$pagination = $model->initPagination();

    		$items = $model->getItems();

    		foreach ($items as $key => $item) {
    			$item->files = $files_model->getItems(array('message_id' => $item->id));
    		}

    		$pathway = new Pathway;				
			$pathway->addItem('Форум', '/admin/forum');
			$pathway->addItem($topic->category_title, '/admin/forum/topics/view/'.$topic->category_id);
			//$pathway->addItem('', '');

			$tmpl = new template;

			$tmpl->setVar('items', $items);
			$tmpl->setVar('topic', $topic);
			$tmpl->setVar('user', $user);
			$tmpl->setVar('pagination', $pagination);
			$tmpl->setVar('filters', $model->filters);

			$tmpl->display('messages');

    	}

	}

	// Message create controller
	function save() {

		if (isset($_POST['submit']) && $_POST['id'] == 0) {

			$model = $this->getModel('messages');
			$topic_model = $this->getModel('topics');
			$files_model = $this->getModel('files');

			$user = User::getUserData();

			$agency = $model->getUserAgency($user->id);

			$topic_id = intval($_POST['topic_id']);

			$topic = $topic_model->getItem($topic_id);

			if (isset($topic->id) && $topic->id > 0) {

				// Saving new message
				$params = array();
				$params['message'] = $model->finishMessage($_POST['message']);
				$params['message_html'] = $model->finishMessage($_POST['message'], true);	
				$params['topic_id'] = $topic_id;
				$params['author_id'] = $user->id;
				if (isset($agency->id))
					$params['agency_id'] = $agency->id;
				$params['state'] = 1;
				$params['create_date'] = date("Y-m-d H:i:s");
				$params['edit_date'] = date("Y-m-d H:i:s");

				// Don't save if message is empty
				if (empty($params['message'] )) {
					Main::redirect('/admin/forum/messages/view/'.$topic_id, 'Сообщение не может быть пустым.');
				}

				$id = $model->SaveNewItem($params);

				if ($id > 0) {

					// Updating messages date and count in topic line
					$params = array();
					$params['last_message_date'] = date("Y-m-d H:i:s");
					$params['messages_count'] = $topic->messages_count + 1;

					$topic_model->SaveItem($topic_id, $params);

					// Saving files
					$files_model->SaveUploadedFiles($id);

					// Sending subsriptions emails
					$model->sendEmails($id);					

					Main::redirect('/admin/forum/messages/view/'.$topic_id.'#m'.$id, 'Сообщение успешно сохранено.');	

				} else {
					
					Main::redirect('/admin/forum/messages/view/'.$topic_id, 'Не удалось сохранить сообщение. Обратитесь к администратору.');	

				}

			} else {

				Main::redirect('/admin/forum', 'Тема не найдена.');

			}

		} 

		elseif (isset($_POST['submit']) && $_POST['id'] > 0) {

			$user = User::getUserData();
			$model = $this->getModel('messages');

			$id = intval($_POST['id']);

			// Get item data
			$item = $model->getItem($id);

			// Don't save if message not last or it is not your message
			if ($item->create_date != $item->last_message_date || $user->id != $item->author_id) {
				Main::redirect('/admin/forum/messages/view/'.$item->topic_id, 'Вы не можете редактировать данное сообщение');
			}
			
			// Saving new message
			$params = array();
			$params['message'] = $model->finishMessage($_POST['message']);
			$params['message_html'] = $model->finishMessage($_POST['message'], true);			
			$params['edit_date'] = date("Y-m-d H:i:s");

			// Don't save if message is empty
			if (empty($params['message'])) {
				Main::redirect('/admin/forum/messages/edit/'.$id, 'Сообщение не может быть пустым.');
			}

			// Saving edited message
			if ($model->SaveItem($id, $params)) {
				Main::redirect('/admin/forum/messages/view/'.$item->topic_id.'#m'.$id, 'Сообщение успешно отредактированно.');
			} else {
				Main::redirect('/admin/forum/messages/edit/'.$id, 'Не удалось записать изменения. Обратитесь к администратору.');
			}

		}

	}

	// Message edit page
	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$user = User::getUserData();

			$model = $this->getModel('messages');			

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);				

				if ($item->author_id != $user->id) {
					Main::redirect('/admin/forum/messages/view/'.$item->topic_id, 'Вы не можете редактировать данное сообщение');	
				}

				if (strtotime($item->create_date) != strtotime($item->last_message_date)) {
					Main::redirect('/admin/forum/messages/view/'.$item->topic_id, 'Вы больше не можете редактировать данное сообщение');
				}

				$pathway = new Pathway;					
				$pathway->addItem('Форум', '/admin/forum');
				$pathway->addItem($item->category_title, '/admin/forum/topics/view/'.$item->category_id);
				$pathway->addItem($item->topic_title, '/admin/forum/messages/view/'.$item->topic_id);
				
				$tmpl = new Template;

				$tmpl->setVar('item', $item);

				$tmpl->display('messages_edit');

			} else {
				Main::redirect('/admin/forum', 'Ошибка! Сообщение не найдено');
			}

		} else {
			Main::redirect('/admin/forum', 'Не указан id элемента');
		}

	}

	// message delete
	function delete() {

		$result = array('success' => false, 'message' => '');

		$args = Registry::get('route')->args;
		$user = User::getUserData();

		$model = $this->getModel('messages');
		$files_model = $this->getModel('files');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	  

	    	$item = $model->getItem($id);

	    	$files = $files_model->getItems(array('message_id' => $item->id));

	    	if ($item->create_date == $item->last_message_date || $user->id == $item->author_id) {				

		    	if ($model->deleteItem($id)) {

		    		foreach ($files as $file) {
		    			$files_model->deleteItem($file->id);
		    		}

		    		$result['success'] = true;
		    		$result['item_id'] = $id;
		    		$result['message'] = 'Элемент успешно удален';

		    	} else {
		    		$result['message'] = 'Не удалось удалить элемент';
		    	}

		    } else {

		    	$result['message'] = 'Недостаточно прав для удаления сообщения';

		    }

	   	}

	   	echo stripslashes(json_encode($result, JSON_UNESCAPED_UNICODE));

	}

}