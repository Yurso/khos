<?php
Class SystemAliasesController Extends ControllerBase {

	function index() {

		$model = $this->getModel('aliases');

		$model->initUserOrdering();

		// FILTERS ADRESS
		$filter = new Filter;
		$filter->name = 'alias';
		$filter->column = 'alias';
		$filter->title = 'Алиас';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// FILTERS ADRESS
		$filter = new Filter;
		$filter->name = 'url';
		$filter->column = 'url';
		$filter->title = 'Ссылка';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		$pagination = $model->initPagination();
		
		$items = $model->getItems();

		$tmpl = new Template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Алиасы');

		$tmpl->display('aliases');

	}

	private function _buildButtonsArray() {

		$buttons = array();		
		
		$buttons[] = array(
			'title' => 'Сохранить',
			'name' => 'save',
			'action' => 'save'
		);
		$buttons[] = array(
			'title' => 'Применить',
			'name' => 'apply',
			'action' => 'save'
		);
		$buttons[] = array(
			'title' => 'Закрыть',
			'name' => 'close',
			'action' => 'close'
		);

		return $buttons;

	}

	function create() {

		$model = $this->getModel('aliases');

		$widget = new stdClass;
		$widget->id = 0;
		$widget->alias = '';
		$widget->url = '';

		$template = new template;

		$template->display('aliases_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$model = $this->getModel('aliases');

			$id = (int) $args[0];

			$item = $model->getItem($id);	

			$template = new template;

			$template->setVar('item', $item);

			$template->display('aliases_edit');

		} else {
			Main::redirect('/admin/system/aliases', 'Ошибка! Не указан идентификатор виджета');
		}


	}

	function save() {

		$model = $this->getModel('aliases');

		$id = (int) $_POST['id'];

		$params = array();
		
		$params['alias'] = trim($_POST['alias']);
		$params['url'] = trim($_POST['url']);

		$succes = false;
		# If it's new element
		if ($id == 0) {
			$id = $model->SaveNewItem($params);
			if ($id > 0) {
				$succes = true;
			}			
		} elseif ($id > 0) {
			if ($model->SaveItem($id, $params)) {
				$succes = true;
			}
		}

		// Set redirect values
		if ($succes) {
			$message = 'Запись успешно сохранена.';
			$redirect = '/admin/system/aliases/edit/' . $id;
		} elseif ($id > 0) {
			$message = 'Произошла ошибка при записи. Данные не сохранены.';
			$redirect = '/admin/system/aliases/edit'.$id; 			
		} else {
			$message = 'Произошла ошибка при записи. Данные не сохранены.';
			$redirect = '/admin/system/aliases/create'; 
		}

		// redirect to items list if user press save 
		if (isset($_POST['save'])) {
			$redirect = '/admin/system/aliases';		
		}

		Main::Redirect($redirect, $message);

	}

    function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('aliases');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/system/aliases', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/system/aliases', 'Успешно удалено ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/system/aliases', 'Не указан id элемента');
	   	}
		
	}

}