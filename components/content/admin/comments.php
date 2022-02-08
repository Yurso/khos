<?php
Class ContentCommentsController Extends ControllerBase {

	function index() {
		
		// get model
		$model = $this->getModel('comments');

		$model->initUserOrdering();

		// set title filter
		$model->addFilter('title', 'Заголовок');

		// set controller filter
		$filter_values = array();
		$controllers = $model->getActiveControllers();

		foreach ($controllers as $controller) {
			$filter_values[$controller->name] = $controller->name;
		}

		$model->addFilter('controller', 'Контроллер', $filter_values);

		$pagination = $model->initPagination();

		// get items list
		$items = $model->getItems();

		// set templates var and display result
		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->getFilters());

		$tmpl->display('comments');
	}

	function create() {

		$model = $this->getModel('comments');

		$item = new stdClass;
		$item->id = 0;
		$item->title = '';
		$item->controller = '';
		$item->item_id = 1;
		$item->state = 1;
		$item->comment = '';
		$item->user_id = 0;
		$item->name = '';
		$item->email = '';
		$item->website = '';
		$item->create_date = date("Y-m-d H:i:s");
		$item->edit_date = date("Y-m-d H:i:s");

		$controllers = $model->getActiveControllers();		

		$template = new template;

		$template->setVar('item', $item);
		$template->setVar('controllers', $controllers);

		$template->display('comments_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('comments');

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);

				$controllers = $model->getActiveControllers();

				$template = new Template;

				$template->setVar('item', $item);
				$template->setVar('controllers', $controllers);

				$template->display('comments_edit');

			} else {
				Main::redirect('/admin/content/comments', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/content/comments', 'Не указан id элемента');
		}

	}

	function save() {

		$model = $this->getModel('comments');

		$id = (int) $_POST['id'];

		$user = new User;
		$userData = $user->getUserData();

		$data = array();

		$data['title']			= $_POST['title'];
		if ($id == 0)
			$data['controller']		= $_POST['controller'];
		$data['item_id']			= $_POST['item_id'];
		$data['state']			= $_POST['state'];
		$data['comment']		= $_POST['comment'];		
		if ($id == 0)
			$data['user_id']	= $userData->id;
		$data['name']			= $_POST['name'];
		$data['email']			= $_POST['email'];
		$data['website']		= $_POST['website'];
		$data['create_date'] = (!empty($_POST['create_date'])) ? $_POST['create_date'] : date("Y-m-d H:i:s");
		$data['edit_date'] = (!empty($_POST['edit_date'])) ? $_POST['edit_date'] : date("Y-m-d H:i:s");

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($data);

			if ($id > 0) {
				Main::Redirect('/admin/content/comments/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/content/comments', 'Ошибка! Не удалось сохранить запись.');
			}
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $data)) {
				Main::Redirect('/admin/content/comments/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/content/comments/edit/' . $id, 'Ошибка! Не удалось сохранить запись.');
			}

		}

	}

	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('comments');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/content/comments', 'Элемент успешно удален');
	    	}

	   	} elseif (isset($_POST['checked'])) {

	   		foreach ($_POST['checked'] as $key => $value) {
	   			
	   			$id = (int) $value;

	   			if ($model->deleteItem($id)) {
	    			$i++;
	    		} else {
	    			Main::setMessage('Не удалось удалить элемент id = ' . $id);
	    		}

	   		}

	   		Main::redirect('/admin/content/comments', 'Успешно удалено ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/content/comments', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('comments');

			$user = new User;
			$user_data = $user->getUserData();

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);

				$data = array();
				$data['title'] = $item->title . ' (copy)';				
				$data['controller'] = $item->controller;
				$data['item_id'] = $item->item_id;
				$data['state'] = $item->state;				
				$data['comment'] = $item->comment;
				$data['user_id'] = $user_data->id;
				$data['name'] = $item->name;
				$data['email'] = $item->email;				
				$data['website'] = $item->website;
				$data['create_date'] = date("Y-m-d H:i:s");
				$data['edit_date'] = date("Y-m-d H:i:s");				

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/content/comments', 'Успешно скопировано ' . $i . ' элементов.');
			} else {
				Main::Redirect('/admin/content/comments', 'Нечего копировать.');
			}

		} else {
			Main::Redirect('/admin/content/comments', 'Нечего копировать.');
		}

	}

}