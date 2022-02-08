<?php
Class LandingTypeBase {

	public function getView($item) {

		$route = Registry::get('route');

		$path_to_view = $route->path.'views'.DIRSEP.'type_'.$item->type.'.php';

		// include view file if it's exist or just return the content
		if (is_file($path_to_view)) {
			ob_start();
			include($path_to_view);
			$result = ob_get_contents();
			ob_end_clean();
		} else {
			$result = $item->content;
		}

		return $result;

	}

	public function getEditForm($item) {
		
		return '<textarea name="content></textarea>';

	}

	public function getParamsForm($params) {

		$param_name = 'show_title';

		if (!isset($params[$param_name])) {
			$params[$param_name] = 1;
		}

		$result = '
			<div class="block-item">
	            <label>Показывать заголовок:</label><br />
	            '.htmler::booleanSelect($params[$param_name], 'params['.$param_name.']').'
	        </div>';

		return $result;

	}

	public function onBeforeItemSave() {

	}

	public function onAfterItemSave($item) {

	}

	public function onItemDelete($item) {
		
	}	

	public function onItemDuplicate($item) {
		
	}

}