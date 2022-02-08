<?php
Class ContentBlogController Extends ControllerBase {

	public $params = array(
		'image' => '',
		'class' => ''
	);

	function index() {

		$model = $this->getModel('blog');
		
		$model->initUserOrdering();

		// FILTERS ADRESS
		$filter = new Filter;
		$filter->name = 'title';
		$filter->column = 'b.title';
		$filter->title = 'Заголовок';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// FILTERS CATEGORY
		$filter = new Filter;
		$filter->name = 'category';
		$filter->column = 'b.category_id';
		$filter->title = 'Категория';
		$filter->setValues($model->getCategoriesList(), 'id', 'title');		

		$model->_setFilter($filter);

		// $model->addFilter('b.title', 'Заголовок');

		// // set categories filter
		// $categories = $model->getCategoriesList();		
		// $filter_values = array();
		// foreach ($categories as $cateogry) {
		// 	$filter_values[$cateogry->id] = $cateogry->title;
		// }
		// $model->addFilter('b.category_id', 'Категория', $filter_values);

		$pagination = $model->initPagination();

		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Блог');
		
		$tmpl->display('blog');

	}

	function create() {

		$data = new stdClass;
		$data->id = 0;
		$data->title = '';
		$data->alias = '';
		$data->category_id = 1;
		$data->state = 1;
		$data->content = '';
		$data->comments = '';
		$data->params = $this->params;
		$data->author_name = '';
		$data->tags = '';
		$data->create_date = '';
		$data->edit_date = '';
		$data->public_date = '';
		
		$model = $this->getModel('blog');

		$categories = $model->getCategoriesList();

		$template = new template;

		$template->setVar('data', $data);
		$template->setVar('categories', $categories);

		$template->display('blog_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('blog');

			if ($model->itemExist($id)) {

				$data = $model->getItem($id);

				if (isset($data->params)) 
					$data->params = array_merge($this->params, unserialize($data->params));

				$categories = $model->getCategoriesList();

				// $pathway = new Pathway;
				// $pathway->addItem('Блог', '/admin/content/blog');
				// $pathway->addItem($data->title, '');

				$template = new Template;

				$template->setVar('data', $data);
				$template->setVar('categories', $categories);

				$template->display('blog_edit');

			} else {
				Main::redirect('/admin/content/blog', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/content/blog', 'Не указан id элемента');
		}

	}

	function save() {

		$redirect = '';
		$message = '';

		$model = $this->getModel('blog');

		$id = (int) $_POST['id'];

		$user = new User;
		$userData = $user->getUserData();

		// Cheking fields
		if (empty($_POST['title'])) {
			if ($id == 0)
				Main::Redirect('/admin/content/blog/create', 'Заголовок не может быть пустым');
			elseif ($id > 0)
				Main::Redirect('/admin/content/blog/edit/' . $id, 'Заголовок не может быть пустым');
		}

		$params = array();

		if (isset($_POST['params'])) {
			$params = serialize($_POST['params']);
		}

		$data = array();

		$data['title'] = $_POST['title'];
		$data['category_id'] = $_POST['category_id'];
		$data['state'] = $_POST['state'];
		$data['content'] = $_POST['content'];
		$data['comments'] = $_POST['comments'];
		$data['params'] = $params;
		$data['tags'] = $_POST['tags'];		
		$data['edit_date'] = date("Y-m-d H:i:s");
		$data['public_date'] = (!empty($_POST['public_date'])) ? $_POST['public_date'] : date("Y-m-d H:i:s");

		if ($id == 0) {
			$data['author_id'] = $userData->id;	
			$data['create_date'] = date("Y-m-d H:i:s");
		} 

		if (empty($_POST['alias'])) {			
			$data['alias'] = $model->finishAlias(Main::str2url($data['title']), $id);	
		} else {
			$data['alias'] = $model->finishAlias($_POST['alias'], $id);	
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

		$redirect = '/admin/content/blog/edit/' . $id;

		if ($id == 0)
			$redirect = '/admin/content/blog/create'; 			

		// redirect to items list if user press save 
		if (isset($_POST['save'])) 
			$redirect = '/admin/content/blog/';		

		Main::Redirect($redirect, $message);

	}

	// Maybe somtime it will be ok
	function save_ajax() {

		$result = array('success' => false, 'messages' => array(), 'redirect' => '');

		$model = $this->getModel('blog');

		$id = (int) $_POST['id'];

		$user = new User;
		$userData = $user->getUserData();

		// Cheking fields
		if (empty($_POST['title'])) {			
			$result['messages'][] = 'Заголовок не может быть пустым';
		}

		$params = array();

		if (isset($_POST['params'])) {
			$params = serialize($_POST['params']);
		}

		$data = array();

		$data['title'] = $_POST['title'];	
		$data['category_id'] = $_POST['category_id'];
		$data['state'] = $_POST['state'];
		$data['content'] = $_POST['content'];
		$data['comments'] = $_POST['comments'];
		$data['params'] = $params;
		$data['tags'] = $_POST['tags'];
		$data['edit_date'] = date("Y-m-d H:i:s");
		$data['public_date'] = (!empty($_POST['public_date'])) ? $_POST['public_date'] : date("Y-m-d H:i:s");

		if (empty($_POST['alias'])) {			
			$data['alias'] = $model->finishAlias(Main::str2url($data['title']), $id);	
		} else {
			$data['alias'] = $model->finishAlias($_POST['alias'], $id);	
		}

		if ($id == 0) {
			$data['author_id'] = $userData->id;
			$data['create_date'] = date("Y-m-d H:i:s");		
		}

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($data);

			if ($id > 0) {
				$result['success'] = true;
				$result['messages'][] = 'Запись успешно сохранена.';
				$result['redirect'] = '/admin/content/blog/edit/'.$id;
			} else {
				$result['messages'][] = 'Ошибка! Не удалось сохранить запись.';
			}
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $data)) {
				$result['success'] = true;
				$result['messages'][] = 'Запись успешно сохранена.';
			} else {
				$result['messages'][] = 'Ошибка! Не удалось сохранить запись.';
			}

		}

		echo stripslashes(json_encode($result, JSON_UNESCAPED_UNICODE));

	}

	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('blog');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/content/blog', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/content/blog', 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/content/blog', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('blog');

			$user = new User;
			$user_data = $user->getUserData();

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);

				$data = array();
				$data['title'] 			= $item->title . ' (copy)';
				$data['alias'] 			= $model->finishAlias($item->alias);				
				$data['category_id'] 	= $item->category_id;
				$data['state'] 			= $item->state;
				$data['content'] 		= $item->content;
				$data['comments'] 		= $item->comments;
				$data['author_id']		= $user_data->id;
				$data['params'] 		= $item->params;
				$data['tags'] 			= $item->tags;

				$data['create_date'] = date("Y-m-d H:i:s");
				$data['edit_date'] = date("Y-m-d H:i:s");
				$data['public_date'] = date("Y-m-d H:i:s");

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/content/blog', 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/admin/content/blog', 'Нечего копировать.');
			}

		}

	}

	function upload_image() {
		// This is a simplified example, which doesn't cover security of uploaded images.
		// This example just demonstrate the logic behind the process.
		 
		// files storage folder
		$dir = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'blog' . DIRSEP;

		mkdir($dir.DIRSEP.'thumbs'.DIRSEP, 0755, true);
		 
		$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		 
		if ($_FILES['file']['type'] == 'image/png'
		|| $_FILES['file']['type'] == 'image/jpg'
		|| $_FILES['file']['type'] == 'image/gif'
		|| $_FILES['file']['type'] == 'image/jpeg'
		|| $_FILES['file']['type'] == 'image/pjpeg')
		{
		    // setting file's mysterious name
		    $filename = md5(date('YmdHis')).'.jpg';
		    $file = $dir.$filename;
		    
		    // copying
		    if (move_uploaded_file($_FILES['file']['tmp_name'], $file)) {
		    	KhImages::resize($file, $file, 962, 0);
		    	KhImages::resize($file, $dir.'thumbs'.DIRSEP.$filename, 200, 0);
		    }
		 
		    // displaying file
		    $array = array(
		        'filelink' => '/public/images/blog/'.$filename
		    );
		 
		    echo stripslashes(json_encode($array));
		 
		}
	}

	function upload_file() {

		$dir = SITE_PATH . 'public' . DIRSEP . 'files' . DIRSEP;

		$_FILES['file']['type'] = strtolower($_FILES['file']['type']);

		if ($_FILES['file']['type'] == 'image/png'
		|| $_FILES['file']['type'] == 'image/jpg'
		|| $_FILES['file']['type'] == 'image/gif'
		|| $_FILES['file']['type'] == 'image/jpeg'
		|| $_FILES['file']['type'] == 'image/pjpeg'
		|| $_FILES['file']['type'] == 'application/zip'
		|| $_FILES['file']['type'] == 'application/pdf')
		{

			move_uploaded_file($_FILES['file']['tmp_name'], $dir.$_FILES['file']['name']);
	 
			$array = array(
			    'filelink' => '/public/files/'.$_FILES['file']['name'],
			    'filename' => $_FILES['file']['name']
			);
			 
			echo stripslashes(json_encode($array));
		}

	}

	function uploaded_images() {

		$result = array();

		$dir = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'blog' . DIRSEP;
		$dir_thumbs = $dir . 'thumbs' . DIRSEP;
		
		$url = '/public/images/blog/';
		$url_thumbs = $url.'thumbs/';

		$filelist = scandir($dir);

		foreach ($filelist as $filename) {

			if (is_dir($dir.$filename)) continue;

			if (!is_file($dir_thumbs.$filename))
				KhImages::resize($dir.$filename, $dir_thumbs.$filename, 200, 0);
			
			$file = array();			

			$file['thumb'] = $url_thumbs.$filename;
			$file['image'] = $url.$filename;
			//$file['title'] = $filename;
			$file['folder'] = $url;

			$result[] = $file;
		}

		echo stripslashes(json_encode($result));

	}

	function uploaded_images2() {

		$result = array();

		$dir = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP;

		$filelist = KhFiles::ScanFiles($dir, '*.*', true);

		foreach ($filelist as $fileitem) {
			$file = array();			

			$file['thumb'] = $fileitem['folder'].$fileitem['name'];
			$file['image'] = $fileitem['folder'].$fileitem['name'];
			//$file['title'] = $filename;
			$file['folder'] = $fileitem['folder'];

			$result[] = $file;
		}

		echo stripslashes(json_encode($result));

	}

}