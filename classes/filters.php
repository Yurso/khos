<?php
Class Filters {

	private $filters = array();
	
	public function add($column, $title, $value = '') {

		$filters = Main::getState('filters', array());

		// if filter isset in state then replace the value
		if (isset($filters[$column])) {
			$value = $filters[$column]['value'];
		}

		// if filter isset in post term then replace the value
		if (isset($_POST['filters'][$column])) {
			$value = $_POST['filters'][$column];
		} 

		$filter = array(
			'value' => $value,
			'title' => $title
		);

		$filters[$column] = $filter;

		Main::setState('filters', $filters);
		
	}

	public function addSelect($column, $title, $values = array()) {

		$filters = Main::getState('filters', array());

		if (isset($filters[$column])) {
			$value = $filters[$column]['value'];
		}

		if (isset($_POST['filters'][$column])) {
			$value = $_POST['filters'][$column];
		} 

		$filter = array(
			'value' => $value,
			'values' => $values,
			'title' => $title,
			'type'  => 'select'
		);

		$filters[$column] = $filter;

		Main::setState('filters', $filters);

	}

	public function display() {

		$result  = '<div class="table-filters">';
		$result .= '	<form method="post">';

		$filters = Main::getState('filters', array());

		foreach ($filters as $column => $value) {
			
			$result .= '		<label>'.$column.'</label>';
			$result .= '		<input type="text" name="filters['.$column.']" value="'.$value.'" />';

		}

		$result .= '	</form>';
		$result .= '</div>';

		echo $result;

	}

}