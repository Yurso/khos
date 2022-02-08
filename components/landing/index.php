<?php
Class LandingIndexController Extends ControllerBase {

	public function index() {
		
		$model = $this->getModel('items');

		$model->setFilter('state', '>', 0);
		
		$items = $model->getItems();

		$types = $model->getTypesList();

		foreach ($items as $key => $item) {
			// Unserialize params to array
			$item->params = unserialize($item->params);
			// If this type not installed
			if (!in_array($item->type, $types)) {
				unset($items[$key]);
			}
			// Init type class 
			$type_class = $model->getTypeClass($item->type);
			// Save type view to item object
			$item->view = $type_class->getView($item);			
		}

		$tmpl = new Template;

		$tmpl->setVar('items', $items);

		$tmpl->display('index');

	}

}