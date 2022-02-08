<?php
Class TasksBillsController Extends ControllerBase {

	public function index() {

		$model = $this->getModel('bills');
		
		$model->initUserOrdering();		

		// FILTERS ADRESS
		$filter = new Filter;
		$filter->name = 'title';
		$filter->column = 'title';
		$filter->title = 'Заголовок';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// FILTERS STATE
		$filter = new Filter;
		$filter->name = 'state';
		$filter->column = 'state';
		$filter->title = 'Статус';
		$filter->operator = '<';
		$filter->first_empty_value = false;
		$filter->values = array(1 => 'Нет', 2 => 'Да');
		$filter->value = 2;

		$model->_setFilter($filter);	

		$model->initPagination();
		
		// Get items array
		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $model->pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Список счетов');
		
		$tmpl->display('bills');

	}

	public function create() {

		$customers_model = $this->getModel('customers');
		$items_model = $this->getModel('items');

		$customers = $customers_model->getItems();

		// set template variables
		$tmpl = new template;
		$tmpl->setVar('customers', $customers);
		$tmpl->setVar('statuses', $items_model->getStatuses());
		$tmpl->setTitle('Новый счет на оплату');
		$tmpl->display('bill_new');

	}

	public function _create() {

		//$items_model = $this->getModel('bills');
		$customers_model = $this->getModel('customers');
		$types_model = $this->getModel('types');

		$item = new stdClass;
		$item->id = 0;
		$item->title = '';
		$item->create_date = date("Y-m-d H:i:s");
		$item->modify_date = date("Y-m-d H:i:s");
		$item->description = '';
		$item->state = 1;		
		$item->params = array();

		$tmpl = new template;
		$tmpl->setVar('item', $item);
		$tmpl->setVar('tasks', array());
		$tmpl->setVar('customers', $customers_model->getItems());
		$tmpl->setVar('types', $types_model->getItems());

		$tmpl->setTitle('Новый счет');

		$tmpl->display('bills_edit');

	}

	public function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('bills');

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);

				$customers_model = $this->getModel('customers');
				$types_model = $this->getModel('types');

				$tmpl = new Template;
				$tmpl->setVar('item', $item);
				$tmpl->setVar('tasks', $model->getBillItems($id));
				$tmpl->setVar('customers', $customers_model->getItems());
				$tmpl->setVar('types', $types_model->getItems());

				$tmpl->setTitle('Редактор счетов');

				$tmpl->display('bills_edit');

			} else {
				Main::redirect('/admin/tasks/bills', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/tasks/bills', 'Не указан id элемента');
		}

	}

	public function save() {

		$redirect = '';
		$message = '';

		$model = $this->getModel('bills');

		$id = (int) $_POST['id'];

		$params = array();
		
		if (empty($_POST['create_date'])) {
			$params['create_date'] = date("Y-m-d H:i:s");
		} else {
			$params['create_date'] = $_POST['create_date'];
		}

		$params['modify_date'] = date("Y-m-d H:i:s");
		$params['title'] = $_POST['title'];
		$params['description'] = $_POST['description'];
		$params['state'] = $_POST['state'];
		$params['paid'] = $_POST['paid'];
		$params['sum'] = $model->getBillSum($id);

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($params);

			if ($id > 0) 
				$message = 'Запись успешно сохранена.';				
			else
				$message = 'Ошибка! Произошла ошибка базы даных. Не удалось сохранить запись.';			
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $params)) 				
				$message = 'Запись успешно сохранена.';	
			else				
				$message = 'Ошибка! Не удалось сохранить запись.';			

		}

		$redirect = '/admin/tasks/bills/edit/' . $id;

		// if id still 0 go to create form
		if ($id == 0) { $redirect = '/admin/tasks/bills/create'; }

		// redirect to items list if user press save 
		if (isset($_POST['save'])) { $redirect = '/admin/tasks/bills'; }

		Main::Redirect($redirect, $message);

	}

	public function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('bills');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/tasks/bills', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/tasks/bills', 'Успешно удалено ' . $i . ' ' . Main::declension_by_number('элемент', $i));

	   	} else {
	   		Main::Redirect('/admin/tasks/bills', 'Не указан id элемента');
	   	}
		
	}

	public function paid() {

		$redirect = '/admin/tasks/bills';
		$message = 'Произошла ошибка при выполнении операции';

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$bills_model = $this->getModel('bills');
			$items_model = $this->getModel('items');

			$bill_id = intval($args[0]);

			$bill_items = $bills_model->getBillItems($bill_id);

			foreach ($bill_items as $item) {
				$params = array();
				//$params['paid'] = 1;
				$params['paid_date'] = date("Y-m-d H:i:s");
				$params['status'] = 'paid';
				$items_model->SaveItem($item->id, $params);
			}

			$params = array();
			$params['paid'] = 1;
			$bills_model->SaveItem($bill_id, $params);

			$redirect = '/admin/tasks/bills/edit/'.$bill_id;
			$message = 'Счет успешно оплачен';

		}

		Main::Redirect($redirect, $message);

	}

}