<?php
Class MenuMenusController Extends ControllerBase {

	function index() {		
		
		$model = $this->getModel('menus');

		$model->initUserOrdering();

		$pagination = $model->initPagination();

		$items = $model->getItems();
		
		$template = new Template;

		$template->setVar('items', $items);		
		$template->setVar('pagination', $pagination);

		$template->display('menus');

	}

	function create() {

		$args = Registry::get('route')->args;

		$item = new stdClass;
		$item->id = 0;
		$item->name = '';		
		$item->description = '';

		$template = new template;

		$template->setVar('item', $item);

		$template->display('menus_edit');
		
	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('menus');

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);

				// $pathway = new Pathway;
				// $pathway->addItem('Менеджер пунктов меню', '/admin/menu/menus/items/items/' . $item->id);
				// $pathway->addItem('Менеджер меню', '/admin/menu/menus');
				// $pathway->addItem($item->name, '');

				$template = new template;

				$template->setVar('item', $item);

				$template->display('menus_edit');

			} else {
				Main::redirect('/admin/menu/menus', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/menu/menus', 'Не указан id элемента');
		}

	}

	function save() {

		$model = $this->getModel('menus');

		$id = (int) $_POST['id'];

		$data = array();		
		$data['name'] = $_POST['name'];
		$data['description'] = $_POST['description'];

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($data);

			if ($id > 0) {
				Main::Redirect('/admin/menu/menus/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/menu/menus/items/' . $data['menu_id'], 'Ошибка! Не удалось сохранить запись.');
			}
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $data)) {
				Main::Redirect('/admin/menu/menus/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/menu/menus/edit/' . $id, 'Ошибка! Не удалось сохранить запись.');
			}

		}

	}

	function delete() {
		
		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('menu');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/menu/menus', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/menu/menus', 'Успешно удалено ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/menu/menus', 'Не указан id элемента');
	   	}

	}

}