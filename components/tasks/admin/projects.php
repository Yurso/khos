<?php
Class TasksProjectsController Extends ControllerBase {

	function index() {

		$p_model = $this->getModel('projects');
		$c_model = $this->getModel('customers');
		// use ordering
		$p_model->initUserOrdering();		

		$user = User::getUserData();

		// FILTERS BY TITLE
		$filter = new Filter;
		$filter->name = 'title';
		$filter->column = 'p.title';
		$filter->title = 'Имя';
		$filter->operator = 'LIKE';
		
		$p_model->_setFilter($filter);

		// FILTERS BY TITLE
		$filter = new Filter;
		$filter->name = 'customer_id';
		$filter->column = 'p.customer_id';
		$filter->title = 'Клиент';
		$filter->setValues($c_model->getItems(), 'id', 'name');	
		
		$p_model->_setFilter($filter);

		// Use pagination 
		$pagination = $p_model->initPagination();

		// Get items array
		$items = $p_model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);		
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $p_model->filters);

		$tmpl->setTitle('Проекты');
		
		$tmpl->display('projects_list');

	}

	function create() {

		$p_model = $this->getModel('projects');
		$c_model = $this->getModel('customers');		
		
		$user = user::getUserData();

		$item = new stdClass;
		$item->id = 0;
		$item->title = '';
		$item->customer_id = '';
		$item->state = 1;
		$item->description = '';
		$item->create_date = '';
		$item->created_by = '';
		$item->modify_date = '';
		$item->modify_by = '';

		$tmpl = new template;

		$tmpl->setVar('item', $item);
		$tmpl->setVar('customers', $c_model->getItems());

		$tmpl->display('projects_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$p_model = $this->getModel('projects');
			$c_model = $this->getModel('customers');

			if ($p_model->itemExist($id)) {

				$item = $p_model->getItem($id);

				$tmpl = new Template;

				$tmpl->setVar('item', $item);
				$tmpl->setVar('customers', $c_model->getItems());				

				$tmpl->display('projects_edit');

			} else {
				Main::redirect('/admin/tasks/projects', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/tasks/projects', 'Не указан id элемента');
		}

	}

	function save() {

		$redirect = '/admin/tasks/projects/';
		$message = 'Ошибка! Не удалось сохранить запись.';

		$model = $this->getModel('projects');		

		$id = (int) $_POST['id'];
		
		$user = User::getUserData();								

		$params = array();

		$columns = $model->getTableColumns();	

		foreach ($columns as $column) {
			if (isset($_POST[$column->Field])) {				
				if (gettype($_POST[$column->Field]) == 'array') {
					$params[$column->Field] = serialize($_POST[$column->Field]);
				} else {
					$params[$column->Field] = trim($_POST[$column->Field]);
				}
			}
		}

		if ($id == 0) {
			$params['created_by'] = $user->id;
		}
		
		$params['modify_date'] = date("Y-m-d H:i:s");
		$params['modify_by'] = $user->id;

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($params);

			if ($id > 0) {
				// Set message
				$message = 'Запись успешно сохранена.';		
			}
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $params)) {			
				// Set message
				$message = 'Запись успешно сохранена.';	
			}

		}

		$redirect = '/admin/tasks/projects/edit/' . $id;

		// if nothing saved
		if ($id == 0) {
			$redirect = '/admin/tasks/projects/create'; 			
		}

		// redirect to items list if user press save 
		if (isset($_POST['save'])) {			
			$redirect = '/admin/tasks/projects/';					
		}

		Main::Redirect($redirect, $message);

	}

	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('projects');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		// Redirect
	    		Main::Redirect('/admin/tasks/projects', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/tasks/projects', 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/tasks/projects', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('projects');

			$user = User::getUserData();

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);				

				$data = array();

				$data['title'] = $item->title;
				$data['customer_id'] = $item->customer_id;
				$data['state'] = $item->state;
				$data['description'] = $item->description;								

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/tasks/projects', 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/admin/tasks/projects', 'Нечего копировать.');
			}

		}

	}

	function autocomplite() {

		$items = array();
		$user = User::getUserData();

		if (isset($_GET['term']) && !empty($_GET['term'])) {

			$model = $this->getModel('projects');

			$name = $_GET['term'];

			$items = $model->getItemsByName($name, $user->id);

		}

		echo json_encode($items, JSON_UNESCAPED_UNICODE);

	}

	function ajaxGetList() {

		if (!isset($_REQUEST['customer_id'])) {
			echo json_encode(array());
			exit;
		}

		$model = $this->getModel('projects');

		$model->setFilter('customer_id', '=', intval($_REQUEST['customer_id']));

		$items = $model->getItems();

		echo json_encode($items, JSON_UNESCAPED_UNICODE);

	}

}