<?php
Class TasksItemsController Extends ControllerBase {

	public function index() {

		$model = $this->getModel('items');
		
		$model->initUserOrdering();		

		$customers_model = $this->getModel('customers');

		$types_model = $this->getModel('types');

		// FILTERS ADRESS
		$filter = new Filter;
		$filter->name = 'title';
		$filter->column = 'i.title';
		$filter->title = 'Заголовок';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// FILTERS CUSTOMER
		$filter = new Filter;
		$filter->name = 'customer_id';
		$filter->column = 'i.customer_id';
		$filter->title = 'Клиент';		
		$filter->setValues($customers_model->getItems(), 'id', 'name');		

		$model->_setFilter($filter);

		// FILTERS TYPES
		$filter = new Filter;
		$filter->name = 'type_id';
		$filter->column = 'i.type_id';
		$filter->title = 'Вид работ';		
		$filter->setValues($types_model->getItems(), 'id', 'title');		

		$model->_setFilter($filter);

		// FILTER STATUS
		$filter = new Filter;
		$filter->name = 'status';
		$filter->column = 'i.status';
		$filter->title = 'Статус задачи';
		$filter->operator = '=';
		$filter->values = $model->getStatuses();		

		$model->_setFilter($filter);

		$model->initPagination();
		
		// Get items array
		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $model->pagination);
		$tmpl->setVar('filters', $model->filters);
		$tmpl->setVar('statuses', $model->getStatuses());

		$tmpl->setTitle('Список задач');
		
		$tmpl->display('items');

	}

	public function create() {

		$items_model = $this->getModel('items');
		$customers_model = $this->getModel('customers');
		$types_model = $this->getModel('types');		
		//$mail_model = $this->getModel('mail');
		$user = Registry::get('user');

		$title = '';
		$description = '';
		
		$message_msgno = '';
		$message_id = '';
		$message_reply_to = '';
		$message_subject = '';
		$message_attachments = array();

		$types = $types_model->getItems();

		$item = new stdClass;
		$item->id = 0;
		$item->title = $title;
		$item->type_id = $types[1]->id;
		$item->date = date("Y-m-d H:i:s");
		$item->state = 0;
		$item->count = 1;
		$item->price = $types[1]->default_price;
		$item->customer_id = 0;
		$item->project_id = 0;
		$item->url = '';
		$item->comment = '';
		$item->paid = 0;
		$item->paid_date = '0000-00-00';
		$item->description = $description;
		$item->message_msgno = $message_msgno;
		$item->message_id = $message_id;
		$item->message_reply_to = $message_reply_to;
		$item->message_subject = $message_subject;
		$item->message_attachments = $message_attachments;
		$item->status = 'new';
		$item->author_name = $user->name;
		$item->messages = array();

		$tmpl = new template;
		$tmpl->setVar('item', $item);
		$tmpl->setVar('customers', $customers_model->getItems());
		$tmpl->setVar('types', $types);
		$tmpl->setVar('statuses', $items_model->getStatuses());

		$tmpl->addScript('/public/js/com_tasks/items_edit.js');

		$tmpl->setTitle('Редактор задач');

		$tmpl->display('items_edit');

	}

	public function view() {

		$args = Registry::get('route')->args;

		if (!isset($args[0])) {
			Main::redirect('/admin/tasks/items', 'Не указан id элемента');
		}

		$id = intval($args[0]);

		$model = $this->getModel('items');

		if (!$model->itemExist($id)) {
			Main::redirect('/admin/tasks/items', 'Ошибка! Элемент с таким id не найден.');
		}

		$item = $model->getItem($id);

		$customers_model = $this->getModel('customers');
		$types_model = $this->getModel('types');
		$files_model = $this->getModel('files');
		$messages_model = $this->getModel('messages');				
		
		$files_model->setFilter('task_id', '=', $id);
		$item->files = $files_model->getItems();
		$item->filespath = Params::getParamValue('tasks_files_path', 'tmp/');				

		$messages_model->setFilter('task_id', '=', $id);
		$item->messages = $messages_model->getItems();

		$tmpl = new Template;
		$tmpl->setVar('item', $item);
		$tmpl->setVar('customers', $customers_model->getItems());
		$tmpl->setVar('types', $types_model->getItems());
		$tmpl->setVar('statuses', $model->getStatuses());

		$tmpl->setTitle('Просмотр задачи "'.$item->title.'"');

		$tmpl->display('items_view');

	}

	public function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('items');

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);

				$customers_model = $this->getModel('customers');
				$types_model = $this->getModel('types');
				$files_model = $this->getModel('files');
				$messages_model = $this->getModel('messages');				
				
				$files_model->setFilter('task_id', '=', $id);
				$item->files = $files_model->getItems();
				$item->filespath = Params::getParamValue('tasks_files_path', 'tmp/');				

				$messages_model->setFilter('task_id', '=', $id);
				$item->messages = $messages_model->getItems();

				$tmpl = new Template;
				$tmpl->setVar('item', $item);
				$tmpl->setVar('customers', $customers_model->getItems());
				$tmpl->setVar('types', $types_model->getItems());
				$tmpl->setVar('statuses', $model->getStatuses());

				$tmpl->addScript('/public/js/com_tasks/items_edit.js');

				$tmpl->setTitle('Редактор задач');

				$tmpl->display('items_edit');

			} else {
				Main::redirect('/admin/tasks/items', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/tasks/items', 'Не указан id элемента');
		}

	}

	public function save() {

		if (isset($_GET['type']) && $_GET['type'] == 'ajax') {
			echo 'hello world';
			exit();
		}

		$redirect = '';
		$message = '';
		$params = array();
		
		$id = (int) $_POST['id'];
		$m_items = $this->getModel('items');	
		$m_messages = $this->getModel('messages');

		if ($id > 0) {
			$item = $m_items->getItem($id);
		}

		$columns = $m_items->getTableColumns();	

		foreach ($columns as $column) {
			if (isset($_POST[$column->Field])) {				
				if (gettype($_POST[$column->Field]) == 'array') {
					$params[$column->Field] = serialize($_POST[$column->Field]);
				} else {
					$params[$column->Field] = trim($_POST[$column->Field]);
				}
			}
		}	

		// if status changed
		if (isset($item) && isset($_POST['status']) && $item->status != $_POST['status']) {

			if ($_POST['status'] == 'paid' && !isset($_POST['paid_date'])) {
				$params['paid_date'] = date("Y-m-d H:i:s");
			}

			if ($_POST['status'] == 'paid' && isset($_POST['paid_date']) && $_POST['paid_date'] == '0000-00-00 00:00:00') {
				$params['paid_date'] = date("Y-m-d H:i:s");
			}

			if ($_POST['status'] == 'complete' && !isset($_POST['complete_date'])) {
				$params['complete_date'] = date("Y-m-d H:i:s");
			}

			if ($_POST['status'] == 'complete' && isset($_POST['complete_date']) && $_POST['complete_date'] == '0000-00-00 00:00:00') {
				$params['complete_date'] = date("Y-m-d H:i:s");
			}

		}

		if (isset($_POST['save_complete'])) {
			//$params['state'] = 1;
			$params['complete_date'] = date("Y-m-d H:i:s");	
			$params['status'] = 'complete';
		}		

		if (isset($_POST['save_paid'])) {
			//$params['state'] = 1;
			//$params['paid'] = 1;
			$params['paid_date'] = date("Y-m-d H:i:s");	
			$params['status'] = 'paid';
		}

		if (isset($_POST['save_delete'])) {
			//$params['state'] = 1;
			//$params['deleted'] = 1;
			$params['status'] = 'canceled';
		}

		// If it's new element
		if ($id == 0) {

			$id = $m_items->SaveNewItem($params);

			if ($id > 0) {
				$message = 'Запись успешно сохранена.';				
				$m_messages->addMessage($id, $_POST['description']);
			} else {
				$message = 'Ошибка! Произошла ошибка базы даных. Не удалось сохранить запись.';			
			}
			
		} elseif ($id > 0) {

			if ($m_items->SaveItem($id, $params)) 				
				$message = 'Запись успешно сохранена.';	
			else				
				$message = 'Ошибка! Не удалось сохранить запись.';			

		}

		// Save files
		if ($id > 0 && count($_FILES)) {
			$files_model = $this->getModel('files');
			$files_model->saveTaskFilesByInputName($id, 'attachments');
		} 

		$redirect = '/admin/tasks/items/edit/' . $id;

		// if id still 0 go to create form
		if ($id == 0) { $redirect = '/admin/tasks/items/create'; }

		// redirect to items list if user press save 
		if (isset($_POST['save_complete']) || isset($_POST['save']) || isset($_POST['save_paid']) || isset($_POST['save_delete'])) { 
			$redirect = '/admin/tasks/items'; 
		}

		if (!empty($_POST['ref']) && !isset($_POST['apply'])) {
			$redirect = $_POST['ref'];
		}

		$mail_model = $this->getModel('mail');

		if (isset($item) && isset($_POST['autoanswer_create']) && $_POST['autoanswer_create'] == 'on') {			

			$from = 'y.yurso@gmail.com';
			$to = $item->message_reply_to;
			$subject = $item->message_subject;
			$msg = $_POST['autoanswer'];

			$mail_model->send_message($from, $to, $subject, $msg);			

		}

		if (isset($item) && isset($_POST['message_unflag']) && $_POST['message_unflag'] == 'on') {
			$mail_model->unFlagMessage($item->message_msgno);
		}

		Main::Redirect($redirect, $message);

	}

	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('items');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/tasks/items', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/tasks/items', 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/tasks/items', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('items');

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);

				$params = array();
					
				$params['date'] = date("Y-m-d H:i:s");
				$params['title'] = $item->title;
				$params['type_id'] = $item->type_id;
				$params['state'] = $item->state;
				$params['customer_id'] = $item->customer_id;
				$params['count'] = $item->count;
				$params['price'] = $item->price;
				$params['url'] = $item->url;
				$params['comment'] = $item->comment;
				$params['description'] = $item->description;
				$params['message_msgno'] = $item->message_msgno;
				$params['message_id'] = $item->message_id;
				$params['message_subject'] = $item->message_subject;
				$params['message_reply_to'] = $item->message_reply_to;

				if ($model->SaveNewItem($params)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/tasks/items', 'Успешно скопировано ' . $i . ' ' . Main::declension_by_number('элемент', $i));
			} else {
				Main::Redirect('/admin/tasks/items', 'Нечего копировать.');
			}

		}

	}

	function paided() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('items');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];

	    	$params = array();
	    	$params['paid'] = 1;
	    	$params['paid_date'] = date("Y-m-d H:i:s");

	    	if ($model->saveItem($id, $params)) {
	    		Main::Redirect('/admin/tasks/items', 'Элемент успешно изменен');
	    	}

	   	} elseif (isset($_POST['checked'])) {

	   		foreach ($_POST['checked'] as $key => $value) {
	   			
	   			$id = (int) $value;

	   			$params = array();
		    	$params['paid'] = 1;
		    	$params['paid_date'] = date("Y-m-d H:i:s");

	   			if ($model->saveItem($id, $params)) {
	    			$i++;
	    		} else {
	    			Main::setMessage('Не удалось изменить элемент id = ' . $id);
	    		}

	   		}

	   		Main::redirect('/admin/tasks/items', 'Успешно изменено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/tasks/items', 'Не указан id элемента');
	   	}

	}

	function download_all_files() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('items');

			if ($model->itemExist($id)) {

				$files_model = $this->getModel('files');
				$files_model->setFilter('task_id', '=', $id);
				$files = $files_model->getItems();
				$filespath = SITE_PATH.'public'.DIRSEP.'files'.DIRSEP.'tasks'.DIRSEP;

				$zip = new ZipArchive();

				$zipfileName = SITE_PATH.'public'.DIRSEP.'tmp'.DIRSEP.'archive_'.date('j_m_Y_h_i_s').'.zip';
				if ($zip->open($zipfileName, ZIPARCHIVE::CREATE) !== true) {
				    echo "Error while creating archive file\n";
				    exit(1);
				}

				foreach ($files as $file) {
					$zip->addFile($filespath.$file->filename, $file->filename);
				}

				$zip->close();
 
				//echo "Archive created\n";
				
			    if (file_exists($zipfileName)) {
				    // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
				    // если этого не сделать файл будет читаться в память полностью!
				    if (ob_get_level()) {
				      ob_end_clean();
				    }
				    // заставляем браузер показать окно сохранения файла
				    header('Content-Description: File Transfer');
				    header('Content-Type: application/octet-stream');
				    header('Content-Disposition: attachment; filename=' . basename($zipfileName));
				    header('Content-Transfer-Encoding: binary');
				    header('Expires: 0');
				    header('Cache-Control: must-revalidate');
				    header('Pragma: public');
				    header('Content-Length: ' . filesize($zipfileName));
				    // читаем файл и отправляем его пользователю
				    readfile($zipfileName);
				    exit;
			  	}
				

			}
		}

	}

}