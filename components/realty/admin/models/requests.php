<?php
Class RealtyRequestsModel Extends ModelBase {

    public $table = "#__realty_requests";

    // Defaults vars
    public $default_ordering = array('column' => 'create_date', 'sort' => 'DESC');

    protected function _buildItemQuery() {

        return $this->_buildItemsQuery() . " WHERE r.id = :id";

    }

    protected function _buildItemsQuery() {

		$query="SELECT 
                    r.*, 
                    users.name AS user_name, 
                    users.email AS user_email,
                    c.name AS customer_name,
                    c.adress AS customer_adress,
                    c.phone AS customer_phone,
                    c.email AS customer_email
                FROM 
                    `$this->table` AS r 
                LEFT JOIN `#__users` AS users
                    ON r.user_id = users.id
                LEFT JOIN `#__realty_customers` AS c
                    ON c.id = r.customer_id";	            

        return $query;

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

    public function saveReqParams($request_id, $params = array()) {

        $dbh = Registry::get('dbh');

        // Deleting all existing records
        $sth = $dbh->prepare("DELETE FROM `#__realty_requests_params` WHERE `request_id` = :request_id");                     
        $sth->execute(array('request_id' => $request_id));        

        // List all new values
        foreach ($params as $parameter => $value) {

            $sth = $dbh->prepare("INSERT INTO `#__realty_requests_params` (request_id, parameter, value, type) VALUES (:request_id, :parameter, :value, :type)");

            $type = gettype($value);

            if ($type == 'array') {
                $value = serialize($value);
            }

            $sth->execute(array(
                'request_id' => $request_id,
                'parameter' => $parameter,
                'value' => $value,
                'type' => $type
            ));

        }

    }

    public function getReqParams($request_id, $params = array()) {

        $dbh = Registry::get('dbh');

        // Get the whole list of params
        $query="SELECT parameter, value, type 
                FROM `#__realty_requests_params` 
                WHERE request_id = :request_id";             

        $sth = $dbh->prepare($query);

        $sth->execute(array(
            'request_id' => $request_id
        ));

        $items = $sth->fetchAll(PDO::FETCH_OBJ);

        foreach ($items as $item) {

            if ($item->type == 'array') {
                $item->value = unserialize($item->value);
            }
            
            $params[$item->parameter] = $item->value;

        }

        return $params;

    }

    public function getObjectsListByParams($request_id, $params) {

        $dbh = Registry::get('dbh');        
        $user = User::getUserData();        
        $query_params = array();

        // get matchings for params
        $matchings = $this->getParamsMatchings();
        
        $query="SELECT
                  r.id,
                  r.adress,
                  r.price,
                  r.total_area,
                  r.last_edit AS last_edit,
                  r.archive AS archive,
                  r.author_id,
                  r.agency_id,
                  rro.selected AS selected,
                  ri.image_name,
                  ra.logo AS agency_logo,
                  ra.name AS agency_name
                FROM
                  `#__realty` AS r
                LEFT JOIN
                  `#__realty_requests_objects` AS rro ON r.id = rro.object_id AND rro.request_id = $request_id
                LEFT JOIN
                  `#__realty_images` AS ri ON r.id = ri.object_id AND ri.ordering = 0
                LEFT JOIN
                  `#__realty_agencys` AS ra ON r.agency_id = ra.id
                WHERE rro.selected = 1
                  
                UNION ALL

                SELECT
                  r.id,
                  r.adress,
                  r.price,
                  r.total_area,
                  r.last_edit,
                  r.archive,
                  r.author_id,
                  r.agency_id,
                  IFNULL(rro.selected, 0),
                  ri.image_name,
                  ra.logo,
                  ra.name
                FROM
                  `#__realty` AS r
                LEFT JOIN
                  `#__realty_requests_objects` AS rro ON r.id = rro.object_id AND rro.request_id = $request_id
                LEFT JOIN
                  `#__realty_images` AS ri ON r.id = ri.object_id AND ri.ordering = 0
                LEFT JOIN
                  `#__realty_agencys` AS ra ON r.agency_id = ra.id
                WHERE
                  r.archive = 0 AND r.deleted = 0 AND IFNULL(rro.selected, 0) < 1";             

        foreach ($params as $key => $value) {
            
            if (!empty($value) && array_key_exists($key, $matchings)) {

                if (gettype($value) == 'array') {

                    $value = implode(",", $value);

                    $query .= " AND r." . $matchings[$key]['column_name'] . " " . $matchings[$key]['operator'] . " (".$value.")";

                } else {
                            
                    $query .= " AND r." . $matchings[$key]['column_name'] . " " . $matchings[$key]['operator'] . " :".$key;
                    
                    $query_params[$key] = $value;  
                }                           
            
            }

        }   
        
        // configurate show params
        if (isset($params['show'])) {
            foreach ($params['show'] as $key => $value) {
                
                if ($value == 0) break;

                $query .= " AND (FALSE";
                // display mine objects
                if ($value == 1) $query .= " OR r.author_id = $user->id";
                // display my company objects
                if ($value == 2) $query .= " OR r.agency_id = $user->agency_id";
                // display other companies objects
                if ($value == 3) $query .= " OR r.agency_id <> $user->agency_id";

                $query .= ")";

            }
        }

        $query .= " ORDER BY selected DESC, last_edit DESC";

        //echo $query;

        $sth = $dbh->prepare($query);

        $sth->execute($query_params);

        $items = $sth->fetchAll(PDO::FETCH_OBJ);

        return $items;

    }

    public function getRequestSelectedObjects($request_id) {

        $dbh = Registry::get('dbh');

        // Get the whole list of params
        $query="SELECT rro.object_id, ro.adress, ro.price, ro.total_area
                FROM `#__realty_requests_objects` AS rro
                LEFT JOIN `#__realty` AS ro
                ON rro.object_id = ro.id
                WHERE rro.request_id = :request_id
                AND rro.selected > 0";             

        $sth = $dbh->prepare($query);

        $sth->execute(array('request_id' => $request_id));

        return $sth->fetchAll(PDO::FETCH_OBJ);

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

    public function countSelectedObjects($request_id) {

        $dbh = Registry::get('dbh');

        $query="SELECT count(object_id) AS count
                FROM `#__realty_requests_objects` 
                WHERE request_id = :request_id AND selected > 0";             

        $sth = $dbh->prepare($query);
        
        $sth->execute(array('request_id' => $request_id));

        $data = $sth->fetch(PDO::FETCH_OBJ);

        return $data->count;

    }

    private function getParamsMatchings() {

        $matchings = array();

        // creating matchings for params
        $matchings['category_id'] = array(
            'column_name' => 'category_id',
            'operator' => '='           
        );              
        $matchings['price_from'] = array(
            'column_name' => 'price',
            'operator' => '>='          
        );
        $matchings['price_to'] = array(
            'column_name' => 'price',
            'operator' => '<='          
        );
        $matchings['area_from'] = array(
            'column_name' => 'total_area',
            'operator' => '>='      
        );
        $matchings['area_to'] = array(
            'column_name' => 'total_area',
            'operator' => '<='          
        );
        $matchings['floor'] = array(
            'column_name' => 'floor',
            'operator' => 'IN'          
        );

        return $matchings;

    }

    public function getNewRequestObjects($request_id) {

        $dbh = Registry::get('dbh');        
        $user = User::getUserData();        
        $query_params = array();

        $params = $this->getReqParams($request_id);
        $matchings = $this->getParamsMatchings();        
        
        $query="SELECT
                  r.id,
                  r.adress,
                  r.price,
                  r.total_area,
                  r.last_edit,
                  r.archive,
                  r.author_id,
                  r.agency_id
                FROM
                  `#__realty` AS r
                LEFT JOIN
                  `#__realty_requests_objects` AS rro 
                  ON r.id = rro.object_id 
                  AND rro.request_id = $request_id 
                WHERE
                  r.archive = 0 
                  AND r.deleted = 0 
                  AND rro.selected IS NULL";             

        foreach ($params as $key => $value) {
            
            if (!empty($value) && array_key_exists($key, $matchings)) {

                if (gettype($value) == 'array') {

                    $value = implode(",", $value);

                    $query .= " AND r." . $matchings[$key]['column_name'] . " " . $matchings[$key]['operator'] . " (".$value.")";

                } else {
                            
                    $query .= " AND r." . $matchings[$key]['column_name'] . " " . $matchings[$key]['operator'] . " :".$key;
                    
                    $query_params[$key] = $value;  
                }                           
            
            }

        }   
        
        // configurate show params
        if (isset($params['show'])) {
            foreach ($params['show'] as $key => $value) {
                
                if ($value == 0) break;

                $query .= " AND (FALSE";
                // display mine objects
                if ($value == 1) $query .= " OR r.author_id = $user->id";
                // display my company objects
                if ($value == 2) $query .= " OR r.agency_id = $user->agency_id";
                // display other companies objects
                if ($value == 3) $query .= " OR r.agency_id <> $user->agency_id";

                $query .= ")";

            }
        }

        $query .= " ORDER BY last_edit DESC";

        //echo $query;

        $sth = $dbh->prepare($query);

        $sth->execute($query_params);

        $items = $sth->fetchAll(PDO::FETCH_OBJ);

        return $items;

    }

    public function setNewRequestObjectViwed($request_id, $object_id) {

        $dbh = Registry::get('dbh');

        $params = array(
            'request_id' => $request_id,
            'object_id' =>  $object_id,
            'selected' => 0
        );

        $sth = $dbh->prepare("INSERT INTO `#__realty_requests_objects` (request_id, object_id, selected) VALUES (:request_id, :object_id, :selected)");

        return $sth->execute($params);

    }


}