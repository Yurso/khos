<?php 
Class Filter {

	public $name = '';
	public $column = '';
	public $title = '';
	public $values = array();
	public $value = '';
	public $first_empty_value = true;
	public $operator = '=';
	public $type = 'string';
	public $empty_value = '';
	public $advansed = false;
	public $hidden = false;

	// Generate values from object or array
	public function setValues($items, $key = 'key', $value = 'value') {

		foreach ($items as $item) {

			if (gettype($item) == 'array') {

				$this->values[$item[$key]] = $item[$value];

			} else {

				$this->values[$item->$key] = $item->$value;	

			}			

		}

	}

	// Sets defaut value of filter
	public function setDefault($value) {

		$filters = Main::getState('filters', array());

		if (!isset($filters[$this->name])) {
			
			$filters[$this->name] = $value;
			
			Main::setState('filters', $filters);

		}

	}

}