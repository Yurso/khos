<?php
Class stdController extends ControllerBase {

	public function index() {

		$route = Registry::get('route');

		$model = $this->getModel($route->controller);		

		$model->initUserOrdering();

		// Check columns and set Filters
		$columns = $model->getTableColumns();
		
		foreach ($columns as $key => $column) {

			$field = $column->Field;

			if (strpos($column->Type, 'varchar')) {
			
				$filter = new Filter;
				$filter->name = $field;
				$filter->column = $field;
				$filter->title = $field;
				$filter->operator = 'LIKE';
				
				$model->_setFilter($filter);

			}
			
		}

		// Use pagination
		$pagination = $model->initPagination();		
		// Get items array
		$items = $model->getItems();

		// Set template parameters
		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle($route->controller);
		
		$tmpl->display($route->controller);

	}

	public function create() {

		$route = Registry::get('route');

		$model = $this->getModel($route->controller);
		
		$columns = $model->getTableColumns();

		$item = new stdClass;

		foreach ($columns as $key => $column) {

			$field = $column->Field;
			
			if (strpos($column->Type, 'int(')) {
				$item->$field = 0;	
			} else {
				$item->$field = '';					
			}
			
		}		

		$tmpl = new template;
		$tmpl->setVar('item', $item);
		$tmpl->display($route->controller.'_edit');

	}

	public function edit() {

		$route = Registry::get('route');

		$args = $route->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel($route->controller);

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);

				$tmpl = new Template;
				$tmpl->setVar('item', $item);				
				$tmpl->display($route->controller.'_edit');

			} else {
				Main::redirect('/'.$route->subpath.$route->component.'/'.$route->controller, 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/'.$route->subpath.$route->component.'/'.$route->controller, 'Не указан id элемента');
		}

	}

	public function save() {

		$redirect = '';
		$message = '';

		$route = Registry::get('route');

		$model = $this->getModel($route->controller);

		$id = (int) $_POST['id'];

		$columns = $model->getTableColumns();

		$params = array();

		foreach ($columns as $key => $column) {

			$field = $column->Field;
			
			$params[$field] = $_POST[$field];
			
		}

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($params);

			if ($id > 0) 
				$message = 'Запись успешно сохранена.';				
			else
				$message = 'Ошибка! Произошла ошибка базы даных. Не удалось сохранить запись.';			
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $params)) 				
				$message = 'Запись успешно сохранена.';	
			else				
				$message = 'Ошибка! Не удалось сохранить запись.';			

		}

		$redirect = '/'.$route->subpath.$route->component.'/'.$route->controller.'/edit/'.$id;

		// if id still 0 go to create form
		if ($id == 0) { $redirect = '/'.$route->subpath.$route->component.'/'.$route->controller.'create'; }

		// redirect to items list if user press save 
		if (isset($_POST['save'])) { $redirect = '/'.$route->subpath.$route->component.'/'.$route->controller; }

		Main::Redirect($redirect, $message);

	}

	function delete() {

		$i = 0;

		$route = Registry::get('route');

		$model = $this->getModel($route->controller);

    	if (isset($route->args[0])) {

	    	$id = (int) $route->args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/'.$route->subpath.$route->component.'/'.$route->controller, 'Элемент успешно удален');
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

	   		Main::redirect('/'.$route->subpath.$route->component.'/'.$route->controller, 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/'.$route->subpath.$route->component.'/'.$route->controller, 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$route = Registry::get('route');

			$model = $this->getModel($route->controller);

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);

				$columns = $model->getTableColumns();

				$params = array();

				foreach ($columns as $key => $column) {

					$field = $column->Field;

					if ($column->Extra == 'auto_increment') continue;
					
					$params[$field] = $item->$field;
					
				}

				if ($model->SaveNewItem($params)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/'.$route->subpath.$route->component.'/'.$route->controller, 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/'.$route->subpath.$route->component.'/'.$route->controller, 'Нечего копировать.');
			}

		}

	}

}