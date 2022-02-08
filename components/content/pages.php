	<?php
Class ContentPagesController Extends ControllerBase {

	function index() {

	}

	function item() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('pages');

			if ($model->itemExist($id)) {

				$item = $model->getItem($id);

				$tmpl = new Template;
				$tmpl->setTitle($item->title);
				$tmpl->setVar('item', $item);
				$tmpl->display('pages_item');
				
			}

		}
		
	}

	function homebridge() {

		$id = 2;
		
		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$model = $this->getModel('pages');

			$item = $model->getItem($id);

			$params = array();
			$params['content'] = $item->content . "event ".date("Y-m-d H:i:s").": ".$args[0]."\n";

			$model->saveItem($id, $params);

		}

	}

	function actions() {

		$user = new User;

		if ($user->getUserAccessName() == 'administrator') {

			$data = array();

			$term = (isset($_GET['term'])) ? $_GET['term'] : '';

			$model = $this->getModel('pages');

			$items = $model->getItems();

			foreach ($items as $key => $item) {

				$data_item = array(
					'value' => 'item/' . $item->id . '-' . $item->alias,
					'label' => $item->id . ' - ' . $item->title,	
					'category' => 'Items'
				);
				
				if (!empty($term) && stripos($data_item['value'], $term) === false && stripos($data_item['label'], $term) === false) continue;

				$data[] = $data_item;

			}

			echo stripslashes(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

	}

}