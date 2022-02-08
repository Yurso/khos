<?php
Class RealtyParamsValuesController Extends ControllerBase {

	function index() {

		$model = $this->getModel('params_values');
		
		$model->initUserOrdering();	

		$model->addFilter('title', 'Заголовок');
		
		$params = $model->getParamsList();		
		$filter_values = array();
		foreach ($params as $param) {
			$filter_values[$param->id] = $param->label.' ('.$param->name.')';
		}
		$model->addFilter('param_id', 'Параметр', $filter_values, key($filter_values), false);		

		$pagination = $model->initPagination();

		$items = $model->getItems();

		// $pathway = new Pathway;
		// $pathway->addItem('Недвижимость', '/admin/realty');		
		// $pathway->addItem('Значения параметров', '');

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);		
		$tmpl->setVar('filters', $model->filters);
		
		$tmpl->display('params_values');

	}

	function create() {

		$filters = Main::getState('filters', array('param_id' => 0));

		$data = new stdClass;
		$data->id = 0;
		$data->param_id = $filters['param_id'];
		$data->value = '';
		$data->title = '';
		$data->ordering = 0;
		$data->state = 1;

		$model = $this->getModel('params_values');

		$params = $model->getParamsList();

		$template = new template;

		$template->setVar('data', $data);
		$template->setVar('params', $params);

		$template->display('params_values_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('params_values');

			if ($model->itemExist($id)) {

				$data = $model->getItem($id);

				$params = $model->getParamsList();

				$template = new Template;

				$template->setVar('data', $data);				
				$template->setVar('params', $params);

				$template->display('params_values_edit');

			} else {
				Main::redirect('/admin/realty/params_values', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/realty/params_values', 'Не указан id элемента');
		}

	}

	function save() {

		$redirect = '';
		$message = '';

		$model = $this->getModel('params_values');

		$id = (int) $_POST['id'];

		$data = array();
		$data['param_id'] = $_POST['param_id'];
		$data['value'] = $_POST['value'];
		$data['title'] = $_POST['title'];
		$data['state'] = $_POST['state'];
		$data['ordering'] = $_POST['ordering'];

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($data);

			if ($id > 0) 
				$message = 'Запись успешно сохранена.';				
			else
				$message = 'Ошибка! Произошла ошибка базы даных. Не удалось сохранить запись.';			
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $data)) 				
				$message = 'Запись успешно сохранена.';	
			else				
				$message = 'Ошибка! Не удалось сохранить запись.';			

		}

		$redirect = '/admin/realty/params_values/edit/' . $id;

		if ($id == 0)
			$redirect = '/admin/realty/params_values/create'; 			

		// redirect to items list if user press save 
		if (isset($_POST['save'])) 
			$redirect = '/admin/realty/params_values/';		

		Main::Redirect($redirect, $message);

	}

	
	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('params_values');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/realty/params_values', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/realty/params_values', 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/realty/params_values', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('params_values');

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);

				$data = array();
				$data['param_id'] = $item->param_id;
				$data['value'] = $item->value;
				$data['title'] = $item->title;
				$data['state'] = $item->state;
				$data['ordering'] = $item->ordering + 1;

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/realty/params_values', 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/admin/realty/params_values', 'Нечего копировать.');
			}

		}

	}

}