<?php
Class CommentsModel Extends ModelBase {

	public $table = '#__comments';

	public $default_ordering = array('column' => 'create_date', 'sort' => 'ASC');
    
    // public function getItems($params = array()) {

    //     $dbh = Registry::get('dbh');

    //     $query  = $this->_buildItemsQuery(); 
    //     //$query .= $this->_buildItemsWhere();
    //     if (count($params)) {            
    //         $i = 0;
    //         $query .= " WHERE";
    //         foreach ($params as $key => $value) {                
    //             if ($i>0) $query .= " AND";
    //             $query .= " $key = :$key";
    //             $i++;
    //         }            
    //     }

    //     $query .= $this->_buildItemsOrder();
    //     $query .= $this->_buildItemsLimit();                          

    //     $sth = $dbh->prepare($query);

    //     $sth->execute($params);

    //     $data = $sth->fetchAll(PDO::FETCH_OBJ);

    //     return $data;

    // }

    protected function _buildItemsOrder() {

        return " ORDER BY create_date ASC";

    }

	public function getActiveControllers() {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__components`
                            WHERE type = 'controller' AND state > 0 AND name NOT LIKE 'admin/%'
                            ORDER BY name");

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }


    public function checkAccess($controller) {

        $result = false;

        $dbh = Registry::get('dbh');

        $sth=$dbh->prepare("SELECT access
                            FROM `#__components`
                            WHERE type = 'controller' AND name = :name");

        $sth->execute(array('name' => $controller));

        $data = $sth->fetch(PDO::FETCH_OBJ);

        // if component registred
        if (isset($data->access)) {
            
            $user = new User;

            if ($user->checkUserAccess($data->access))
                $result = true;

        }

        return $result;

    }

}