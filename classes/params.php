<?php
Class Params {    

    static public function setParamValue($param_name, $value) {

		$dbh = Registry::get('dbh');            

        $sth = $dbh->prepare("DELETE FROM `#__params` WHERE name = :param_name");
        
        $sth->execute(array('param_name' => $param_name));
        
        $sth = $dbh->prepare("INSERT INTO `#__params` (name, value, modify_date) VALUES (:name, :value, :modify_date)");

        $result = $sth->execute(
	        array(
	        	'name' => $param_name,
	        	'value' => $value,
	        	'modify_date' => date("Y-m-d H:i:s")
	        )
	    );

	    return $result;

    }

    static public function getParamData($param_name) {

    	$dbh = Registry::get('dbh');            
        
        $sth = $dbh->prepare("SELECT * FROM `#__params` WHERE name = :name");

        $sth->execute(array(
	      	'name' => $param_name
	    ));    

	    return $sth->fetch(PDO::FETCH_OBJ);

    }

    static public function getParamValue($param_name, $result = '') {

    	$dbh = Registry::get('dbh');            
        
        $sth = $dbh->prepare("SELECT value FROM `#__params` WHERE name = :name");

        $sth->execute(array(
	      	'name' => $param_name
	    ));    

	    $param_data = $sth->fetch(PDO::FETCH_OBJ);

	    if (isset($param_data->value)) {
	    	$result = $param_data->value;
	    }

	    return $result;

    }

}