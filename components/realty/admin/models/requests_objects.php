<?php
Class RealtyRequestsObjectsModel Extends ModelBase {

    public $table = "#__realty_requests_objects";

    // Defaults vars
    //public $default_ordering = array('column' => 'name', 'sort' => 'ASC');

    public function clearRequestObjects($requset_id) {

		$dbh = Registry::get('dbh');

    	$sth = $dbh->prepare("DELETE FROM `#__realty_requests_objects` WHERE `request_id` = :requset_id");        
        
        $sth->execute(array('requset_id' => $requset_id));

    }

    public function checkItemAccess($item_id) { 

        if ($item_id == 0) return true;

        $dbh = Registry::get('dbh');
        $user = User::getUserData();

        if ($user->access_name == 'administrator') 
            return true;

        $query="SELECT user_id
                FROM `#__realty_requests` 
                WHERE id = :id";             

        $sth = $dbh->prepare($query);
        
        $sth->execute(array('id' => $item_id));
        
        $item = $sth->fetch(PDO::FETCH_OBJ);

        if (isset($item->user_id) && $item->user_id == $user->id)
            return true;

        return false;

    }

}