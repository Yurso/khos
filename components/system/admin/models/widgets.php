<?php
Class SystemWidgetsModel Extends ModelBase {

	public $table = '#__widgets';

	public $default_ordering = array('column' => 'ordering', 'sort' => 'ASC');

	public function getWidgetsList() {

		$dbh = Registry::get('dbh');

		$sth = $dbh->query("SELECT *
							FROM `#__components`
							WHERE type = 'widget' AND state > 0
							ORDER BY name ASC");

		return $sth->fetchAll(PDO::FETCH_OBJ);

	}

	public function getItem($params = array()) {
		
		$item = parent::getItem($params);
		
		$item->params = unserialize($item->params);
		
		return $item;
		
	}

	public function getWidgetParams($name) {

		$dbh = Registry::get('dbh');

		$sth = $dbh->prepare("SELECT params
							  FROM `#__components`
							  WHERE type = 'widget' AND name = :name");
		
		$sth->execute(array('name' => $name));

		$data = $sth->fetch(PDO::FETCH_OBJ);

		return unserialize($data->params);

	}

}