<?php
Class ContentGalleryController Extends ControllerBase {

	public function index() {
		
		// get model
		$model = $this->getModel('gallery_images');

		$model->initUserOrdering();

		// get additional data
		$categories = $model->getCategoriesList();		

		// FILTERS CATEGORY
		$filter = new Filter;
		$filter->name = 'title';
		$filter->column = 'gi.title';
		$filter->title = 'Заголовок';		
        $filter->operator = 'LIKE';
        
		$model->_setFilter($filter);

		// FILTERS CATEGORY
		$filter = new Filter;
		$filter->name = 'category';
		$filter->column = 'gi.category_id';
		$filter->title = 'Категория';
		$filter->setValues($categories, 'id', 'title');	
		$model->_setFilter($filter);

		$pagination = $model->initPagination();

		// get items list
		$items = $model->getItems();

		// set templates var and display result
		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('categories', $categories);		
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Галерея');

		$tmpl->display('gallery_images');
	}

	public function create() {

		$model = $this->getModel('gallery_images');

		$item = new stdClass;
		$item->id = 0;
		$item->title = '';
		$item->description = '';
		$item->pathway = '';
		$item->filename = '';
		$item->state = 1;
		$item->category_id = 0;

		$categories = $model->getCategoriesList();

		$tmpl = new template;		
		$tmpl->setVar('item', $item);
		$tmpl->setVar('categories', $categories);
		$tmpl->display('gallery_images_edit');

	}

	public function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('gallery_images');

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);
				$categories = $model->getCategoriesList();

				$tmpl = new Template;
				$tmpl->setVar('item', $item);
				$tmpl->setVar('categories', $categories);
				$tmpl->display('gallery_images_edit');

			} else {
				Main::redirect('/admin/content/gallery', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/content/gallery', 'Не указан id элемента');
		}

	}

	public function save() {

		$model = $this->getModel('gallery_images');

		$id = (int) $_POST['id'];

		$data = array();

		$data['title']			= $_POST['title'];
		$data['description']	= $_POST['description'];
		$data['pathway']		= $_POST['pathway'];
		$data['filename']		= $_POST['filename'];
		$data['state']			= $_POST['state'];
		$data['ordering']		= 0;		
		$data['category_id']	= $_POST['category_id'];

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($data);

			if ($id > 0) {
				Main::Redirect('/admin/content/gallery/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/content/gallery', 'Ошибка! Не удалось сохранить запись.');
			}
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $data)) {
				Main::Redirect('/admin/content/gallery/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/content/gallery/edit/' . $id, 'Ошибка! Не удалось сохранить запись.');
			}

		}

	}

	public function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('gallery_images');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/content/gallery', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/content/gallery', 'Успешно удалено ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/content/gallery', 'Не указан id элемента');
	   	}
		
	}

	public function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('gallery_images');

			$user = new User;
			$user_data = $user->getUserData();

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);

				$data = array();
				$data['title'] = $item->title . ' (copy)';				
				$data['description'] = $item->description;
				$data['pathway'] = $item->pathway;
				$data['filename'] = $item->filename;
				$data['state'] = $item->state;				
				$data['ordering'] = $item->ordering;
				$data['category_id'] = $item->category_id;			

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/content/gallery', 'Успешно скопировано ' . $i . ' элементов.');
			} else {
				Main::Redirect('/admin/content/gallery', 'Нечего копировать.');
			}

		} else {
			Main::Redirect('/admin/content/gallery', 'Нечего копировать.');
		}

	}

	public function multiplesave() {

		$model = $this->getModel('gallery_images');

		if ($_POST['category_id'] < 1) {
			Main::Redirect('/admin/content/gallery', 'Не указана категория для сохранения изображений.');
            return;
		}

		$category_id = (int) $_POST['category_id'];

		$images = $_FILES['images'];

		print_r($images);

        // allowed file mime types
        $types = array('image/jpeg', 'image/gif', 'image/pjpeg');
        
        // generate path name
        $path = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'gallery' . DIRSEP;
        $thumbs_path = $path . 'thumbs' . DIRSEP;
        $url_path = '/public/images/gallery/';

        // create path if path not exist
        if (!is_dir($thumbs_path)) {
            
            if (!mkdir($thumbs_path, 0755, true)) {
                Main::setMessage('Не удалось создать категорию для загрузки изображений.');
                return;
            }

        }

        $i = 0;

        // process file list
        foreach ($images["error"] as $key => $error) {

            if ($error != UPLOAD_ERR_OK) continue;

            if (!in_array($images['type'][$key], $types)) continue;
            
            $tmp_name = $images["tmp_name"][$key];

            $extension = strtolower(substr(strrchr($images["name"][$key],'.'),1));

            $name = $category_id . '-' . Main::generateCode(10).'.'.$extension;

            // if name already exist
            while (is_file($path.$name)) {
            	$name = Main::generateCode(10).'.'.$extension;
            }

            // if file saved successful
            if (move_uploaded_file($tmp_name, $path.$name)) {

                $params = array();
				$params['title']		= $images["name"][$key];
				$params['description']	= '';
				$params['pathway']		= $url_path;
				$params['filename']		= $name;
				$params['state']		= 1;
				$params['ordering']		= 0;		
				$params['category_id']	= $category_id;

                if (!$model->SaveNewItem($params)) {
                    unlink($path.$name);
                    Main::setMessage('Не удалось создать запись в базе данных по файлу ' . $images["name"][$key] . '. Файл не будет сохранен.');    
                }

                // create small image
                $model->image_resize($path.$name, $thumbs_path.$name, 300, 0);

                $i++;

            }

        }

       	Main::Redirect('/admin/content/gallery', 'Успешно загружено '.$i.' изображений.');

	}

    public function sort() {                  
        
        if (isset($_POST['item'])) {
	        
	        $ordering = $_POST['item'];

	        $model = $this->getModel('gallery_images');
	        
	        foreach ($ordering as $order => $id) {                
	            
	            $model->SaveItem($id, array('ordering' => $order));

	        }
        }

    }

}

