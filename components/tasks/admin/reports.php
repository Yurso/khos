<?php
Class TasksReportsController Extends stdController {

	public function index() {

		$model = $this->getModel('reports');

		$model->initUserOrdering();		

		$user = User::getUserData();

		// FILTERS BY TITLE
		$filter = new Filter;
		$filter->name = 'title';
		$filter->column = 'title';
		$filter->title = 'Заголовок';
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

		$tmpl->setTitle('Отчеты');

		$tmpl->display('reports');

	}

	public function create() {

		$model = $this->getModel('reports');
		
		$user = user::getUserData();

		$item = new stdClass;
		$item->id = 0;
		$item->title = '';
		$item->description = '';
		$item->query = '';
		$item->params = array();
		$item->columns = array();
		$item->author_id = $user->id;
		$item->create_date = '';
		$item->author_id = '';

		$tmpl = new template;

		$tmpl->setVar('item', $item);

		$tmpl->display('reports_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('reports');

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);

				$tmpl = new Template;

				$tmpl->setVar('item', $item);				

				$tmpl->display('reports_edit');

			} else {
				Main::redirect('/admin/tasks/reports', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/tasks/reports', 'Не указан id элемента');
		}

	}

	function save() {

		$redirect = '/admin/tasks/reports/';
		$message = 'Ошибка! Не удалось сохранить запись.';

		$model = $this->getModel('reports');		

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

		// $params['params'] = serialize(
		// 	array(
		// 		array(
		// 			'title' => 'Начало периода',
		// 			'alias' => 'date_start',
		// 			'type' => 'date',
		// 			'default' => ''
		// 		)
		// 	)
		// );

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

		$redirect = '/admin/tasks/reports/edit/' . $id;

		// if nothing saved
		if ($id == 0) {
			$redirect = '/admin/tasks/reports/create'; 			
		}

		// redirect to items list if user press save 
		if (isset($_POST['save'])) {			
			$redirect = '/admin/tasks/reports/';					
		}

		Main::Redirect($redirect, $message);

	}

	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('reports');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		// Redirect
	    		Main::Redirect('/admin/tasks/reports', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/tasks/reports', 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/tasks/reports', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('reports');

			$user = User::getUserData();

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);				

				$params = array();

				foreach ($item AS $field => $value) {
					$params[$field] = $value;
				}

				unset($params['id']);
				$params['author_id'] = $user->id;
				$params['create_date'] = date("Y-m-d H:i:s");
				$params['modify_date'] = date("Y-m-d H:i:s");

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/tasks/reports', 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/admin/tasks/reports', 'Нечего копировать.');
			}

		}

	}

}