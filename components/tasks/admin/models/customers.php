<?php
Class TasksCustomersModel Extends ModelBase {

    public $table = "#__tasks_customers";

    public function deleteItem($id) {
        
        $dbh = Registry::get('dbh');

        // Check if customer exist and get the name
        $query="SELECT name
                FROM `#__tasks_customers`
                WHERE id = ?";       
        $sth = $dbh->prepare($query);
        $sth->execute(array($id));
        $data = $sth->fetch(PDO::FETCH_OBJ);

        if (!isset($data->name)) {
            Main::setMessage('Клиента с id = '.$id.' не существует.');
            return false;
        }

        $customer_name = $data->name;

        // Check if customer has tasks
        $query="SELECT count(id) AS records
                FROM `#__tasks_items`
                WHERE customer_id = ?";
        $sth = $dbh->prepare($query);
        $sth->execute(array($id));
        $data = $sth->fetch(PDO::FETCH_OBJ);

        if ($data->records) {            
            Main::setMessage('Невозможно удалить клиента '.$customer_name.', т.к. с ним связаны задачи');
            return false;
        }
        
        // If all is ok delete the record
        $sth = $dbh->prepare("DELETE FROM `$this->table` WHERE `id` =  :id");                    
        $result = $sth->execute(array('id' => $id));       

        return $result;
    }

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
                WHERE email = :email";             

        $sth = $dbh->prepare($query);
        
        $sth->execute(array('email' => $email));
        
        $item = $sth->fetch(PDO::FETCH_OBJ);

        if (isset($item->customer_id) && $item->customer_id > 0) {
            $customer_id = $item->customer_id;
        }

        return $customer_id;

    }

    public function SaveCustomerEmails($customer_id, $values) {

        $dbh = Registry::get('dbh');

        // Delete old records
        $params = array();
        $params['customer_id'] = $customer_id;

        $query="DELETE FROM `#__tasks_customers_emails` WHERE customer_id = :customer_id";             

        $sth = $dbh->prepare($query);

        $sth->execute($params);

        foreach ($values['email'] as $key => $value) {
            
            $params = array();
            $params['customer_id'] = $customer_id;
            $params['email'] = $values['email'][$key];
            $params['description'] = $values['description'][$key];
            $params['state'] = $values['state'][$key];

            $query = "INSERT INTO `#__tasks_customers_emails` (customer_id, email, description, state) VALUES (:customer_id, :email, :description, :state)";
            
            $sth = $dbh->prepare($query);

            $sth->execute($params);

        }

    }

}