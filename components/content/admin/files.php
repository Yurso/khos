<?php
Class ContentFilesController Extends ControllerBase {
	
	public function index() {

		// $dir = SITE_PATH . 'public';
		// $base_url = '/public';

		// $files1 = scandir($dir);
		// $files2 = array();

		// foreach ($files1 as $filename) {

		// 	$file = new stdClass;
		// 	$file->name = $filename;
		// 	$file->path = $base_url.'/'.$filename;
		// 	$file->fullpath = $dir.DIRSEP.$filename;
			
		// 	if (is_dir($file->fullpath)) {
		// 		$file->type = 'dir';
		// 	} else {
		// 		$file->type = 'file';
		// 	}

		// 	$files2[] = $file;

		// }

		$tmpl = new template;

		//$tmpl->setVar('files', $files2);
		
		$tmpl->display('files');

	}

	public function scan() {

		$base_url = '/public';
		$base_dir = SITE_PATH . 'public';

		// check sub dir value
		$sub_dir = '';
		if (isset($_GET['sub_dir'])) {
			$sub_dir = $_GET['sub_dir'];
		}

		$sub_dir = str_replace($base_dir.DIRSEP, '', realpath($base_dir.DIRSEP.$sub_dir));

		// base dir always should be in realpath
		if (strripos(realpath($base_dir.DIRSEP.$sub_dir), $base_dir) === false) {
			$sub_dir = '';
		}

		// set current values
		$current_dir = realpath($base_dir.DIRSEP.$sub_dir);
		$current_url = str_replace(SITE_PATH, '/', $current_dir);

		// scan current dir and unset /. folder
		$files = scandir($current_dir);
		array_shift($files);

		$result = array();

		// collect all information in result array
		foreach ($files as $filename) {

			$fullpath = $current_dir.DIRSEP.$filename;

			$file = new stdClass;
			// name of file
			$file->name = $filename;
			// dir or file
			if (is_dir($fullpath)) {
				$file->type = 'dir';
			} else {
				$file->type = 'file';
			}
			// file public url
			$file->url = $current_url.'/'.$filename;
			// file fullpath
			$file->fullpath = $fullpath;
			// file subdir (value without base_dir)
			$file->sub_dir = $sub_dir.DIRSEP.$filename;
			// file extension
			$file->ext = $ext = substr(strrchr($filename,'.'), 1);

			$result[] = $file;

		}

		// encode result to json
		echo json_encode($result);

	}

	public function upload() {

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