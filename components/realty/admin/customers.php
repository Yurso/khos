<?php
Class RealtyCustomersController Extends ControllerBase {

	function index() {

		$model = $this->getModel('customers');	

		$model->initUserOrdering();		

		$user = User::getUserData();

		// FILTERS BY TITLE
		$filter = new Filter;
		$filter->name = 'name';
		$filter->column = 'name';
		$filter->title = 'Имя';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// FILTERS BY TYPE
		$filter = new Filter;
		$filter->name = 'type';
		$filter->column = 'type';
		$filter->title = 'Тип';		
		$filter->operator = '=';
		$filter->values = array(
			'seller' => 'Продавец',
			'buyer'  => 'Покупатель'
		);		
		
		$model->_setFilter($filter);

		// FILTERS BY USER
		$filter = new Filter;
		$filter->name = 'user';
		$filter->column = 'user_id';
		$filter->title = 'Пользователь';
		$filter->operator = '=';
		$filter->hidden = true;	
		$filter->first_empty_value = false;
		$filter->value = $user->id;	
		
		$model->_setFilter($filter);

		$pagination = $model->initPagination();
		// Get items array
		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);		
		$tmpl->setVar('pagination', $model->pagination);
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
		$item->type = 'seller';

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
					Main::Redirect('/admin/realty/customers', 'У вас нет доступа к записи.');
				}

				$item = $model->getItem($id);

				$tmpl = new Template;

				$tmpl->setVar('item', $item);

				$tmpl->display('customers_edit');

			} else {
				Main::redirect('/admin/realty/customers', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/realty/customers', 'Не указан id элемента');
		}

	}

	function save() {

		//print_r($_POST);
		//exit();

		$redirect = '/admin/realty/customers/';
		$message = 'Ошибка! Не удалось сохранить запись.';

		$model = $this->getModel('customers');

		$id = (int) $_POST['id'];
		
		$user = User::getUserData();								

		$data = array();

		$data['name'] = $_POST['name'];
		$data['adress'] = $_POST['adress'];		
		$data['phone'] = $_POST['phone'];
		$data['email'] = $_POST['email'];
		$data['birthday'] = $_POST['birthday'];

		if (isset($_POST['type']) && $_POST['type'] == 'buyer') {
			$data['type'] = 'buyer';	
		} else {
			$data['type'] = 'seller';
		}
		

		// if ($id == 0) {
		// 	$data['user_id'] = $user->id;	
		// 	$data['create_date'] = date("Y-m-d H:i:s");
		// }		 

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

		$redirect = '/admin/realty/customers/edit/' . $id;

		// if nothing saved
		if ($id == 0) {
			$redirect = '/admin/realty/customers/create'; 			
		}

		// redirect to items list if user press save 
		if (isset($_POST['save'])) {			
			$redirect = '/admin/realty/customers/';					
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
	    		Main::Redirect('/admin/realty/customers', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/realty/customers', 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/realty/customers', 'Не указан id элемента');
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
				$data['type'] = $item->type;

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/realty/customers', 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/admin/realty/customers', 'Нечего копировать.');
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