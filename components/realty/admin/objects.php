<?php
Class RealtyObjectsController Extends ControllerBase {

	public function index() {

		$model = $this->getModel('objects');
		
		$model->initUserOrdering();	

		$params = $model->getParams();		

		// FILTERS ADRESS
		$filter = new Filter;
		$filter->name = 'adress';
		$filter->column = 'r.adress';
		$filter->title = 'Адрес';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);
		
		// FILTERS CATEGORY
		$filter = new Filter;
		$filter->name = 'category';
		$filter->column = 'r.category_id';
		$filter->title = 'Категория';
		$filter->setValues($model->getCategoriesList(), 'id', 'title');		

		$model->_setFilter($filter);

		// FILTERS AGENCY
		$filter = new Filter;
		$filter->name = 'agency';
		$filter->column = 'r.agency_id';
		$filter->title = 'Агентство';	
		$filter->setValues($model->getAgencysList(), 'id', 'name');		

		$model->_setFilter($filter);

		// FILTERS PRICE_FROM
		$filter = new Filter;
		$filter->name = 'price_from';
		$filter->column = 'r.price';
		$filter->type = 'int';
		$filter->operator = '>=';
		$filter->title = 'Цена от';			

		$model->_setFilter($filter);	

		// FILTERS PRICE_TO
		$filter = new Filter;
		$filter->name = 'price_to';
		$filter->column = 'r.price';
		$filter->type = 'int';
		$filter->operator = '<=';
		$filter->title = 'Цена до';		
		$filter->empty_value = 100000000;

		$model->_setFilter($filter);

		// FILTERS agent_name
		$filter = new Filter;
		$filter->name = 'agent_name';
		$filter->column = 'r.agent_name';
		$filter->title = 'Агент';
		$filter->operator = 'LIKE';
		$filter->advansed = true;	
		
		$model->_setFilter($filter);

		// FILTERS house_type
		$filter = new Filter;
		$filter->name = 'house_type';
		$filter->column = 'r.house_type';
		$filter->type = 'int';
		$filter->operator = '=';
		$filter->title = 'Тип дома';	
		$filter->advansed = true;			
		$filter->values = $params['house_type'];

		$model->_setFilter($filter);

		// FILTERS wc_type
		$filter = new Filter;
		$filter->name = 'wc_type';
		$filter->column = 'r.wc_type';
		$filter->type = 'int';
		$filter->operator = '=';
		$filter->title = 'Санузел';	
		$filter->advansed = true;			
		$filter->values = $params['wc_type'];		

		$model->_setFilter($filter);

		// FILTERS loggia_type
		$filter = new Filter;
		$filter->name = 'loggia_type';
		$filter->column = 'r.loggia_type';
		$filter->type = 'int';
		$filter->operator = '=';
		$filter->title = 'Лоджия/Балкон';	
		$filter->advansed = true;			
		$filter->values = $params['loggia_type'];		

		$model->_setFilter($filter);

		// FILTERS exclusive
		$filter = new Filter;
		$filter->name = 'param_exclusive';
		$filter->column = 'r.param_exclusive';
		$filter->type = 'int';
		$filter->operator = '=';
		$filter->title = 'Эксклюзив';	
		$filter->advansed = true;			
		$filter->values = array(1 => 'Только эксклюзив');		

		$model->_setFilter($filter);

		// FILTERS archive
		$filter = new Filter;
		$filter->name = 'archive';
		$filter->column = 'r.archive';
		$filter->type = 'int';
		$filter->operator = '<';
		$filter->title = 'Архив';	
		$filter->hidden = true;			
		$filter->value = 1;		

		$model->_setFilter($filter);

		// FILTERS deleted
		$filter = new Filter;
		$filter->name = 'deleted';
		$filter->column = 'r.deleted';
		$filter->type = 'int';
		$filter->operator = '<';
		$filter->title = 'Архив';	
		$filter->hidden = true;			
		$filter->first_empty_value = false;
		$filter->value = 1;		

		$model->_setFilter($filter);

		$pagination = $model->initPagination();
		
		// Get items array
		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('params', $params);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Объекты недвижимости');
		
		$tmpl->display('objects');

	}

	public function archive() {

		$user = User::getUserData();
		
		$model = $this->getModel('objects');
		
		$model->initUserOrdering();

		$params = $model->getParams();		

		// FILTERS ADRESS
		$filter = new Filter;
		$filter->name = 'adress';
		$filter->column = 'r.adress';
		$filter->title = 'Адрес';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);
		
		// FILTERS CATEGORY
		$filter = new Filter;
		$filter->name = 'category';
		$filter->column = 'r.category_id';
		$filter->title = 'Категория';
		$filter->setValues($model->getCategoriesList(), 'id', 'title');		

		$model->_setFilter($filter);

		// FILTERS AGENCY
		$filter = new Filter;
		$filter->name = 'agency';
		$filter->column = 'r.agency_id';
		$filter->title = 'Агентство';	
		$filter->setValues($model->getAgencysList(), 'id', 'name');		

		$model->_setFilter($filter);

		// FILTERS PRICE_FROM
		$filter = new Filter;
		$filter->name = 'price_from';
		$filter->column = 'r.price';
		$filter->type = 'int';
		$filter->operator = '>=';
		$filter->title = 'Цена от';			

		$model->_setFilter($filter);	

		// FILTERS PRICE_TO
		$filter = new Filter;
		$filter->name = 'price_to';
		$filter->column = 'r.price';
		$filter->type = 'int';
		$filter->operator = '<=';
		$filter->title = 'Цена до';		
		$filter->empty_value = 100000000;

		$model->_setFilter($filter);

		// FILTERS house_type
		$filter = new Filter;
		$filter->name = 'house_type';
		$filter->column = 'r.house_type';
		$filter->type = 'int';
		$filter->operator = '=';
		$filter->title = 'Тип дома';	
		$filter->advansed = true;			
		$filter->values = $params['house_type'];

		$model->_setFilter($filter);

		// FILTERS wc_type
		$filter = new Filter;
		$filter->name = 'wc_type';
		$filter->column = 'r.wc_type';
		$filter->type = 'int';
		$filter->operator = '=';
		$filter->title = 'Санузел';	
		$filter->advansed = true;			
		$filter->values = $params['wc_type'];		

		$model->_setFilter($filter);

		// FILTERS loggia_type
		$filter = new Filter;
		$filter->name = 'loggia_type';
		$filter->column = 'r.loggia_type';
		$filter->type = 'int';
		$filter->operator = '=';
		$filter->title = 'Лоджия/Балкон';	
		$filter->advansed = true;			
		$filter->values = $params['loggia_type'];		

		$model->_setFilter($filter);

		// FILTERS deleted
		$filter = new Filter;
		$filter->name = 'deleted';
		$filter->column = 'r.deleted';
		$filter->type = 'int';
		$filter->operator = '<';
		$filter->title = 'Архив';	
		$filter->hidden = true;			
		$filter->first_empty_value = false;
		$filter->value = 1;		

		$model->_setFilter($filter);

		// FILTERS archive
		$filter = new Filter;
		$filter->name = 'archive';
		$filter->column = 'r.archive';
		$filter->type = 'int';
		$filter->operator = '>';
		$filter->title = 'Архив';	
		$filter->hidden = true;			
		$filter->value = 0;		

		$model->_setFilter($filter);

		if ($user->access_name == 'manager') {
			// FILTERS author_id	
			$filter = new Filter;
			$filter->name = 'author_id';
			$filter->column = 'r.author_id';
			$filter->type = 'int';
			$filter->operator = '=';
			$filter->title = 'Автор';		
			$filter->hidden = true;	
			$filter->first_empty_value = false;
			$filter->value = $user->id;	
		}

		if ($user->access_name == 'chief') {
			// FILTERS author_id	
			$filter = new Filter;
			$filter->name = 'agency_id';
			$filter->column = 'r.agency_id';
			$filter->type = 'int';
			$filter->operator = '=';
			$filter->title = 'Автор';		
			$filter->hidden = true;	
			$filter->first_empty_value = false;
			$filter->value = $user->agency_id;	
		}
				

		$model->_setFilter($filter);

		$pagination = $model->initPagination();

		// Get items array
		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('params', $params);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Архив объектов');
		
		$tmpl->display('objects');

	}

	public function trash() {

		$user = User::getUserData();
		$model = $this->getModel('objects');
		
		$model->initUserOrdering();

		$params = $model->getParams();	

		// FILTERS ADRESS
		$filter = new Filter;
		$filter->name = 'adress';
		$filter->column = 'r.adress';
		$filter->title = 'Адрес';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);
		
		// FILTERS CATEGORY
		$filter = new Filter;
		$filter->name = 'category';
		$filter->column = 'r.category_id';
		$filter->title = 'Категория';
		$filter->setValues($model->getCategoriesList(), 'id', 'title');		

		$model->_setFilter($filter);

		// FILTERS AGENCY
		$filter = new Filter;
		$filter->name = 'agency';
		$filter->column = 'r.agency_id';
		$filter->title = 'Агентство';	
		$filter->setValues($model->getAgencysList(), 'id', 'name');		

		$model->_setFilter($filter);

		// FILTERS PRICE_FROM
		$filter = new Filter;
		$filter->name = 'price_from';
		$filter->column = 'r.price';
		$filter->type = 'int';
		$filter->operator = '>=';
		$filter->title = 'Цена от';			

		$model->_setFilter($filter);	

		// FILTERS PRICE_TO
		$filter = new Filter;
		$filter->name = 'price_to';
		$filter->column = 'r.price';
		$filter->type = 'int';
		$filter->operator = '<=';
		$filter->title = 'Цена до';		
		$filter->empty_value = 100000000;

		$model->_setFilter($filter);

		// FILTERS house_type
		$filter = new Filter;
		$filter->name = 'house_type';
		$filter->column = 'r.house_type';
		$filter->type = 'int';
		$filter->operator = '=';
		$filter->title = 'Тип дома';	
		$filter->advansed = true;			
		$filter->values = $params['house_type'];

		$model->_setFilter($filter);

		// FILTERS wc_type
		$filter = new Filter;
		$filter->name = 'wc_type';
		$filter->column = 'r.wc_type';
		$filter->type = 'int';
		$filter->operator = '=';
		$filter->title = 'Санузел';	
		$filter->advansed = true;			
		$filter->values = $params['wc_type'];		

		$model->_setFilter($filter);

		// FILTERS loggia_type
		$filter = new Filter;
		$filter->name = 'loggia_type';
		$filter->column = 'r.loggia_type';
		$filter->type = 'int';
		$filter->operator = '=';
		$filter->title = 'Лоджия/Балкон';	
		$filter->advansed = true;			
		$filter->values = $params['loggia_type'];		

		$model->_setFilter($filter);

		// FILTERS deleted
		$filter = new Filter;
		$filter->name = 'deleted';
		$filter->column = 'r.deleted';
		$filter->type = 'int';
		$filter->operator = '>';	
		$filter->hidden = true;			
		$filter->first_empty_value = false;
		$filter->value = 0;		

		$model->_setFilter($filter);

		// FILTERS author_id
		$filter = new Filter;
		$filter->name = 'author_id';
		$filter->column = 'r.author_id';
		$filter->type = 'int';
		$filter->operator = '=';
		$filter->title = 'Автор';
		
		if ($user->access_name != 'administrator') {
			$filter->hidden = true;	
			$filter->first_empty_value = false;
			$filter->value = $user->id;	
		}				

		$model->_setFilter($filter);

		// Get params list
		$params = $model->getParams();

		$pagination = $model->initPagination();
		// Get items list
		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('params', $params);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Корзина');
		
		$tmpl->display('objects');

	}

	public function create() {

		$model = $this->getModel('objects');
		$user = user::getUserData();

		$data = new stdClass;
		$data->id = 0;
		$data->category_id = 0;
		$data->adress = '';
		$data->floors = '';
		$data->floor = '';
		$data->house_type = '';
		$data->total_area = '';
		$data->living_area = '';
		$data->kitchen_area = '';
		$data->wc_type = '';
		$data->loggia_type = '';
		$data->rights = '';
		$data->price = '';
		$data->commission = '';
		$data->agency_id = 0;
		$data->agent_name = $user->name;
		$data->agent_phone = $user->phone;
		$data->type_of_deal = '';
		$data->create_date = '';
		$data->last_edit = '';
		$data->author_name = '';
		$data->state = 1;
		$data->comment = '';
		$data->param_uglovaya = '';
		$data->param_pipes = '';
		$data->param_windows = '';
		$data->param_flooring = '';
		$data->param_main_door = '';
		$data->param_room_doors = '';
		$data->param_exclusive = 0;
		$data->images = array();
		$data->disabled = false;		
		$data->archive = 0;
		$data->deleted = 0;

		$categories = $model->getCategoriesList();
		$agencys = $model->getUserAgencys();
		$house_types = $model->getHouseTypes();
		$params = $model->getParams();		

		$template = new template;

		$template->setVar('data', $data);
		$template->setVar('categories', $categories);
		$template->setVar('agencys', $agencys);		
		$template->setVar('house_types', $house_types);
		$template->setVar('params', $params);

		$template->display('objects_edit');

	}

	public function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('objects');

			if ($model->itemExist($id)) {

				$data = $model->getItem($id);

				$categories = $model->getCategoriesList();
				$agencys = $model->getAgencysList();
				$params = $model->getParams();

				$images_model = $this->getModel('images');
				$data->images = $images_model->getItems(array('object_id' => $id));

				// checking for form access
				$data->disabled = !$model->checkItemAccess($id);

				$tmpl = new Template;

				$tmpl->setVar('data', $data);
				$tmpl->setVar('categories', $categories);				
				$tmpl->setVar('agencys', $agencys);
				$tmpl->setVar('params', $params);
				$tmpl->setVar('config', registry::get('config'));

				$tmpl->display('objects_edit');

			} else {
				Main::redirect('/admin/realty/objects', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/realty/objects', 'Не указан id элемента');
		}

	}

	public function save() {

		$redirect = '';
		$message = '';

		$model = $this->getModel('objects');

		$id = (int) $_POST['id'];

		$item = $model->getItem($id);
		$userData = User::getUserData();			

		// If user not an author
		if (!$model->checkItemAccess($id)) {
			Main::Redirect('/admin/realty/objects/edit/'.$id, 'Недостаточно прав для записи объекта.');	
		}					

		$data = array();

		$data['category_id'] = $_POST['category_id'];
		$data['adress'] = trim($_POST['adress']);
		$data['floors'] = $_POST['floors'];
		$data['floor'] = $_POST['floor'];
		$data['house_type'] = $_POST['house_type'];
		$data['total_area'] = $_POST['total_area'];
		$data['living_area'] = $_POST['living_area'];
		$data['kitchen_area'] = $_POST['kitchen_area'];
		$data['wc_type'] = $_POST['wc_type'];
		$data['loggia_type'] = $_POST['loggia_type'];
		$data['rights'] = trim($_POST['rights']);
		$data['price'] = intval(str_replace(" ", "", $_POST['price']));
		$data['commission'] = intval(str_replace(" ", "", $_POST['commission']));
		
		if (isset($_POST['agency_id']))
			$data['agency_id'] = $_POST['agency_id'];

		$data['agent_name'] = trim($_POST['agent_name']);
		$data['agent_phone'] = trim($_POST['agent_phone']);
		$data['type_of_deal'] = trim($_POST['type_of_deal']);
		$data['last_edit'] = date("Y-m-d H:i:s");
		$data['state'] = $_POST['state'];
		$data['comment'] = trim($_POST['comment']);
		$data['param_uglovaya'] = $_POST['param_uglovaya'];
		$data['param_pipes'] = $_POST['param_pipes'];
		$data['param_windows'] = $_POST['param_windows'];
		$data['param_flooring'] = $_POST['param_flooring'];
		$data['param_main_door'] = $_POST['param_main_door'];
		$data['param_room_doors'] = $_POST['param_room_doors'];
		$data['param_exclusive'] = $_POST['param_exclusive'];

		if ($id == 0) {
			$data['author_id'] = $userData->id;	
			$data['create_date'] = date("Y-m-d H:i:s");
		} 

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

		if ($id > 0) {
			// Save images
			$images_model = $this->getModel('images');
			$images_model->_SaveUploadedImages($id);
		}


		$redirect = '/admin/realty/objects/edit/' . $id;

		if ($id == 0)
			$redirect = '/admin/realty/objects/create'; 			

		// redirect to items list if user press save 
		if (isset($_POST['save'])) {
			
			$redirect = '/admin/realty/objects';		
			
		}

		Main::Redirect($redirect, $message);

	}

	public function view() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('objects');

			if ($model->itemExist($id)) {

				$data = $model->getItem($id);

				$categories = $model->getCategoriesList();
				$agencys = $model->getAgencysList();
				$params = $model->getParams();

				$images_model = $this->getModel('images');
				$data->images = $images_model->getItems(array('object_id' => $id));

				// checking for form access
				$data->disabled = !$model->checkItemAccess($id);

				$tmpl = new Template;

				$tmpl->setVar('data', $data);
				$tmpl->setVar('categories', $categories);				
				$tmpl->setVar('agencys', $agencys);
				$tmpl->setVar('params', $params);
				$tmpl->setVar('config', registry::get('config'));

				// mini view (hide buttons and extra fields)
				$miniview = false;
				if (isset($_GET['miniview']) && $_GET['miniview'] > 0) {
					$miniview = true;
				}
				$tmpl->setVar('miniview', $miniview);

				$tmpl->display('objects_view');

			} else {
				Main::redirect('/admin/realty/objects', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/realty/objects', 'Не указан id элемента');
		}

	}

	public function delete() {

		$redirect = '/admin/realty';

		if (isset($_POST['ref_page']) && !empty($_POST['ref_page'])) {
			$redirect = '/'.$_POST['ref_page']; 
		}

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('objects');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];

	    	if ($model->checkItemAccess($id)) {	    	

		    	if ($model->deleteItem($id)) {
		    		Main::Redirect($redirect, 'Объект успешно удален');
		    	}

		    } else {

		    	Main::Redirect($redirect, 'Недостаточно прав для удаления объекта id = ' . $id);

		    }

	   	} elseif (isset($_POST['checked'])) {

	   		foreach ($_POST['checked'] as $key => $value) {
	   			
	   			$id = (int) $value;	   			

	   			if ($model->checkItemAccess($id)) {

		   			if ($model->deleteItem($id)) {
		    			$i++;
		    		} else {
		    			Main::setMessage('Не удалось удалить объект id = ' . $id);
		    		}

		    	} else {

		    		Main::setMessage('Недостаточно прав для удаления объекта id = ' . $id);

		    	}

	   		}

	   		Main::redirect($redirect, 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('объект', $i));

	   	} else {
	   		Main::Redirect($redirect, 'Вы не выбрали ни одного объекта для удаления');
	   	}
		
	}

	public function recover() {		
		
		$model = $this->getModel('objects');
		
		$redirect = '/admin/realty/objects';

		if (isset($_POST['ref_page']) && !empty($_POST['ref_page'])) {
			$redirect = '/'.$_POST['ref_page']; 
		}

		$i = 0;	

		$args = Registry::get('route')->args;		

    	if (isset($args[0])) {

	    	$id = (int) $args[0];

	    	if ($model->checkItemAccess($id, true)) {	    	

		    	$params = array();
					
				$params['archive'] = 0;
				$params['deleted'] = 0;	
				$params['last_edit'] = date("Y-m-d H:i:s");

				if ($model->SaveItem($id, $params)) {
					$i++;
				}

		    } else {

		    	Main::setMessage('Нет доступа к объекту id='.$id);

		    }

	   	} elseif (isset($_POST['checked'])) {					

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				if ($model->checkItemAccess($id, true)) {

					$params = array();
					
					$params['archive'] = 0;
					$params['deleted'] = 0;	
					$params['last_edit'] = date("Y-m-d H:i:s");

					if ($model->SaveItem($id, $params)) {
						$i++;
					}

				} else {

					Main::setMessage('Нет доступа к объекту id='.$id);

				}		

			}			

		} else {
	   		Main::Redirect($redirect, 'Вы не выбрали ни одного объекта для восстановления');
	   	}

		Main::Redirect($redirect, 'Восстановлено ' . $i . ' ' . Main::declension_by_number('объект', $i));			

	}

	public function archivate() {

		$message = '';
		$model = $this->getModel('objects');
		$args = Registry::get('route')->args;		

		if (isset($_POST['checked'])) {			

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;				
				 
				if ($model->checkItemAccess($id)) {

					$params = array();

					$params['archive'] = 1;

					if ($model->SaveItem($id, $params)) {
						$i++;
					}

				} else {

					Main::setMessage('Нет доступа к объекту id='.$id);

				}	

			}
			
			$message = 'Помещено в архив ' . $i . ' ' . Main::declension_by_number('объект', $i);	

		} elseif (isset($args[0])) {
			
			$id = intval($args[0]);				
				 
			if ($model->checkItemAccess($id)) {

				$params = array();

				$params['archive'] = 1;

				if ($model->SaveItem($id, $params)) {
					$i++;
				}

			} else {
				Main::setMessage('Нет доступа к объекту id='.$id);
			}

			$message = 'Объект успешно помещен в архив';	

		} else {
	   		
	   		$message = 'Вы не выбрали ни одного объекта для архивации';

	   	}

		Main::Redirect('/admin/realty/objects', $message);		

	}

	public function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('objects');

			$user = new User;
			$user_data = $user->getUserData();

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);				

				$data = array();

				$data['category_id'] = $item->category_id;
				$data['adress'] = $item->adress;
				$data['floors'] = $item->floors;
				$data['floor'] = $item->floor;
				$data['house_type'] = $item->house_type;
				$data['total_area'] = $item->total_area;
				$data['living_area'] = $item->living_area;
				$data['kitchen_area'] = $item->kitchen_area;
				$data['wc_type'] = $item->wc_type;
				$data['loggia_type'] = $item->loggia_type;
				$data['rights'] = $item->rights;
				$data['price'] = $item->price;
				$data['commission'] = $item->commission;
				$data['agency_id'] = $item->agency_id;
				$data['agent_name'] = $item->agent_name;
				$data['agent_phone'] = $item->agent_phone;
				$data['type_of_deal'] = $item->type_of_deal;
				$data['author_id'] = $user_data->id;	
				$data['create_date'] = date("Y-m-d H:i:s");	
				$data['last_edit'] = date("Y-m-d H:i:s");
				$data['state'] = $item->state;
				$data['comment'] = $item->comment;
				$data['param_uglovaya'] = $item->param_uglovaya;
				$data['param_pipes'] = $item->param_pipes;
				$data['param_windows'] = $item->param_windows;
				$data['param_flooring'] = $item->param_flooring;
				$data['param_main_door'] = $item->param_main_door;
				$data['param_room_doors'] = $item->param_room_doors;

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/realty/objects', 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/admin/realty/objects', 'Нечего копировать.');
			}

		}

	}

	public function printview() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('objects');

			if ($model->itemExist($id)) {

				$data = $model->getItem($id);

				$categories = $model->getCategoriesList();
				$agencys = $model->getAgencysList();
				$params = $model->getParams();
				$agent = $model->getAgentInfo(user::getUserData('id'));

				$images_model = $this->getModel('images');
				$data->images = $images_model->getItems(array('object_id' => $id));

				// checking for form access
				$data->disabled = !$model->checkItemAccess($id);

				$tmpl = new Template;

				$tmpl->setVar('data', $data);
				$tmpl->setVar('categories', $categories);				
				$tmpl->setVar('agencys', $agencys);				
				$tmpl->setVar('params', $params);
				$tmpl->setVar('agent', $agent);
				$tmpl->setVar('config', registry::get('config'));

				$tmpl->display('objects_print', 'print');

			} else {
				Main::redirect('/admin/realty/objects', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/realty/objects', 'Не указан id элемента');
		}

	}

	public function printlist() {

		if (isset($_POST['checked']) && count($_POST['checked'])) {			

			$model = $this->getModel('objects');						

			$id_list = $_POST['checked'];

			$items = $model->getItemsByList($id_list);			
			$params = $model->getParams();

			$agent = $model->getAgentInfo(user::getUserData('id'));

			// Get information about images and icject it to items
			$images_model = $this->getModel('images');	
			foreach ($items as $key => $item) {				
				$item->images = $images_model->getItems(array('object_id' => $item->id));
			}

			$tmpl = new Template;

			$tmpl->setVar('items', $items);
			$tmpl->setVar('params', $params);
			$tmpl->setVar('agent', $agent);
			$tmpl->setVar('config', registry::get('config'));

			$tmpl->display('objects_printlist', 'print');

		} else {
	   		Main::Redirect('/admin/realty/objects', 'Вы не выбрали ни одного объекта для пачети');
	   	}

	}

	public function export() {

		// Будем передавать CSV
		header('Content-Type: text/csv; charset=UTF-8');		
		header('Content-Disposition: attachment; filename="realty-export.csv"');

		$sep = ";";
		$csv = "";
		// headers
		$csv .= "Дата" . $sep;                                
        $csv .= "Адрес" . $sep;
        $csv .= "Категория" . $sep;
        $csv .= "Агентство" . $sep;
        $csv .= "Этаж" . $sep;
        $csv .= "Этажность" . $sep;
        $csv .= "Дом" . $sep;
       	$csv .= "Общая площадь" . $sep;
        $csv .= "Жилая площадь" . $sep;
        $csv .= "Площадь кухни" . $sep;
        $csv .= "Сан. узел" . $sep;
        $csv .= "Лоджия/Балкон" . $sep;
        $csv .= "Эксклюзив" . $sep;
        $csv .= "Угловая" . $sep;
        $csv .= "Трубы" . $sep;
        $csv .= "Окна" . $sep;
        $csv .= "Полы" . $sep;
        $csv .= "Входная дверь" . $sep;
        $csv .= "Межкомнатные двери" . $sep;       
        $csv .= "Статус" . $sep;
        $csv .= "Альтернатива/Прямая продажа" . $sep;
        $csv .= "Цена" . $sep;
        $csv .= "Комиссия" . $sep;
        $csv .= "Агент" . $sep;
        $csv .= "Телефон" . $sep;
        //$csv .= "Дополнительная информация" . $sep;

        $csv .= "\r\n";

		if (isset($_POST['checked'])) {			

			$model = $this->getModel('objects');			

			$id_list = $_POST['checked'];

			$items = $model->getItemsByList($id_list);			
			$params = $model->getParams();

			foreach ($items as $key => $item) {
				
				// Выводим данные по каждой линии
				$csv .= date("d.m.y", strtotime($item->last_edit)) . $sep;                                
                $csv .= $item->adress . $sep;
                $csv .= $item->category_title . $sep;
                $csv .= $item->agency_name . $sep;
                $csv .= $item->floor . $sep;
                $csv .= $item->floors . $sep;
                $csv .= $params['house_type'][$item->house_type] . $sep;
               	$csv .= $item->total_area . $sep;
                $csv .= $item->living_area . $sep;
                $csv .= $item->kitchen_area . $sep;
                $csv .= $params['wc_type'][$item->wc_type] . $sep;
                $csv .= $params['loggia_type'][$item->loggia_type] . $sep;
                $csv .= htmler::YesNo($item->param_exclusive) . $sep; // Эксклюзив
                $csv .= htmler::YesNo($item->param_uglovaya) . $sep; //Угловая
		        $csv .= $params['param_pipes'][$item->param_pipes] . $sep; //Трубы
		        $csv .= $params['param_windows'][$item->param_windows] . $sep; //Окна
		        $csv .= $params['param_flooring'][$item->param_flooring] . $sep; //Полы
		        $csv .= $params['param_main_door'][$item->param_main_door] . $sep; //Входная дверь
		        $csv .= $params['param_room_doors'][$item->param_room_doors] . $sep; //Межкомнатные двери 
                $csv .= $item->rights . $sep;
                $csv .= $item->type_of_deal . $sep; //Альтернатива/Прямая продажа
                $csv .= $item->price . $sep;
                $csv .= $item->commission . $sep;
                $csv .= $item->agent_name . $sep;
                $csv .= $item->agent_phone . $sep; //Телефон
        		//$csv .= $item->comment . $sep; //Дополнительная информация

                $csv .= "\r\n";

			}

		} else {
	   		Main::Redirect('/admin/realty/objects', 'Вы не выбрали ни одного объекта для экспорта');
	   	}

		//$csv = iconv("Windows-1251", "UTF-8", $csv);

		$csv = mb_convert_encoding($csv, 'cp1251', 'UTF-8');

		echo $csv;		

	}

	public function autocomplite() {

		$data = array();

		if (isset($_GET['term']) && !empty($_GET['term']) && isset($_GET['field']) && !empty($_GET['field'])) {

			$term = $_GET['term'];
			$field = $_GET['field'];

			$model = $this->getModel('objects');

			$data = $model->getAutocompliteValues($field, $term);

			echo stripslashes(json_encode($data, JSON_UNESCAPED_UNICODE));	

		}

	}

	public function images_sort() {                  
        
        if (isset($_POST['item'])) {
	        
	        $ordering = $_POST['item'];

	        $model = $this->getModel('images');
	        
	        foreach ($ordering as $order => $id) {                
	            
	            $model->SaveItem($id, array('ordering' => $order));

	        }
        }

    }

	public function images_delete() {

		$args = Registry::get('route')->args;

		$model = $this->getModel('images');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		echo 'Элемент успешно удален';
	    	}
	    }

	}

	public function feedback() {

		if (empty($_POST['id']) or empty($_POST['text'])) {
			echo 'Ошибка сбора данных';
			return;
		}

		$item_id = intval($_POST['id']);		

		// Получаем данные по материалу
		$model = $this->getModel('objects');
		$item = $model->getItem($item_id);

		if (!isset($item->id)) {
			echo 'Ошибка сбора данных. Такого объекта не существует.';
			return;
		}

		// Получаем данные пользователя		
		$user = User::getUserData();

		// Получатель
		$mail_to = $item->author_email;
		//$mail_to = 'y.yurso@gmail.com';		

		// Отправитель
		$mail_from = $user->email;
		$name_from = $user->name;

		// Тема письма
		$subject = 'Вопрос по объекту "' . $item->adress . '"';

		// Текст письма 
		$text = trim($_POST['text']);

		// Заголовки
		$headers = 'From: ' . $name_from . '<'.$mail_from.'>' . "\r\n" .
				   'Reply-To: ' . $mail_from . "\r\n" .
				   'X-Mailer: PHP/' . phpversion();

		// Отправить письмо
		$success = mail($mail_to, $subject, $text, $headers);
		
		if ($success) {
			echo 'Сообщение успешно отправлено.';
		} else {
			echo 'Произошла ошибка при отправке сообщения. Попробуйте еще раз.';
		}

	}

	public function upload_image() {		

		$config = Registry::get('config');

		$result = array('success' => false, 'filename' => '', 'message' => '');			 
	 	// tmp folder for images
		$storeFolder = 'public' . DIRSEP . 'images' . DIRSEP . 'tmp' . DIRSEP;  
		// allowed file mime types
        $types = array('image/jpeg', 'image/gif', 'image/pjpeg', 'image/png');		        
		 
		if (!empty($_FILES)) {
			// checking filetype
			if (in_array($_FILES['file']['type'], $types)) {
		     
			    $tempFile = $_FILES['file']['tmp_name'];          //3             
			    $targetPath = SITE_PATH . $storeFolder;  //4

			    // get extensiton of file and change filename
			    $extension = strtolower(substr(strrchr($_FILES['file']['name'],'.'),1));
			    $filename = Main::generateCode(10).'.'.$extension;		      		    
			     
			    $targetFile =  $targetPath.$filename;
			    $thumbFile = $targetPath.'thumb_'.$filename;  //5
			 
			 	try {
			    	// moving images
			    	move_uploaded_file($tempFile,$targetFile);		 	
			    	// resize images
			    	list($w_i, $h_i, $type) = getimagesize($targetFile);
	                if (($config->realty_images_max_width > 0 && $w_i > $config->realty_images_max_width) || ($config->realty_images_max_height > 0 && $h_i > $config->realty_images_max_height)) {
	                    KhImages::resize($targetFile, $targetFile, $config->realty_images_max_width, $config->realty_images_max_height);    
	                }
	                // create small image
                	KhImages::resize($targetFile, $thumbFile, $config->realty_images_thumbs_width, $config->realty_images_thumbs_height);
	                // set result information
			    	$result['success'] = true;
			    	$result['filename'] = $filename;

				} catch (Exception $e) {
					$result['success'] = false;
					$result['message'] = "Не удалось переместить файл. Обратитесь к администратору.";
				}

			} else {
				$result['success'] = false;
				$result['message'] = "Вы не можете загружать файлы этого формата.";
			}

		}		     
		
		echo stripslashes(json_encode($result, JSON_UNESCAPED_UNICODE));		

	}

}