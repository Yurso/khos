<?php
Class ShopUnitsController Extends ControllerBase {

	function index() {		
		
		$model = $this->getModel('units');

		$model->initUserOrdering();

		$pagination = $model->initPagination();

		$items = $model->getItems();
		
		$pathway = new Pathway;
		$pathway->addItem('Магазин', '/admin/shop/products');
		$pathway->addItem('Менеджер единиц измерения', '');
		
		$template = new Template;

		$template->setVar('items', $items);		
		$template->setVar('pagination', $pagination);

		$template->display('units');

	}

	function create() {		

		$item = new stdClass;	
		$item->id = 0;
		$item->title = '';		
		$item->description = '';
		$item->state = 1;

		$template = new template;

		$template->setVar('item', $item);

		$template->display('units_edit');
		
	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('units');

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);

				$pathway = new Pathway;
				$pathway->addItem('Магазин', '/admin/shop');
				$pathway->addItem('Менеджер единиц измерения', '/admin/shop/units');
				$pathway->addItem($item->title, '');

				$template = new template;

				$template->setVar('item', $item);

				$template->display('units_edit');

			} else {
				Main::redirect('/admin/shop/units', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/shop/units', 'Не указан id элемента');
		}

	}

	function save() {

		$model = $this->getModel('units');

		$id = (int) $_POST['id'];

		$data = array();		
		$data['title'] = $_POST['title'];
		$data['description'] = $_POST['description'];
		$data['state'] = $_POST['state'];

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($data);

			if ($id > 0) {
				Main::Redirect('/admin/shop/units/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/shop/units/', 'Ошибка! Не удалось сохранить запись.');
			}
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $data)) {
				Main::Redirect('/admin/shop/units/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/shop/units/edit/' . $id, 'Ошибка! Не удалось сохранить запись.');
			}

		}

	}

	function delete() {
		
		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('units');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/shop/units', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/shop/units', 'Успешно удалено ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/shop/units', 'Не указан id элемента');
	   	}

	}

}