<?php
Class RealtyRequestsController Extends ControllerBase {

	function index() {

		$model = $this->getModel('requests');
		
		$model->initUserOrdering();	

		$user = User::getUserData();

		// FILTERS BY TITLE
		$filter = new Filter;
		$filter->name = 'title';
		$filter->column = 'title';
		$filter->title = 'Заголовок';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// FILTERS BY CUSTOMER
		$filter = new Filter;
		$filter->name = 'customer_name';
		$filter->column = 'IFNULL(c.name,\'\')';
		$filter->title = 'Клиент';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// FILTER BY USER
		$filter = new Filter;
		$filter->name = 'user';
		$filter->column = 'r.user_id';
		$filter->title = 'Показывать';
		$filter->values = array($user->id => 'Только мои', 0 => 'Все');			
		$filter->first_empty_value = false;	
		$filter->setDefault($user->id);		

		$model->_setFilter($filter);

		// FILTERS archive
		$filter = new Filter;
		$filter->name = 'archive';
		$filter->column = 'r.archive';
		$filter->type = 'int';
		$filter->operator = '<';
		$filter->title = 'Показывать архивные';	
		$filter->hidden = false;
		$filter->first_empty_value = false;			
		$filter->values = array(1 => 'Нет', 2 => 'Да');		

		$model->_setFilter($filter);

		$pagination = $model->initPagination();
		// Get items array
		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);		
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);
		$tmpl->setVar('user', User::getUserData());	
		$tmpl->setVar('params', $model->getParams());	

		$tmpl->setTitle('Заявки');
		
		$tmpl->display('requests');

	}

	function create() {

		$model = $this->getModel('objects');
		$user = user::getUserData();
		$categories = $model->getCategoriesList(false, 'realty');
		$params = $model->getParams();

		$item = new stdClass;
		$item->id = 0;
		$item->title = '';
		$item->description = '';
		$item->create_date = date("Y-m-d H:i:s");
		$item->edit_date = date("Y-m-d H:i:s");
		$item->user_name = $user->name;
		$item->priority = '2';
		$item->customer_id = 0;
		$item->customer_name = '';
		$item->customer_adress = '';
		$item->customer_phone = '';
		$item->customer_email = '';
		$item->params['floor'] = $params['floor'];
		$item->params['show'] = array(0,1,2,3);

		$tmpl = new template;

		$tmpl->setVar('item', $item);
		$tmpl->setVar('categories', $categories);
		$tmpl->setVar('params', $params);

		$tmpl->display('requests_edit');

	}

	function view() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('requests');

			if ($model->itemExist($id)) {
				// get request information
				$item = $model->getItem($id);
				// get request object search params
				$item->params = $model->getReqParams($id);
				// get item access
				$item->access = $model->checkItemAccess($id);
				// get full objects list by request params
				$objects = $model->getObjectsListByParams($id, $item->params);				

				// clear new objects count if user have access
				if ($item->access) {
					$data = array();
					$data['new_objects_count'] = 0;
					$model->SaveItem($id, $data);
				}

				// Show template
				$tmpl = new Template;

				$tmpl->setVar('item', $item);
				$tmpl->setVar('categories', $model->getCategoriesList(false, 'realty'));
				$tmpl->setVar('params', $model->getParams());				
				$tmpl->setVar('objects', $objects);								
				$tmpl->setVar('config', Registry::get('config'));
				$tmpl->setVar('selected_count', $model->countSelectedObjects($id));

				$tmpl->display('requests_view');

			} else {
				Main::redirect('/admin/realty/requests', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/realty/requests', 'Не указан id элемента');
		}

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('requests');

			if ($model->itemExist($id)) {

				if (!$model->checkItemAccess($id)) {
					Main::Redirect('/admin/realty/requests/view/'.$id, 'У вас нет доступа для изменений в этой записи.');
				}

				$item = $model->getItem($id);

				$item->params = $model->getReqParams($id);

				$categories = $model->getCategoriesList(false, 'realty');

				//$objects = $model->getRequsetObjects($id);

				$tmpl = new Template;

				$tmpl->setVar('item', $item);
				$tmpl->setVar('categories', $categories);
				$tmpl->setVar('params', $model->getParams());
				$tmpl->setVar('access', $model->checkItemAccess($id));				
				//$tmpl->setVar('objects', $objects);

				$tmpl->display('requests_edit');

			} else {
				Main::redirect('/admin/realty/requests', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/realty/requests', 'Не указан id элемента');
		}

	}

	function save() {

		//print_r($_POST);
		//exit();

		$redirect = '';
		$message = '';

		$model = $this->getModel('requests');

		$id = (int) $_POST['id'];

		if (!$model->checkItemAccess($id)) {
			Main::Redirect('/admin/realty/requests/view/'.$id, 'У вас нет доступа для изменений в этой записи.');
		}
		
		$user = User::getUserData();								

		$data = array();

		$data['title'] = $_POST['title'];		
		$data['description'] = $_POST['description'];
		$data['edit_date'] = date("Y-m-d H:i:s");
		$data['priority'] = $_POST['priority'];		
		$data['customer_id'] = $_POST['customer_id'];

		if ($id == 0) {
			$data['user_id'] = $user->id;	
			$data['create_date'] = date("Y-m-d H:i:s");
			$data['archive'] = "-1";
		}

		// Save customer data to customers
		if ($_POST['customer_flag'] == 'new' || $_POST['customer_flag'] == 'edit') {

			if (!empty($_POST['customer_name'])) {

				$customers_model = $this->getModel('customers');

				$customer_data = array();
				$customer_data['name'] = $_POST['customer_name'];
				$customer_data['adress'] = $_POST['customer_adress'];
				$customer_data['phone'] = $_POST['customer_phone'];
				$customer_data['email'] = $_POST['customer_email'];
				$customer_data['user_id'] = $user->id;

				if ($_POST['customer_flag'] == 'new') {				
					$data['customer_id'] = intval($customers_model->SaveNewItem($customer_data));
				} elseif (!empty($_POST['customer_id'])) {
					$customer_id = intval($_POST['customer_id']);
					$customers_model->SaveItem($customer_id, $customer_data);
				}

			} else {
				Main::setMessage('Не удалось сохранить данные клиента. Имя не может быть пустым.');
			}

		}

		$message = 'Ошибка! Не удалось сохранить запись.';			 

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($data);

			if ($id > 0) {
				// Set message
				$message = 'Запись успешно сохранена.';
				// Saving request parameters
				$model->saveReqParams($id, $_POST['params']);				
			}
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $data)) {			
				// Set message
				$message = 'Запись успешно сохранена.';
				// Saving request parameters
				$model->saveReqParams($id, $_POST['params']);	
			}

		}

		$redirect = '/admin/realty/requests/edit/' . $id;

		// if nothing saved
		if ($id == 0) {
			$redirect = '/admin/realty/requests/create'; 			
		}

		// redirect to items list if user press save 
		if (isset($_POST['save'])) {			
			$redirect = '/admin/realty/requests/view/'.$id;					
		}

		Main::Redirect($redirect, $message);

	}

	function save_objects() {

		//print_r($_POST);

		$model = $this->getModel('requests_objects');

		$request_id = intval($_POST['id']);

		if (!$model->checkItemAccess($request_id)) {
			Main::Redirect('/admin/realty/requests/view/'.$request_id, 'У вас нет доступа для изменений в этой записи.');
		}

		// Clear all old information
		$model->clearRequestObjects($request_id);

		// Saving new objects data
		foreach ($_POST['objects'] as $value) {
			
			$data = array();
			$data['request_id'] = $request_id;
			$data['object_id'] = intval($value);
			if (isset($_POST['checked'])) {
				if (in_array($value, $_POST['checked'])) {
					$data['selected'] = 1;
				} else {
					$data['selected'] = -1;
				}
			}
			$data['select_date'] = date("Y-m-d H:i:s");

			$model->SaveNewItem($data, false);

		}

		Main::Redirect('/admin/realty/requests/view/'.$request_id, 'Данные успешно сохранены.');

	}

	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('requests');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];

	    	if (!$model->checkItemAccess($id)) {
				Main::Redirect('/admin/realty/requests/', 'У вас нет доступа для изменений в этой записи.');
			}	    	

	    	if ($model->deleteItem($id)) {
	    		// Clear all parameters records
	    		$model->saveReqParams($id, array());
	    		// Redirect
	    		Main::Redirect('/admin/realty/requests', 'Элемент успешно удален');
	    	}

	   	} elseif (isset($_POST['checked'])) {

	   		foreach ($_POST['checked'] as $key => $value) {
	   			
	   			$id = (int) $value;

	   			if ($model->checkItemAccess($id)) {

		   			if ($model->deleteItem($id)) {
		    			// Clear all parameters records
		    			$model->saveReqParams($id, array());
		    			// Count deleted records	    			
		    			$i++;	
		    		} else {
		    			Main::setMessage('Не удалось удалить элемент id = ' . $id);
		    		}

		    	} else {
		    		Main::setMessage('У вас нет доступа к элементу id = ' . $id);
		    	}

	   		}

	   		Main::redirect('/admin/realty/requests', 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/realty/requests', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('requests');

			$user = User::getUserData();

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);				

				$data = array();

				$data['title'] = $item->title;
				$data['description'] = $item->description;
				$data['create_date'] = date("Y-m-d H:i:s");	
				$data['last_edit'] = date("Y-m-d H:i:s");
				$data['user_id'] = $user->id;

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/realty/requests', 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/admin/realty/requests', 'Нечего копировать.');
			}

		}

	}

	// function object() {

	// 	$matchings = array();

	// 	$matchings['category_id'] = array(
	// 		'column_name' => 'category_id',
	// 		'operator' => '='			
	// 	);
		
	// 	$matchings['price_from'] = array(
	// 		'column_name' => 'price',
	// 		'operator' => '>='			
	// 	);

	// 	$matchings['price_to'] = array(
	// 		'column_name' => 'price',
	// 		'operator' => '<='			
	// 	);

	// 	$matchings['area_from'] = array(
	// 		'column_name' => 'total_area',
	// 		'operator' => '>='		
	// 	);

	// 	$matchings['area_to'] = array(
	// 		'column_name' => 'total_area',
	// 		'operator' => '<='			
	// 	);
		
	// 	$params = $_POST['params'];

	// 	$model = $this->getModel('requests');

	// 	$items = $model->getObjectsListByParams($params, $matchings);

	// 	echo stripslashes(json_encode($items, JSON_UNESCAPED_UNICODE));

	// }

	// function select_object() {

	// 	$result = array('success' => false, 'message' => 'Не удалось добавить объект');

	// 	if (isset($_POST['request_id']) && isset($_POST['object_id'])) {

	// 		$data = array();
	// 		$data['request_id'] = intval($_POST['request_id']);
	// 		$data['object_id'] = intval($_POST['object_id']);
	// 		$data['select_date'] = date("Y-m-d H:i:s");

	// 		$model = $this->getModel('realty_requests_objects');

	// 		try {
	// 			if ($model->SaveNewItem($data, false)) {
	// 				$result['success'] = true;
	// 				$result['message'] = 'Объект успешно добавлен для показа';
	// 			}
	// 		} catch (Exception $e) {
	// 			$result['success'] = false;
	// 			$result['message'] = $e->getMessage();
	// 		}

	// 	}

	// 	echo stripslashes(json_encode($result, JSON_UNESCAPED_UNICODE)); 

	// }

	function archivate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('requests');			

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;				
				 
				if ($model->checkItemAccess($id)) {

					$params = array();

					$params['archive'] = 1;

					if ($model->SaveItem($id, $params)) {
						$i++;
					}

				} else {

					Main::setMessage('Нет доступа к объекту id='.$id);

				}	

			}
				
			Main::Redirect('/admin/realty/requests', 'Помещено в архив ' . $i . ' ' . Main::declension_by_number('объект', $i));			

		} else {
	   		Main::Redirect('/admin/realty/requests', 'Вы не выбрали ни одного объекта для архивации');
	   	}

	}

	function printlist() {

		if (isset($_POST['checked']) && count($_POST['checked'])) {			

			$model = $this->getModel('objects');						

			$id_list = $_POST['checked'];

			$items = $model->getItemsByList($id_list);			
			$params = $model->getParams();

			$agent = $model->getAgentInfo(user::getUserData('id'));

			// Get information about images and icject it to items
			$images_model = $this->getModel('images');	
			foreach ($items as $key => $item) {				
				$item->images = $images_model->getItems(array('object_id' => $item->id));
			}

			Main::setState('ordering',  null);

			$tmpl = new Template;

			$tmpl->setVar('items', $items);
			$tmpl->setVar('params', $params);
			$tmpl->setVar('agent', $agent);
			$tmpl->setVar('config', registry::get('config'));

			$tmpl->display('objects_printlist', 'print');

		} else {
	   		Main::Redirect('/admin/realty', 'Вы не выбрали ни одного объекта для пачети');
	   	}

	}

}