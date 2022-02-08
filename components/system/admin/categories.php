<?php
Class SystemCategoriesController Extends ControllerBase {

	function index() {
		
		$model = $this->getModel('categories');

		$model->initUserOrdering();

		// FILTERS ADRESS
		$filter = new Filter;
		$filter->name = 'title';
		$filter->column = 'title';
		$filter->title = 'Заголовок';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// FILTERS CATEGORY
		$filter = new Filter;
		$filter->name = 'component';
		$filter->column = 'component';
		$filter->title = 'Компонент';
		$filter->setValues($model->getActiveComponents(), 'name', 'name');		

		$model->_setFilter($filter);

		// set controller filter
		// $filter_values = array();
		// $current_controller = '';
		// $controllers = $model->getActiveControllers();

		// foreach ($controllers as $controller) {
		// 	$filter_values[$controller->name] = $controller->name;
		// }

		// if (isset($_GET['controller']) and isset($filter_values[$_GET['controller']])) {
		// 	$current_controller = $_GET['controller'];
		// }

		// $model->addFilter('controller', 'Контроллер', $filter_values, $current_controller);

		// set pagination
		//$pagination = $model->getPagination();

		// get categories items
		$items = $model->getItems();

		//print_r($items);

		// set template vars and display result
		$tmpl = new Template;

		$tmpl->setVar('items', $items);
		//$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Категории');

		$tmpl->display('categories');
	}

	function create() {

		$item = new stdClass;
		$item->id = 0;
		$item->title = '';
		$item->alias = '';
		$item->description = '';
		$item->state = 1;
		$item->parent_id = 0;
		$item->component = '';

		$model = $this->getModel('categories');
		
		$components = $model->getActiveComponents();
		$parents = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('item', $item);
		$tmpl->setVar('components', $components);
		$tmpl->setVar('parents', $parents);

		$tmpl->display('categories_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('categories');

			if ($model->itemExist($id)) {

				// get item data
				$item = $model->getItem($id);

				// set filter by component for parent intems
				$model->addFilter('component', 'component', array(), $item->component);

				// get parent items
				$parents = $model->getItems();

				// unset current item from parent items
				$unset = array();
				$unset[] = $item->id;

				foreach ($parents as $key => $parent) {
					if (in_array($parent->id, $unset) or in_array($parent->parent_id, $unset)) {
						$unset[] = $parent->id;
						unset($parents[$key]);
					}
				}

				// get registred contorllers
				$components = $model->getActiveComponents();

				// set template values and display
				$tmpl = new Template;

				$tmpl->setVar('item', $item);
				$tmpl->setVar('parents', $parents);
				$tmpl->setVar('components', $components);

				$tmpl->display('categories_edit');

			} else {
				Main::redirect('/admin/system/categories', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/system/categories', 'Не указан id элемента');
		}

	}

	function save() {

		$model = $this->getModel('categories');

		$id = (int) $_POST['id'];

		$data = array();

		$data['title'] = $_POST['title'];

		if (empty($_POST['alias'])) {			
			$data['alias'] = $model->finishAlias(Main::str2url($data['title']), $id);	
		} else {
			$data['alias'] = $model->finishAlias($_POST['alias'], $id);	
		}
		
		$data['description'] = $_POST['description'];
		$data['state'] = $_POST['state'];
		$data['parent_id'] = $_POST['parent_id'];
		
		if ($id == 0) $data['component'] = $_POST['component'];
		
		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($data);

			if ($id > 0) {
				Main::Redirect('/admin/system/categories/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/system/categories', 'Ошибка! Не удалось сохранить запись.');
			}
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $data)) {
				Main::Redirect('/admin/system/categories/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/system/categories/edit/' . $id, 'Ошибка! Не удалось сохранить запись.');
			}

		}

	}

	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('categories');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/system/categories', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/system/categories', 'Успешно удалено ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/system/categories', 'Не указан id элемента');
	   	}
		
	}

	function sort() {                  
        
        if (isset($_POST['item'])) {
	        
	        $ordering = $_POST['item'];

	        $model = $this->getModel('categories');
	        
	        foreach ($ordering as $order => $id) {                
	            
	            $model->SaveItem($id, array('ordering' => $order));

	        }
        }

    }

}