<?php
Class RealtyAgencysModel Extends ModelBase {

    public $table = "#__realty_agencys";

    // Defaults vars
    public $default_ordering = array('column' => 'name', 'sort' => 'ASC');

    public function getUsersList($agency_id = 0) {

    	$dbh = Registry::get('dbh');

		$query="SELECT name 
				FROM `#__users`
                WHERE agency_id = :agency_id";	            

        $sth = $dbh->prepare($query);

        $sth->execute(array('agency_id' => $agency_id));

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    // public function SaveUsersList($agency_id, $users = array()) {

    // 	$dbh = Registry::get('dbh');

    // 	$result = true;

    // 	// Delete all first
    // 	$sth=$dbh->prepare("DELETE FROM `#__realty_agencys_users`
    // 						WHERE `agency_id` = :agency_id");

    // 	$sth->execute(array('agency_id' => $agency_id));

    // 	foreach ($users as $value) {

    // 		$sth=$dbh->prepare("INSERT INTO `#__realty_agencys_users` (agency_id, user_id)
    // 							VALUES (:agency_id, :user_id)");

    // 		if (!$sth->execute(array('agency_id' => $agency_id, 'user_id' => $value))) {
    // 			$result = false;
    // 		}

    		
    // 	}

    // 	return $result;

    // }

}