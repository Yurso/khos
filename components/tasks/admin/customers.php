<?php
Class TasksCustomersController Extends ControllerBase {

	function index() {

		$model = $this->getModel('customers');
		// use ordering
		$model->initUserOrdering();		

		$user = User::getUserData();

		// FILTERS BY TITLE
		$filter = new Filter;
		$filter->name = 'name';
		$filter->column = 'name';
		$filter->title = 'Имя';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// Use pagination 
		$pagination = $model->initPagination();

		// Get items array
		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);		
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Клиенты');
		
		$tmpl->display('customers');

	}

	function create() {

		$model = $this->getModel('customers');
		
		$user = user::getUserData();

		$item = new stdClass;
		$item->id = 0;
		$item->name = '';
		$item->adress = '';
		$item->phone = '';
		$item->email = '';
		$item->group_name = '';
		$item->emails = array();

		$tmpl = new template;

		$tmpl->setVar('item', $item);

		$tmpl->display('customers_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('customers');

			if ($model->itemExist($id)) {

				if (!$model->checkItemAccess($id)) {
					Main::Redirect('/admin/tasks/customers', 'У вас нет доступа к записи.');
				}

				$item = $model->getItem($id);

				$email_model = $this->getModel('emails');
				$email_model->setFilter('customer_id', '=', $item->id);
				$item->emails = $email_model->getItems();

				$projects_model = $this->getModel('projects');
				$projects_model->setFilter('customer_id', '=', $item->id);
				$item->projects = $projects_model->getItems();

				$tmpl = new Template;

				$tmpl->setVar('item', $item);				

				$tmpl->display('customers_edit');

			} else {
				Main::redirect('/admin/tasks/customers', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/tasks/customers', 'Не указан id элемента');
		}

	}

	function save() {

		$redirect = '/admin/tasks/customers/';
		$message = 'Ошибка! Не удалось сохранить запись.';

		$model = $this->getModel('customers');		

		$id = (int) $_POST['id'];
		
		$user = User::getUserData();								

		$data = array();

		$data['name'] = $_POST['name'];
		$data['adress'] = $_POST['adress'];		
		$data['phone'] = $_POST['phone'];
		$data['email'] = $_POST['email'];		
		$data['group_name'] = $_POST['group_name'];			 

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($data);

			if ($id > 0) {
				// Set message
				$message = 'Запись успешно сохранена.';		
			}
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $data)) {			
				// Set message
				$message = 'Запись успешно сохранена.';	
			}

		}

		if ($id > 0) {
			$model->SaveCustomerEmails($id, $_POST['emails']);
		}

		$redirect = '/admin/tasks/customers/edit/' . $id;

		// if nothing saved
		if ($id == 0) {
			$redirect = '/admin/tasks/customers/create'; 			
		}

		// redirect to items list if user press save 
		if (isset($_POST['save'])) {			
			$redirect = '/admin/tasks/customers/';					
		}

		Main::Redirect($redirect, $message);

	}

	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('customers');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		// Redirect
	    		Main::Redirect('/admin/tasks/customers', 'Элемент успешно удален');
	    	}

	   	} elseif (isset($_POST['checked'])) {

	   		foreach ($_POST['checked'] as $key => $value) {
	   			
	   			$id = (int) $value;

	   			if ($model->deleteItem($id)) {
	    			// Count deleted records	    			
	    			$i++;	
	    		} else {
	    			Main::setMessage('Не удалось удалить элемент id = ' . $id);
	    		}

	   		}

	   		Main::redirect('/admin/tasks/customers', 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/tasks/customers', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('customers');

			$user = User::getUserData();

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);				

				$data = array();

				$data['name'] = $item->name;
				$data['adress'] = $item->adress;
				$data['phone'] = $item->phone;
				$data['email'] = $item->email;				
				$data['group_name'] = $item->group_name;

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/tasks/customers', 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/admin/tasks/customers', 'Нечего копировать.');
			}

		}

	}

	function autocomplite() {

		$items = array();
		$user = User::getUserData();

		if (isset($_GET['term']) && !empty($_GET['term'])) {

			$model = $this->getModel('customers');

			$name = $_GET['term'];

			$items = $model->getItemsByName($name, $user->id);

		}

		echo stripslashes(json_encode($items, JSON_UNESCAPED_UNICODE));

	}

}