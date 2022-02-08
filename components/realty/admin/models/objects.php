<?php
Class RealtyObjectsModel Extends ModelBase {

    public $table = "#__realty";

    // Defaults vars
    public $default_ordering = array('column' => 'r.last_edit', 'sort' => 'DESC');

    protected function _buildItemQuery() {

        $query  = $this->_buildItemsQuery();

        $query .= " WHERE r.id = :id";

        return $query;

    }

    protected function _buildItemsQuery() {

		$query="SELECT 
                    r.*, 
                    users.name AS author_name, 
                    users.email AS author_email,
                    categories.title AS category_title,
                    agencys.name AS agency_name,
                    agencys.logo AS agency_logo
                FROM 
                    `$this->table` AS r 
                LEFT JOIN `#__users` AS users
                    ON r.author_id = users.id
                LEFT JOIN `#__categories` AS categories
                    ON r.category_id = categories.id
                LEFT JOIN `#__realty_agencys` AS agencys
                    ON r.agency_id = agencys.id";	            

        return $query;

	}

    // protected function _buildItemsWhere() {

    //     $query = parent::_buildItemsWhere();

    //     $query .= " AND r.deleted = 0";

    //     return $query;

    // }

    public function deleteItem($id) {

        $dbh = Registry::get('dbh');

        $item = $this->getItem($id);

        // If item already marked as deleted then delete line from db. Else only mark as deleted
        if ($item->deleted) {            

            $result = parent::deleteItem($id);

            // CLEARING ALL ITEM IMAGES

            // geting object images list from db
            $sth = $dbh->prepare("SELECT * FROM `#__realty_images` WHERE `object_id` = :id");        
            $sth->execute(array('id' => $id));
            $images = $sth->fetchAll(PDO::FETCH_OBJ);

            // images path
            $path = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'realty' . DIRSEP;
            $thumbs_path = $path . 'thumbs' . DIRSEP;

            // unlinking files
            foreach ($images as $image) {
                unlink($path.$image->image_name);
                unlink($thumbs_path.$image->image_name);
            }

            // delete records from db
            $sth = $dbh->prepare("DELETE FROM `#__realty_images` WHERE `object_id` = :id");        
            $sth->execute(array('id' => $id));

        } else {            
            
            $params = array('id' => $id);        
            $query = "UPDATE `#__realty` SET deleted = 1 WHERE id = :id";        
            $sth = $dbh->prepare($query);            
            $result = $sth->execute($params);

        }

        return $result;

    }

	public function getAgencysList() {

		$dbh = Registry::get('dbh');

		$query="SELECT * FROM `#__realty_agencys` WHERE state > 0";	            

        $sth = $dbh->prepare($query);

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_OBJ);

	}

    public function getHouseTypes() {

        $dbh = Registry::get('dbh');

        $query="SELECT * FROM `#__realty_house_types` WHERE state > 0";             

        $sth = $dbh->prepare($query);

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_OBJ);
        
    }

    public function getParams() {

        $params = array();

        $dbh = Registry::get('dbh');

        // Get the whole list of params
        $query="SELECT name 
                FROM `#__realty_params` 
                WHERE state > 0";             

        $sth = $dbh->prepare($query);

        $sth->execute();

        $items = $sth->fetchAll(PDO::FETCH_OBJ);

        foreach ($items as $key => $item) {
            $params[$item->name] = array();
        }             

        // Get params values
        $query="SELECT 
                    realty_params_values.*,
                    realty_params.name AS param_name  
                FROM 
                    `#__realty_params_values` AS realty_params_values 
                LEFT JOIN 
                    `#__realty_params` AS realty_params
                ON 
                    realty_params_values.param_id = realty_params.id 
                WHERE 
                    realty_params_values.state > 0
                    AND realty_params.state > 0
                ORDER BY
                    realty_params_values.ordering ASC";             

        $sth = $dbh->prepare($query);

        $sth->execute();

        $items = $sth->fetchAll(PDO::FETCH_OBJ);

        foreach ($items as $item) {

            $params[$item->param_name][$item->value] = $item->title;
                        
        }

        return $params;
        
    }

    public function getAutocompliteValues($field_name, $term) {

        $dbh = Registry::get('dbh');

        $query="SELECT $field_name AS value 
                FROM `#__realty` 
                WHERE state > 0 AND $field_name LIKE :term
                GROUP BY $field_name  
                LIMIT 10";             

        $sth = $dbh->prepare($query);

        $sth->execute(array('term' => $term . '%'));

        return $sth->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getUserAgencys() {        

        $dbh = Registry::get('dbh');
        $user = User::getUserData(); 

        if ($user->access_name == 'administrator')
            return $this->getAgencysList();        

        $query="SELECT *
                FROM `#__realty_agencys`
                WHERE id = :id";             

        $sth = $dbh->prepare($query);

        $sth->execute(array('id' => $user->agency_id));

        return $sth->fetchAll(PDO::FETCH_OBJ);        

    }

    public function checkItemAccess($item_id, $use_agencies = false) { 

        if ($item_id == 0) return true;

        $dbh = Registry::get('dbh');
        $user = User::getUserData();

        if ($user->access_name == 'administrator') 
            return true;

        $query="SELECT author_id, agency_id 
                FROM `#__realty` 
                WHERE id = $item_id";             

        $sth = $dbh->prepare($query);
        $sth->execute();
        $item = $sth->fetch(PDO::FETCH_OBJ);

        if (isset($item->author_id) && $item->author_id == $user->id)
            return true;

        if ($use_agencies && isset($item->agency_id) && $item->agency_id == $user->agency_id)
            return true;  

        return false;

    }

    public function getItemsByList($id_list) {

        $dbh = Registry::get('dbh');

        $query = $this->_buildItemsQuery();

        $query .= " WHERE r.id IN (";

        $i = 0;

        foreach ($id_list as $id) {

            $i++;
            
            $query .= (int) $id;

            if ($i < count($id_list)) $query .= ", ";            

        }            

        $query .= ")"; 

        $query .= $this->_buildItemsOrder();       

        $sth = $dbh->prepare($query);

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function getAgentInfo($id) {

        $dbh = Registry::get('dbh');

        $user = user::getUserData();

        $query="SELECT 
                    agencys.*
                FROM 
                    `#__realty_agencys` AS agencys            
                WHERE 
                    agencys.id = $user->agency_id";

        $sth = $dbh->prepare($query);
        
        $sth->execute();

        $user->agency = $sth->fetch(PDO::FETCH_OBJ);

        return $user;

    }

    public function getItemsByDate($date_from, $date_before) {

        $dbh = Registry::get('dbh');

        $params = array(
            'date_from' => $date_from,
            'date_before' => $date_before
        );

        $query="SELECT 
                    r.id,
                    r.adress,
                    u.id AS author_id,
                    u.name,
                    u.email
                FROM `#__realty` AS r
                LEFT JOIN `#__users` AS u
                ON r.author_id = u.id
                WHERE r.last_edit >= :date_from 
                AND r.last_edit <= :date_before
                AND r.archive = 0
                AND r.deleted = 0";

        $sth = $dbh->prepare($query);
        
        $sth->execute($params);

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }


}