<?php
Class SystemComponentsController Extends ControllerBase {

	function index() {		

		$model = $this->getModel('components');	

		$model->initUserOrdering();
		
		$items = $model->getItems();	
		
		$components = $model->findComponents();
		$widgets = $model->findWidgets();
		$themes = $model->findThemes();

		// FINDING UNREGISTRED COMPONENTS
		foreach ($items as $key => $component) {
			
			if (($component->type == 'component') and isset($components[$component->name])) {				
				
				unset($components[$component->name]);

			} elseif (($component->type == 'widget') and isset($widgets[$component->name])) {				
				
				unset($widgets[$component->name]);

			} elseif (($component->type == 'theme') and isset($themes[$component->name])) {				
				
				unset($themes[$component->name]);

			} else {				
				
				$items[$key]->state = -1;
				
			}
		}

		// PAGINATING AND GET ITEMS
		$model->use_pagination = true;

		// FILTER BY NAME
		$filter = new Filter;
		$filter->name = 'name';
		$filter->column = 'c.name';
		$filter->title = 'Название';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// FILTER BY TYPE
		$filter = new Filter;
		$filter->name = 'type';
		$filter->column = 'c.type';
		$filter->title = 'Тип';
		$filter->values = array(
			'component' => 'Компоненты', 
			'theme' => 'Темы', 
			'widget' => 'Виджеты'
		);

		$model->_setFilter($filter);

		// FILTER controllers
		$filter = new Filter;
		$filter->name = 'type';
		$filter->column = 'c.type';
		$filter->operator = '<>';
		$filter->hidden = true;			
		$filter->value = 'controller';	

		$model->_setFilter($filter);	
		
		$pagination = $model->initPagination();
		// GET ITEMS
		$items = $model->getItems();

		// DISPLAY RESULT
		$tmpl = new template;

		$tmpl->setVar('registred', $items);
		$tmpl->setVar('components', $components);		
		$tmpl->setVar('widgets', $widgets);
		$tmpl->setVar('themes', $themes);		
		$tmpl->setVar('pagination', $pagination);		
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Компоненты');

		$tmpl->display('components');
	}

	function register()	{

		if (isset($_POST['type'])) {

			$model = $this->getModel('components');
			$type = $_POST['type'];
			$i = 0;

			if ($type == 'component') {

				foreach ($_POST['components'] as $key => $name) {

					$data = array();
					$data['name'] = $name;
					$data['type'] = $type;
					$data['state'] = 0;
					$data['access'] = User::getAccessId('administrator');
					//$data['params'] = serialize($model->getControllerParams($name));
					$data['register_date'] = date("Y-m-d H:i:s");
					$data['edit_date'] = date("Y-m-d H:i:s");

					$model->SaveNewItem($data);

					$i++;

				}

			} elseif ($type == 'widget') {				

				foreach ($_POST['widgets'] as $key => $name) {

					$data = array();
					$data['name'] = $name;
					$data['type'] = $type;
					$data['state'] = 1;
					$data['access'] = 0;
					$data['params'] = serialize($model->getWidgetParams($name));					
					$data['register_date'] = date("Y-m-d H:i:s");
					$data['edit_date'] = date("Y-m-d H:i:s");

					$model->SaveNewItem($data);
					
					$i++;

				}

			} elseif ($type == 'theme') {				

				foreach ($_POST['themes'] as $key => $name) {

					$data = array();
					$data['name'] = $name;
					$data['type'] = $type;
					$data['state'] = 1;
					$data['access'] = 0;
					$data['params'] = NULL;
					$data['register_date'] = date("Y-m-d H:i:s");
					$data['edit_date'] = date("Y-m-d H:i:s");

					$model->SaveNewItem($data);
					
					$i++;

				}

			}

			Main::redirect('/admin/system/components', 'Зарегестрированно ' . $i . ' компонентов');

		}

	} 

	function unreg() {
		
		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('components');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/system/components', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/system/components', 'Успешно удалено ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/system/components', 'Не указан id элемента');
	   	}

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('components');

			$item = $model->getItem($id);

			if (isset($item->params)) {
				$item->params = unserialize($item->params);
			}

			$users_access = $model->getUsersAccessTree();

			if ($item->type == 'component') {
				$model->updateControllers($item->name);
				$controllers = $model->getControllers($item->name);			
			}

			$tmpl = new template;

			$tmpl->setVar('item', $item);
			$tmpl->setVar('users_access', $users_access);

			if ($item->type == 'component') {
				$tmpl->setVar('controllers', $controllers);
			}

			$tmpl->display('components_edit');

		} else {
			Main::redirect('/admin/system/components', 'Не указан идентификатор компонента');
		}		

	}

	function save() {

		$model = $this->getModel('components');

		$id = (int) $_POST['id'];

		$data = array();
		$data['state'] = (int) $_POST['state'];
		// $data['access'] = (int) $_POST['access'];
		
		if (isset($_POST['params'])) {
			$data['params'] = serialize($_POST['params']);
		} else {
			$data['params'] = serialize(array());
		}

		$data['edit_date'] = date("Y-m-d H:i:s");

		$ctrl_access = $_POST['ctrl_access'];
		$ctrl_state = $_POST['ctrl_state'];

		foreach ($ctrl_access as $key => $access) {
			$ctrl_data = array();
			$ctrl_data['access'] = $access;
			$ctrl_data['state'] = $ctrl_state[$key];
			$model->SaveItem($key, $ctrl_data);
		}

		if ($model->SaveItem($id, $data)) {
			Main::Redirect('/admin/system/components/edit/' . $id, 'Запись успешно сохранена.');
		} else {
			Main::Redirect('/admin/system/components/edit/' . $id, 'Ошибка! Не удалось сохранить запись.');
		}

	}

	function enable() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('components');

		$params = array();
	    $params['state'] = 1;

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	

	    	if ($model->saveItem($id, $params)) {
	    		Main::Redirect('/admin/system/components', 'Элемент успешно опубликован');
	    	}

	   	} elseif (isset($_POST['checked'])) {

	   		foreach ($_POST['checked'] as $key => $value) {
	   			
	   			$id = (int) $value;

	   			if ($model->saveItem($id, $params)) {
	    			$i++;
	    		} else {
	    			Main::setMessage('Не удалось опубликовать элемент id = ' . $id);
	    		}

	   		}

	   		Main::redirect('/admin/system/components', 'Успешно опубликованно ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/system/components', 'Не указан id элемента');
	   	}

	}

	function disable() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('components');

		$params = array();
	    $params['state'] = 0;

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	

	    	if ($model->saveItem($id, $params)) {
	    		Main::Redirect('/admin/system/components', 'Элемент успешно опубликован');
	    	}

	   	} elseif (isset($_POST['checked'])) {

	   		foreach ($_POST['checked'] as $key => $value) {
	   			
	   			$id = (int) $value;

	   			if ($model->saveItem($id, $params)) {
	    			$i++;
	    		} else {
	    			Main::setMessage('Не удалось опубликовать элемент id = ' . $id);
	    		}

	   		}

	   		Main::redirect('/admin/system/components', 'Успешно опубликованно ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/system/components', 'Не указан id элемента');
	   	}

	}

}