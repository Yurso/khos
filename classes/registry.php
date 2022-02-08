<?php
Class Registry {

	static public $reg = array();

	public static function set($name, $object) {

		self::$reg[$name] = $object;

	}

	public static function get($name) {

		if (isset(self::$reg[$name])) {
			return self::$reg[$name];
		} else {
			return null;
		}

	}

	public static function isReg($name) {

	 	return isset(self::$reg[$name]);

	 }

}