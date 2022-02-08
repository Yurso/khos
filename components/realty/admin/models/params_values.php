<?php
Class RealtyParamsValuesModel Extends ModelBase {

    public $table = "#__realty_params_values";

    // Defaults vars
    public $default_ordering = array('column' => 'ordering', 'sort' => 'ASC');


    public function getParamsList() {

    	$dbh = Registry::get('dbh');

		$query="SELECT * FROM `#__realty_params` WHERE state > 0";	            

        $sth = $dbh->prepare($query);

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

}