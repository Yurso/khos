<?php
Class TasksCustomersModel Extends ModelBase {

    public $table = "#__tasks_customers";

    // Defaults vars
    //public $default_ordering = array('column' => 'name', 'sort' => 'ASC');

    public function getItemsByName($name, $user_id = 0) {

    	$dbh = Registry::get('dbh');

		$query="SELECT *, concat(name, ' (', phone, ')') AS value
				FROM `#__tasks_customers`
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
                FROM `#__tasks_customers` 
                WHERE id = :id";             

        $sth = $dbh->prepare($query);
        
        $sth->execute(array('id' => $item_id));
        
        $item = $sth->fetch(PDO::FETCH_OBJ);

        if (isset($item->user_id) && $item->user_id == $user->id)
            return true;

        return false;

    }

    public function getCustomerByEmail($email) {

        $dbh = Registry::get('dbh');

        $customer_id = 0;

        $query="SELECT customer_id
                FROM `#__tasks_customers_emails` 
                WHERE email = :email AND state > 0";             

        $sth = $dbh->prepare($query);
        
        $sth->execute(array('email' => $email));
        
        $item = $sth->fetch(PDO::FETCH_OBJ);

        if (isset($item->customer_id) && $item->customer_id > 0) {
            $customer_id = $item->customer_id;
        }

        return $customer_id;

    }

}