<?php
Class RealtyParamsController Extends ControllerBase {

	function index() {

		$model = $this->getModel('params');

		$model->initUserOrdering();	

		$pagination = $model->initPagination();

		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);		
		
		$tmpl->display('params');

	}

	function create() {

		$data = new stdClass;
		$data->id = 0;
		$data->name = '';
		$data->label = '';
		$data->type = '';
		$data->description = '';
		$data->field_width = '';
		$data->state = 1;
		
		$model = $this->getModel('params');

		$template = new template;

		$template->setVar('data', $data);

		$template->display('params_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('params');

			if ($model->itemExist($id)) {

				$data = $model->getItem($id);

				$template = new Template;

				$template->setVar('data', $data);

				$template->display('params_edit');

			} else {
				Main::redirect('/admin/realty/params', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/realty/params', 'Не указан id элемента');
		}

	}

	function save() {

		$redirect = '';
		$message = '';

		$model = $this->getModel('params');

		$id = (int) $_POST['id'];

		$data = array();
		$data['name'] = $_POST['name'];
		$data['label'] = $_POST['label'];
		$data['type'] = $_POST['type'];
		$data['description'] = $_POST['description'];
		$data['field_width'] = $_POST['field_width'];
		$data['state'] = $_POST['state'];
		
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

		$redirect = '/admin/realty/params/edit/' . $id;

		if ($id == 0)
			$redirect = '/admin/realty/params/create'; 			

		// redirect to items list if user press save 
		if (isset($_POST['save'])) 
			$redirect = '/admin/realty/params/';		

		Main::Redirect($redirect, $message);

	}

	
	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('params');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/realty/params', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/realty/params', 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/realty/params', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('params');

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);

				$data = array();
				$data['name'] = $item->name;
				$data['label'] = $item->label;
				$data['type'] = $item->type;
				$data['description'] = $item->description;
				$data['field_width'] = $item->logo;
				$data['state'] = $item->state;

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/realty/params', 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/admin/realty/params', 'Нечего копировать.');
			}

		}

	}

}