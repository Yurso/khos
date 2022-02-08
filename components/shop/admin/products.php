<?php
Class ShopProductsController Extends ControllerBase {

	function index() {
		
		$model = $this->getModel('products');

		$model->initUserOrdering();

		$model->addFilter('title', 'Заголовок');

		$categories = $model->getCategoriesList();

		$pagination = $model->initPagination();

		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->getFilters());
		$tmpl->setVar('categories', $categories);

		$tmpl->display('products');
	}

	function create() {

		$model = $this->getModel('products');

		$categories = $model->getCategoriesList();
		$units = $model->getUnitsList();

		$item = new stdClass;
		$item->id = 0;
		$item->title = '';
		$item->alias = '';
		$item->description = '';
		$item->price = 0;
		$item->state = 1;
		$item->images = array();			
		$item->unit_title = '';

		$tmpl = new template;

		$tmpl->setVar('item', $item);
		$tmpl->setVar('categories', $categories);
		$tmpl->setVar('units', $units);	

		$tmpl->display('products_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('products');

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);
				$categories = $model->getCategoriesList($id);
				$units = $model->getUnitsList();

				$images_model = $this->getModel('products_images');
				$item->images = $images_model->getItems(array('product_id' => $id));

				$tmpl = new Template;

				$tmpl->setVar('item', $item);
				$tmpl->setVar('categories', $categories);				
				$tmpl->setVar('units', $units);	

				$tmpl->display('products_edit');

			} else {
				Main::redirect('/admin/shop/products', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/shop/products', 'Не указан id элемента');
		}

	}

	function save() {

		$model = $this->getModel('products');

		$id = (int) $_POST['id'];

		$user = new User;
		$userData = $user->getUserData();

		if (empty($_POST['alias'])) {			
			$alias = $model->finishAlias(Main::str2url($_POST['title']), $id);	
		} else {
			$alias = $model->finishAlias($_POST['alias'], $id);	
		}

		$data = array();

		$data['title'] = $_POST['title'];
		$data['alias'] = $alias;
		$data['description'] = $_POST['description'];
		$data['price'] = (int) $_POST['price'];
		$data['state'] = (int) $_POST['state'];	
		$data['unit_id'] = (int) $_POST['unit_id'];

		$images_model = $this->getModel('products_images');

		$success = false;

		// Save item
		if ($id == 0) {
			$id = $model->SaveNewItem($data);
			if ($id > 0) $success = true;
		} else {
			$success = $model->SaveItem($id, $data);
		}

		// If item successful saved
		if ($success) {

			// Save categories
			if (!$model->SaveItemCategories($id, $_POST['categories']))
				Main::setMessage('Произошла ошибка при записи категорий. Попробуйте сохранить запись еще раз.');			
			
			// Save images
			$images_model->SaveUploadedImages($id);

			// Save an defult image to products table
			$images = $images_model->getItems(array('product_id' => $id));
			// If we have images get first
			if (isset($images[0]->id)) {
				$data = array();
				$data['default_image_id'] = $images[0]->id;
				$model->SaveItem($id, $data);
			}

			// Redirecting
			Main::Redirect('/admin/shop/products/edit/' . $id, 'Запись успешно сохранена.');

		} else {

			if ($id > 0) {
				Main::Redirect('/admin/shop/products/edit/' . $id, 'Ошибка! Не удалось сохранить запись.');	
			} else {
				Main::Redirect('/admin/shop/products/create', 'Ошибка! Не удалось сохранить запись.');
			}			

		}

	}

	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('products');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/shop/products', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/shop/products', 'Успешно удалено ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/shop/products', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('products');

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);

				$data = array();
				$data['title'] = $item->title . ' (copy)';	
				$data['alias'] = $model->finishAlias($item->alias);			
				$data['description'] = $item->description;
				$data['price'] = $item->price;
				$data['state'] = $item->state;								

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/shop/products', 'Успешно скопировано ' . $i . ' элементов.');
			} else {
				Main::Redirect('/admin/shop/products', 'Нечего копировать.');
			}

		} else {
			Main::Redirect('/admin/shop/products', 'Нечего копировать.');
		}

	}

	function images_sort() {                  
        
        if (isset($_POST['item'])) {
	        
	        $ordering = $_POST['item'];

	        $model = $this->getModel('products_images');
	        
	        foreach ($ordering as $order => $id) {                
	            
	            $model->SaveItem($id, array('ordering' => $order));

	        }
        }

    }

	function images_delete() {

		$args = Registry::get('route')->args;

		$model = $this->getModel('products_images');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		echo 'Элемент успешно удален';
	    	}
	    }

	}

}