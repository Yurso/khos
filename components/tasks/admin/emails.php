<?php
Class TasksEmailsController Extends stdController {

	public $model_name = 'emails';

	public function index() {

		$model = $this->getModel('emails');
		$customers_model = $this->getModel('customers');
		// use ordering
		$model->initUserOrdering();				

		// FILTERS BY TITLE
		$filter = new Filter;
		$filter->name = 'email';
		$filter->column = 'tce.email';
		$filter->title = 'Email';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// FILTERS CUSTOMER
		$filter = new Filter;
		$filter->name = 'customer_id';
		$filter->column = 'tce.customer_id';
		$filter->title = 'Клиент';		
		$filter->setValues($customers_model->getItems(), 'id', 'name');		

		$model->_setFilter($filter);

		// Use pagination 
		$pagination = $model->initPagination();

		// Get items array
		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);		
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Адреса клиентов');
		
		$tmpl->display('emails');

	}

	public function create() {

		$customers_model = $this->getModel('customers');		
		$customers = $customers_model->getItems();		

		$item = new stdClass;
		$item->id = 0;
		$item->email = '';
		$item->customer_id = 0;

		$tmpl = new template;

		$tmpl->setVar('item', $item);
		$tmpl->setVar('customers', $customers);

		$tmpl->display('emails_edit');

	}

	public function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('emails');

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);

				$customers_model = $this->getModel('customers');
				$customers = $customers_model->getItems();

				$tmpl = new Template;

				$tmpl->setVar('item', $item);
				$tmpl->setVar('customers', $customers);

				$tmpl->display('emails_edit');

			} else {
				Main::redirect('/admin/tasks/emails', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/tasks/emails', 'Не указан id элемента');
		}

	}

}