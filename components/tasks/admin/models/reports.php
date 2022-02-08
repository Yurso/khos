<?php
Class TasksReportsModel Extends ModelBase {

    public $table = "#__tasks_reports";

    public function getItem($id = array()) {

    	$item = parent::getItem($id);

    	if (empty($item->params)) {
			$item->params = array();
		} else {
			$item->params = unserialize($item->params);
		}
		
		if (empty($item->columns)) {
			$item->columns = array();
		} else {
			$item->columns = unserialize($item->columns);
		}

		return $item;

    }

}