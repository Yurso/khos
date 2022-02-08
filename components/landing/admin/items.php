<?php
Class LandingItemsController Extends ControllerBase {

	public function index() {

		$model = $this->getModel('items');
		
		$model->initUserOrdering();

		$pagination = $model->initPagination();

		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);
		$tmpl->setVar('buttons', $this->_buildListButtonsArray());

		$tmpl->setTitle('Менеджер лендингов');
		
		$tmpl->display('items');

	}

	public function create() {

		$model = $this->getModel('items');

		$types = $model->getTypesList();

		$type_name = '';
		
		if (isset($_GET['type']) && in_array($_GET['type'], $types)) {							
			$type_name = $_GET['type'];	
		}

		if (!empty($type_name)) {

			$type_class = $model->getTypeClass($type_name);

			$item = new stdClass;
			$item->id = 0;
			$item->title = '';
			$item->content = '';
			$item->type = $type_name;
			$item->state = 1;
			$item->ordering = $model->getLastOrderingValue()+1;
			$item->create_date = '';
			$item->modify_date = '';	
			$item->author_name = '';	

			$tmpl = new template;

			$tmpl->setVar('item', $item);
			$tmpl->setVar('edit_form', $type_class->getEditForm($item));
			$tmpl->setVar('params_form', $type_class->getParamsForm(array()));
			$tmpl->setVar('buttons', $this->_buildEditButtonsArray());

			$tmpl->display('items_edit');

		} else {
			$tmpl = new template;
			$tmpl->setVar('types', $types);
			$tmpl->display('items_type_choose');
		}

	}

	public function edit() {
		// Get args array
		$args = Registry::get('route')->args;
		// If user does't choose any item
		if (!isset($args[0])) {
			Main::redirect('/admin/landing/items', 'Не указан id элемента');
		}		
		// Init model class
		$model = $this->getModel('items');
		// Some security for item id
		$id = intval($args[0]);
		// Check if item exist
		if ($model->itemExist($id)) {
			// Get item object
			$item = $model->getItem($id);
			// Init type class
			$type_class = $model->getTypeClass($item->type);
			// Init template
			$tmpl = new Template;
			// Set template vars
			$tmpl->setVar('item', $item);
			$tmpl->setVar('edit_form', $type_class->getEditForm($item));
			$tmpl->setVar('params_form', $type_class->getParamsForm($item->params));
			$tmpl->setVar('buttons', $this->_buildEditButtonsArray());
			// Display template
			$tmpl->display('items_edit');
		} else {
			// Redirect to list if nothing choosed
			Main::redirect('/admin/landing/items', 'Ошибка! Элемент с таким id не найден.');
		}

	}
	
	// Additional action for edit button in list
	public function edit_checked() {		
		// Init model class
		$model = $this->getModel('items');
		// If choosed some elemnt from list
		if (isset($_POST['checked'])) {
			// Check for exist object and redirect 
			foreach ($_POST['checked'] as $value) {
	   			// Some security for id value
	   			$id = intval($value);
	   			// Redirect to item edit if item exist
	   			if ($model->itemExist($id)) {
	    			Main::redirect('/admin/landing/items/edit/'.$id);
	    		}
	   		}	   		
		}
		// Redirect to list if nothing choosed
	   	Main::redirect('/admin/landing/items', 'Ошибка при выборе элемента для редактирования');
	}

	public function save() {

		$redirect = '';
		$message = '';

		// Some data, which we will use
		$model = $this->getModel('items');	
		$user = Registry::get('user');	
		
		// set id value from post and unset post[id] value
		$id = (int) $_POST['id'];
		unset($_POST['id']);

		// Type check and init type class
		$types = $model->getTypesList();
		// Default type_name
		$type_name = '';	
		// Check type value in POST		
		if (isset($_POST['type']) && in_array($_POST['type'], $types)) {							
			$type_name = $_POST['type'];	
		}	
		// Redirect if something wrong with type	
		if (empty($type_name)) {
			Main::Redirect('/admin/landing/items', 'Указан неверный тип.');	
		}
		// Init type class
		$type_class = $model->getTypeClass($type_name);

		// Trigger onBeforeItemSave
		$type_class->onBeforeItemSave();

		// // If user not an author
		// if (!$model->checkItemAccess($id)) {
		// 	Main::Redirect('/admin/realty/objects/edit/'.$id, 'Недостаточно прав для записи объекта.');	
		// }		

		// collect object data
		$data = array();
		$fields = array();

		$columns = $model->getTableColumns();

		foreach ($columns as $column) {
			if (isset($_POST[$column->Field])) {
				if (gettype($_POST[$column->Field]) == 'array') {
					$data[$column->Field] = serialize($_POST[$column->Field]);
				} else {
					$data[$column->Field] = trim($_POST[$column->Field]);
				}
			}
		}	

		$data['title'] = htmlspecialchars($data['title']);
		$data['modify_date'] = date("Y-m-d H:i:s");	
		$data['params'] = serialize($_POST['params']);	

		// if it's new object
		if ($id == 0) {
			$data['author_id'] = $user->id;	
			$data['create_date'] = date("Y-m-d H:i:s");
		} 

		# If it's new element
		if ($id == 0) {
			$id = $model->SaveNewItem($data);
			if ($id > 0) {
				$message = 'Запись успешно сохранена.';				
			} else {
				$message = 'Ошибка! Произошла ошибка базы даных. Не удалось сохранить запись.';			
			}			
		} elseif ($id > 0) {
			if ($model->SaveItem($id, $data)) {				
				$message = 'Запись успешно сохранена.';				
			} else {
				$message = 'Ошибка! Не удалось сохранить запись.';			
			}
		}

		$redirect = '/admin/landing/items/edit/' . $id;

		if ($id == 0) {
			$redirect = '/admin/landing/items/create'; 			
		} else {
			$item = $model->getItem($id);
			$type_class->onAfterItemSave($item);
		}

		// redirect to items list if user press save 
		if (isset($_GET['close']) && $_GET['close'] == 1) {			
			$redirect = '/admin/landing/items';					
		}

		Main::Redirect($redirect, $message);

	}

	public function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('items');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/landing/items', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/landing/items', 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/landing/items', 'Не указан id элемента');
	   	}
		
	}

	public function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('items');
			$user = User::getUserData();
			$columns = $model->getTableColumns();
			$ordering = $model->getLastOrderingValue();

			$i = 0;

			foreach ($_POST['checked'] as $value) {
				// GET id value from POST data
				$id = intval($value);
				// Get old item values
				$item = $model->getItem($id);
				// New element array
				$data = array();
				// Copy all fields
				foreach ($columns as $column) {
					$field = $column->Field;
					$data[$field] = $item->$field;
				}
				// Additional data changes
				$data['title'] = $item->title . ' (copy)';
				$data['create_date'] = date("Y-m-d H:i:s");
				$data['modify_date'] = date("Y-m-d H:i:s");
				$data['author_id'] = $user->id;
				$ordering++;
				$data['ordering'] = $ordering;
				// We don't need id value for new element
				unset($data['id']);
				// Save new element
				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/landing/items', 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/admin/landing/items', 'Нечего копировать.');
			}

		}

	}

	public function sort() {                  
        
        if (isset($_POST['item'])) {
	        
	        $ordering = $_POST['item'];

	        $model = $this->getModel('items');
	        
	        foreach ($ordering as $order => $id) {                
	            
	            $model->SaveItem($id, array('ordering' => $order));

	        }
        }

    }

	private function _buildEditButtonsArray() {

		$buttons = array();		
		
		$buttons[] = array(
			'title' => 'Сохранить',
			'action' => '/save?close=1'
		);
		$buttons[] = array(
			'title' => 'Применить',
			'action' => '/save'
		);
		$buttons[] = array(
			'title' => 'Закрыть',
			'action' => '/'
		);

		return $buttons;

	}

	private function _buildListButtonsArray() {

		$buttons = array();		
		
		$buttons[] = array(
			'title' => 'Создать',
			'action' => '/create'
		);
		$buttons[] = array(
			'title' => 'Редактировать',
			'action' => '/edit_checked'
		);
		$buttons[] = array(
			'title' => 'Скопировать',
			'action' => '/duplicate'
		);
		$buttons[] = array(
			'title' => 'Удалить',
			'action' => '/delete'
		);

		return $buttons;

	}
	
}