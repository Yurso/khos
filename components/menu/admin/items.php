<?php
Class MenuItemsController Extends ControllerBase {

	function index() {		
		
		$model = $this->getModel('items');

		$menus = $model->getMenusList();

		$model->initUserOrdering();

		// FILTERS title
		$filter = new Filter;
		$filter->name = 'title';
		$filter->column = 'items.title';
		$filter->title = 'Заголовок';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);		

		// FILTERS menu_id
		$filter = new Filter;
		$filter->name = 'menu_id';
		$filter->column = 'items.menu_id';
		$filter->title = 'Меню';		
		$filter->first_empty_value = false;
		$filter->value = $menus[0]->id;
		$filter->setValues($menus, 'id', 'name');		

		$model->_setFilter($filter);

		// FILTERS menu_id
		$filter = new Filter;
		$filter->name = 'component';
		$filter->column = 'items.component';
		$filter->title = 'Компонент';
		$filter->setValues($model->getActiveComponents(), 'name', 'name');		

		$model->_setFilter($filter);

		// FILTERS CATEGORY
        $filter = new Filter;
        $filter->name = 'access_id';
        $filter->column = 'items.access_id';
        $filter->title = 'Доступ';
        $filter->setValues($model->getUsersAccessList(), 'id', 'name'); 

        $model->_setFilter($filter);

		// $filter_values = array();		
		// foreach ($menus as $value) {
		// 	$filter_values[$value->id] = $value->name;
		// }
		// $model->addFilter('items.menu_id', 'Меню', $filter_values, $menus[0]->id, false);

		$items = $model->getItems();

		$tmpl = new Template;
		
		$tmpl->setVar('items', $items);
		$tmpl->setVar('filters', $model->filters);		

		$tmpl->display('items');

	}

	function create() {

		$state_filters = Main::getState('filters', array());
		$model = $this->getModel('items');
		$users_access = $model->getUsersAccessTree();

		$item = new stdClass;
		$item->id = 0;
		$item->title = '';		
		$item->parent_id = 0;
		$item->description = '';
		$item->image = '';
		$item->state = 1;
		$item->ordering = '';
		$item->component = '';
		$item->controller = '';
		$item->action = '';
		$item->access_id = 1;
		$item->counter_query = '';
		$item->target = '';

		$menus = $model->getMenusList();	

		// get menu_id from state or first from table
		if (isset($state_filters['menu_id'])) {
			$item->menu_id = $state_filters['menu_id'];
		} else {
			$item->menu_id = $menus[0];
		}

		$components = $model->getActiveComponents();
		$controllers = $model->getActiveControllers();
		$parents = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('item', $item);
		$tmpl->setVar('parents', $parents);
		$tmpl->setVar('menus', $menus);
		$tmpl->setVar('users_access', $users_access);
		$tmpl->setVar('controllers', $controllers);
		$tmpl->setVar('components', $components);

		$tmpl->display('items_edit');
		
	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('items');

			if ($model->itemExist($id)) {

				// get data
				$item = $model->getItem($id);

				$model->addFilter('items.menu_id');
				$parents = $model->getItems();
				
				$menus = $model->getMenusList();				
				$controllers = $model->getActiveControllers();
				$components = $model->getActiveComponents();

				$users_access = $model->getUsersAccessTree();

				// unset current item and child items from items list
				$unset = array();
				$unset[] = $item->id;

				foreach ($parents as $key => $parent) {
					if (in_array($parent->id, $unset) or in_array($parent->parent_id, $unset)) {
						$unset[] = $parent->id;
						unset($parents[$key]);
					}
				}

				// set vars and load template
				$tmpl = new template;

				$tmpl->setVar('item', $item);
				$tmpl->setVar('parents', $parents);
				$tmpl->setVar('menus', $menus);
				$tmpl->setVar('users_access', $users_access);
				$tmpl->setVar('controllers', $controllers);
				$tmpl->setVar('components', $components);

				$tmpl->display('items_edit');

			} else {
				Main::redirect('/admin/menu/items', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/menu/items', 'Не указан id элемента');
		}

	}

	function save() {

		$model = $this->getModel('items');

		$id = (int) $_POST['id'];

		$data = array();
		$params = array();
		
		$data['title'] = $_POST['title'];		
		$data['parent_id'] = $_POST['parent_id'];
		$data['component'] = $_POST['component'];
		$data['controller'] = $_POST['controller'];
		$data['action'] = $_POST['action'];
		$data['target'] = $_POST['target'];
		$data['description'] = $_POST['description'];
		$data['image'] = $_POST['image'];
		$data['state'] = $_POST['state'];
		$data['access_id'] = intval($_POST['access_id']);			
		$data['counter_query'] = req::string('counter_query');		
		
		if ($id == 0) {
			$data['menu_id'] = $_POST['menu_id'];
			$data['ordering'] = $model->getLastOrdering() + 1;
		}

		// Generating url value		
		if (!empty(trim($_POST['component']))) {
			
			$url = '/' . trim($_POST['component']);
		
			if (!empty(trim($_POST['controller']))) {
				$controller_parts = explode('/', trim($_POST['controller']));
				if (count($controller_parts) > 1) {
					$url = '/' . $controller_parts[0] . $url . '/' . $controller_parts[1];
				} else {
					$url .= '/' . $controller_parts[0];
				}
			}

			if (!empty(trim($_POST['action']))) {
				$url .= '/' . trim($_POST['action']);
			}

		} else {

			$url = trim($_POST['action']);

		}

		$data['url'] = $url;

		# If it's new element
		if ($id == 0) {
			$id = $success = $model->SaveNewItem($data);			
		} else {
			$success = $model->SaveItem($id, $data);
		}
		
		if ($id == 0) {
			$redirect = '/admin/menu/items/create';
		} else {
			$redirect = '/admin/menu/items/edit/'.$id;
		}

		if (isset($_POST['save'])) {
			$redirect = '/admin/menu/items';
		}

		if ($success) {
			$message = 'Запись успешно сохранена.';
		} else {
			$message = 'Произошла ошибка при сохранении записи.';
		}

		Main::redirect($redirect, $message);

	}

	function delete() {
		
		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('items');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/menu/items', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/menu/items', 'Успешно удалено ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/menu/items', 'Не указан id элемента');
	   	}

	}

    function sort() {                  
        
        if (isset($_POST['item'])) {
	        
	        $ordering = $_POST['item'];

	        $model = $this->getModel('items');
	        
	        foreach ($ordering as $order => $id) {                
	            
	            $model->SaveItem($id, array('ordering' => $order));

	        }
        }

    }

    function frontpageitem() {

    	//print_r($_POST);

    	$success = false;

    	$model = $this->getModel('items');

    	if (isset($_POST['checked']) && count($_POST['checked'])) {

    		$id = (int) array_shift($_POST['checked']);

    	 	if ($id > 0) {

    	 		$model->resetFrontPage();

    	 		$data = array();
    			$data['frontpage'] = 1;

    			$success = $model->SaveItem($id, $data);

    	 	} 	

    	}

    	if ($success) {
    		Main::redirect('/admin/menu/items', 'Элемент успешно назначен начальной страницей');
    	} else {
    		Main::redirect('/admin/menu/items', 'Произошла ошибка при назначении эдемента в качестве начальной страницы');
    	}

    }

}