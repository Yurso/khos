<?php
Class RealtyCustomersModel Extends ModelBase {

    public $table = "#__realty_customers";

    // Defaults vars
    public $default_ordering = array('column' => 'name', 'sort' => 'ASC');

    public function getItemsByName($name, $user_id = 0) {

    	$dbh = Registry::get('dbh');

		$query="SELECT *, concat(name, ' (', phone, ')') AS value
				FROM `#__realty_customers`
                WHERE name LIKE :name";	   

        if ($user_id > 0) {
            $query .= " AND user_id = $user_id";
        }         

        $sth = $dbh->prepare($query);

        $sth->execute(array('name' => $name.'%'));

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function checkItemAccess($item_id) { 

        if ($item_id == 0) return true;

        $dbh = Registry::get('dbh');
        $user = User::getUserData();

        if ($user->access_name == 'administrator') 
            return true;

        $query="SELECT user_id
                FROM `#__realty_customers` 
                WHERE id = :id";             

        $sth = $dbh->prepare($query);
        
        $sth->execute(array('id' => $item_id));
        
        $item = $sth->fetch(PDO::FETCH_OBJ);

        if (isset($item->user_id) && $item->user_id == $user->id)
            return true;

        return false;

    }

}