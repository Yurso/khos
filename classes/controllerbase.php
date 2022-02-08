<?php
Abstract Class ControllerBase {

	// require function in all conrollers
	abstract public function index();

	// function find a model file and return model class
	protected function getModel($name) {

		$route = Registry::get('route');

		$file = $route->path . DIRSEP . 'models' . DIRSEP . $name . '.php';

		if (is_file($file)) {
			
			include_once($file);

			$class = str_replace("_", "", $route->component.$name.'Model');

			return new $class;	

		} else {

			trigger_error('Model "' . $name . '" not found in component path', E_USER_ERROR);
			
			return false;

		}		

	}

}