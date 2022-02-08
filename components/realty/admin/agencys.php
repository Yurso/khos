<?php
Class RealtyAgencysController Extends ControllerBase {

	function index() {

		$model = $this->getModel('agencys');

		$model->initUserOrdering();

		$pagination = $model->initPagination();

		$items = $model->getItems();

		// $pathway = new Pathway;
		// $pathway->addItem('Недвижимость', '/admin/realty/objects');		
		// $pathway->addItem('Агенства', '');

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);		
		
		$tmpl->display('agencys');

	}

	function create() {

		$data = new stdClass;
		$data->id = 0;
		$data->name = '';
		$data->full_name = '';
		$data->adress = '';
		$data->description = '';
		$data->logo = '';
		$data->state = 1;
		
		$model = $this->getModel('agencys');
		$users = $model->getUsersList();

		$template = new template;

		$template->setVar('data', $data);
		$template->setVar('users', $users);

		$template->display('agencys_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('agencys');

			if ($model->itemExist($id)) {

				$data = $model->getItem($id);
				$users = $model->getUsersList($id);

				$template = new Template;

				$template->setVar('data', $data);
				$template->setVar('users', $users);

				$template->display('agencys_edit');

			} else {
				Main::redirect('/admin/realty/agencys', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/realty/agencys', 'Не указан id элемента');
		}

	}

	function save() {		

		$redirect = '';
		$message = '';

		$model = $this->getModel('agencys');

		$id = (int) $_POST['id'];

		$data = array();
		$data['name'] = $_POST['name'];
		$data['full_name'] = $_POST['full_name'];
		$data['adress'] = $_POST['adress'];
		$data['description'] = $_POST['description'];		
		$data['state'] = $_POST['state'];

		// Saving logo file
		$logo = $_FILES['logo'];
		$logo_types = array('image/jpeg', 'image/gif', 'image/pjpeg', 'image/png');		
		$path = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'realty_agencys' . DIRSEP;
		$url_path = '/public/images/realty_agencys/';

		if ($logo['error'] == UPLOAD_ERR_OK && in_array($logo['type'], $logo_types)) {

			if (!is_dir($path)) {            
	            mkdir($path, 0755, true);
        	}

			$filename = $id.'-'.Main::str2url($logo['name']);

			if (move_uploaded_file($logo['tmp_name'], $path.$filename)) {
				$data['logo'] = $url_path.$filename;
			}

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

		// if ($id != 0) {
		// 	$model->SaveUsersList($id, $_POST['users']);
		// }
			
		$redirect = '/admin/realty/agencys/edit/' . $id;

		if ($id == 0)
			$redirect = '/admin/realty/agencys/create'; 			

		// redirect to items list if user press save 
		if (isset($_POST['save'])) 
			$redirect = '/admin/realty/agencys/';		

		Main::Redirect($redirect, $message);

	}

	
	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('agencys');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/realty/agencys', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/realty/agencys', 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/realty/agencys', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('agencys');

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);

				$data = array();
				$data['name'] = $item->name;
				$data['full_name'] = $item->full_name;
				$data['adress'] = $item->adress;
				$data['description'] = $item->description;
				$data['logo'] = $item->logo;
				$data['state'] = $item->state;

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/realty/agencys', 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/admin/realty/agencys', 'Нечего копировать.');
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