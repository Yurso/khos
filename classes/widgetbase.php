<?php
Abstract Class WidgetBase {

	public $params = array();
	public $widget_name = '';

	abstract public function display();

	// function find a model file and return model class
	protected function getModel($name) {

		echo $this->widget_name;

		$file = SITE_PATH.'widgets'.DIRSEP.$this->widget_name.DIRSEP.'models'.DIRSEP.$name.'.php';

		if (is_file($file)) {
			
			include_once($file);

			$class = $this->widget_name.$name.'WModel';

			return new $class;	

		} else {

			trigger_error('Model "' . $name . '" not found in '.$this->widget_name.' widget path', E_USER_NOTICE);
			
			return false;

		}		

	}

}