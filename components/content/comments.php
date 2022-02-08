<?php
Class CommentsController Extends ControllerBase {

	public function index() {

	}

	public function show() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$controller = $args[0];
			$item_id = (int) $args[1];

			$model = $this->getModel('comments');

			$params = array('controller' => $controller, 'item_id' => $item_id);
			$comments = $model->getItems($params);

			$tmpl = new Template;
			$tmpl->setVar('comments', $comments);
			$tmpl->setVar('params', $params);
			$tmpl->display('comments', 'ajax');

		}

	}

	public function show_list() {
		
		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$controller = $args[0];
			$item_id = (int) $args[1];

			$model = $this->getModel('comments');

			$params = array('controller' => $controller, 'item_id' => $item_id);
			$comments = $model->getItems($params);

			$tmpl = new Template;
			$tmpl->setVar('comments', $comments);
			$tmpl->setVar('params', $params);
			$tmpl->display('comments_list', 'ajax');

		}

	}

	public function json_list() {
		
		$items = array();

		$args = Registry::get('route')->args;

		if (isset($args[0]) && isset($args[1]) && $args[1] > 0) {

			$controller = $args[0];
			$item_id = (int) $args[1];						

			$model = $this->getModel('comments');

			$model->setFilter('controller', '=', $controller);
			$model->setFilter('item_id', '=', $item_id);			
			
			// Only new comments with id > last_id
			if (isset($args[2]))
				$model->setFilter('id', '>', $args[2]);

			//$params = array('controller' => $controller, 'item_id' => $item_id);
			$items = $model->getItems();		

			foreach ($items as $key => $item) {
				$item->have_access = false;
				$item->create_date = date("d.m.y h:i", strtotime($item->create_date));
				if (User::getUserData('access_name') == 'administrator' || User::getUserData('id') == $item->user_id) {
					$item->have_access = true;	
				}
			}	

		}

		echo stripslashes(json_encode($items, JSON_UNESCAPED_UNICODE));

	}

	public function save() {

		$model = $this->getModel('comments');
		$model_realty = $this->getModel('realty');
		$config = Registry::get('config');
		$user = User::getUserData();

		$result = array('success' => false, 'message' => '');

		if (isset($_POST['comment']) && !empty($_POST['comment']) && $model->checkAccess($_POST['controller'])) {			

			$data = array();				
			$data['title'] = 'Комментарий к объекту';
			$data['controller'] = $_POST['controller'];
			$data['item_id'] = (int) $_POST['item_id'];
			$data['state'] = 1;
			$data['comment'] = strip_tags($_POST['comment']);
			$data['user_id'] = $user->id;
			$data['name'] = $user->name;
			$data['email'] = '';
			$data['website'] = '';
			$data['create_date'] = date("Y-m-d H:i:s");
			$data['edit_date'] = date("Y-m-d H:i:s");

			$id = $model->SaveNewItem($data);

			if ($id > 0) {				

				if ($data['controller'] == 'realty') {
					
					$realty_item = $model_realty->getItem($data['item_id']);
					
					if (!empty($realty_item->author_email)) {
						
						$to      = $realty_item->author_email;
						$subject = $config->SiteName.' - новый комментарий к вашей записи';
						$message = $user->name . ' оставил новый комментарий к вашей записи на сайте '.$config->SiteName. "\r\n\r\n";
						$message .= 'Для просмотра перейдите по ссылке:'."\r\n";						
						$message .=$config->BaseURL.'/admin/realty/edit/'.$data['item_id'];
						
						mail($to, $subject, $message);

					}

				}

				$result['success'] = true;
				$result['message'] = 'Ваш комментарий успешно сохранен.';
				$result['comment'] = $data['comment'];
				$result['username'] = $data['name'];
				$result['create_date'] = $data['create_date'];

			} else {
				//Main::Redirect('/blog/post/' . $data['item_id'], 'Произошла ошибка при записи комментария. Пожалуйста повторите еще раз.');
				$result['message'] = 'Произошла ошибка при записи комментария. Пожалуйста повторите еще раз.';
			}

		} else {

			//Main::Redirect('/blog/post/' . (int) $_POST['item_id'], 'Не заполнены все обязательные поля.');
			$result['message'] =  'Произошла ошибка при записи комментария. Пожалуйста повторите еще раз.';

		}

		echo stripslashes(json_encode($result, JSON_UNESCAPED_UNICODE));

	}

	public function delete() {

		if (User::getUserAccessName() == 'administrator' || User::getUserAccessName() == 'manager') {

			$result = array('success' => false, 'message' => '');

			$args = Registry::get('route')->args;		

	    	if (isset($args[0])) {

	    		$model = $this->getModel('comments');

		    	$id = (int) $args[0];	    	

		    	if ($model->itemExist($id)) {

			    	if ($model->deleteItem($id)) {
			    		$result['success'] = true;
			    		$result['message'] = 'Элемент успешно удален';
			    	} else {
			    		$result['message'] = 'Не удалось удалить элемент';
			    	}

		    	} else {
		    		$result['message'] = 'Такого элемента не существует';
		    	}
		    }

		    echo stripslashes(json_encode($result, JSON_UNESCAPED_UNICODE));

		}
		
	}


}