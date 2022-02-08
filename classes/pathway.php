<?php
Class Pathway {

	private $items = array();

	function __construct() {

		Registry::set('pathway', array());

	}

	public function addItem($title, $url = '') {

		$item = new stdClass;

		$item->url = $url;
		$item->title = $title;

		$this->items[] = $item;

		Registry::set('pathway', $this->items);

	}

	public function clear() {

		Registry::set('pathway', array());

	}

}