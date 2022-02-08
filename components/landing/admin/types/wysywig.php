<?php
Class LandingWysywigType extends LandingTypeBase {

	public function getEditForm($item) {

		$result =  '<div class="block-item">
			            <label>Текст:</label><br />
			            <textarea class="wysywig" name="content">'.$item->content.'</textarea>
			        </div>';

		return $result;

	}

	public function getParamsForm($params) {

		// SHOW TITLE
		$param_name = 'show_title';

		if (!isset($params[$param_name])) {
			$params[$param_name] = 1;
		}

		$result = '
			<div class="block-item">
	            <label>Показывать заголовок:</label><br />
	            '.htmler::booleanSelect($params[$param_name], 'params['.$param_name.']').'
	        </div>';

	    // FULLSCREEN
	    $param_name = 'fullscreen';

		if (!isset($params[$param_name])) {
			$params[$param_name] = 0;
		}

		$result .= '
			<div class="block-item">
	            <label>Во весь экран:</label><br />
	            '.htmler::booleanSelect($params[$param_name], 'params['.$param_name.']').'
	        </div>';

	    // CLASS
	    $param_name = 'class';

		if (!isset($params[$param_name])) {
			$params[$param_name] = '';
		}

		$result .= 
			'<div class="block-item">
	            <label>Класс блока:</label><br />
	            <input type="text" name="params['.$param_name.']" value="'.$params[$param_name].'" />
	        </div>';

	    // ID
	    $param_name = 'id';

		if (!isset($params[$param_name])) {
			$params[$param_name] = '';
		}

		$result .= 
			'<div class="block-item">
	            <label>id блока:</label><br />
	            <input type="text" name="params['.$param_name.']" value="'.$params[$param_name].'" />
	        </div>';

	    // BLOCK ATTRS
	    $param_name = 'attrs';

		if (!isset($params[$param_name])) {
			$params[$param_name] = '';
		}

		$result .= 
			'<div class="block-item">
	            <label>Атрибуты блока:</label><br />
	            <input type="text" name="params['.$param_name.']" value="'.htmlspecialchars($params[$param_name]).'" />
	        </div>';

		return $result;

	}

}