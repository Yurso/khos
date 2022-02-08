<?php
Class SystemSessionsController Extends ControllerBase {

	function index() {
		
		$model = $this->getModel('sessions');

		$model->initUserOrdering();

		$users_model = $this->getModel('users');				

		// FILTERS CATEGORY
		$filter = new Filter;
		$filter->name = 'user_id';
		$filter->column = 's.user_id';
		$filter->title = 'Пользователь';
		$filter->setValues($users_model->getItems(), 'id', 'name');	
		
		$model->_setFilter($filter);

		$pagination = $model->initPagination();

		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Активные сессии');

		$tmpl->display('sessions');
	}

	function create() {

		$item = new stdClass;
		$item->id = 0;
		$item->user_id = 0;
		$item->hash = '';
		$item->ip = 1;
		$item->start_date = date("Y-m-d H:i:s");
		$item->active_date = date("Y-m-d H:i:s");
		$item->last_page = '';

		$tmpl = new template;

		$tmpl->setVar('item', $item);

		$tmpl->display('sessions_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('sessions');

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);

				$tmpl = new Template;

				$tmpl->setVar('item', $item);

				$tmpl->display('sessions_edit');

			} else {
				Main::redirect('/admin/system/sessions', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/system/sessions', 'Не указан id элемента');
		}

	}

	function save() {

		$model = $this->getModel('sessions');

		$id = (int) $_POST['id'];

		$params = array();
		$params['user_id'] = $_POST['user_id'];
		$params['hash'] = $_POST['hash'];
		$params['ip'] = $_POST['ip'];
		$params['start_date'] = $_POST['start_date'];
		$params['active_date'] = $_POST['active_date'];
		$params['last_page'] = $_POST['last_page'];

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($params);

			if ($id > 0) {
				Main::Redirect('/admin/system/sessions/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/system/sessions', 'Ошибка! Не удалось сохранить запись.');
			}
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $params)) {
				Main::Redirect('/admin/system/sessions/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/system/sessions/edit/' . $id, 'Ошибка! Не удалось сохранить запись.');
			}

		}

	}

	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('sessions');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/system/sessions', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/system/sessions', 'Успешно удалено ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/system/sessions', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('sessions');

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);

				$params = array();
				$params['user_id'] = $item->user_id;
				$params['hash'] = $item->hash;
				$params['ip'] = $item->ip;
				$params['start_date'] = $item->start_date;
				$params['active_date'] = $item->active_date;
				$params['last_page'] = $item->last_page;			

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/system/sessions', 'Успешно скопировано ' . $i . ' элементов.');
			} else {
				Main::Redirect('/admin/system/sessions', 'Нечего копировать.');
			}

		} else {
			Main::Redirect('/admin/system/sessions', 'Нечего копировать.');
		}

	}

}