<?php
Class TasksItemsModel Extends ModelBase {

    public $table = "#__tasks_items";

    public $default_ordering = array('column' => 'i.date', 'sort' => 'DESC');

    protected function _buildItemsQuery() {

		$query="SELECT 
                    i.*,
                    c.name AS customer_name,
                    p.title AS project_title,
                    t.title AS type_title
                FROM 
                    `#__tasks_items` AS i 
                LEFT JOIN `#__tasks_customers` AS c
                    ON i.customer_id = c.id
                LEFT JOIN `#__tasks_projects` AS p
                    ON i.project_id = p.id
                LEFT JOIN `#__tasks_types` AS t
                    ON i.type_id = t.id";	            

        return $query;

	}
    /*
    protected function _buildItemsOrder() {
        
        $query  = " ORDER BY i.state ASC ";
        
        $query .= str_replace("ORDER BY", " , ", parent::_buildItemsOrder());
        
        return $query;        
    }
    */

    private function getParamsMatchings() {

        $matchings = array();

        // creating matchings for params
        $matchings['date_from'] = array(
            'column_name' => 'complete_date',
            'operator' => '>='           
        );              
        $matchings['date_to'] = array(
            'column_name' => 'complete_date',
            'operator' => '<='          
        );
        $matchings['customers'] = array(
            'column_name' => 'customer_id',
            'operator' => 'IN',
            'type' => 'int'
        );
        $matchings['statuses'] = array(
            'column_name' => 'status',
            'operator' => 'IN',
            'type' => 'string'        
        );
        $matchings['state'] = array(
            'column_name' => 'state',
            'operator' => 'IN'      
        );
        $matchings['paid'] = array(
            'column_name' => 'paid',
            'operator' => 'IN'          
        );
        $matchings['deleted'] = array(
            'column_name' => 'deleted',
            'operator' => 'IN'          
        );

        return $matchings;

    }

    public function getItemsByParams($params) {

        $query_params = array();

        $dbh = Registry::get('dbh'); 

        $matchings = $this->getParamsMatchings();

        $query = $this->_buildItemsQuery();
        $query .= " WHERE true";

        foreach ($params as $key => $value) {
            
            if (!empty($value) && array_key_exists($key, $matchings)) {

                if (gettype($value) == 'array') {

                    $value = implode(",", $value);

                    if (isset($matchings[$key]['type']) && $matchings[$key]['type'] == 'int') {
                        $query .= " AND i." . $matchings[$key]['column_name'] . " " . $matchings[$key]['operator'] . " (".$value.")";
                    } else {
                        $query .= " AND i." . $matchings[$key]['column_name'] . " " . $matchings[$key]['operator'] . " ('".$value."')";
                    }

                } else {
                            
                    $query .= " AND i." . $matchings[$key]['column_name'] . " " . $matchings[$key]['operator'] . " :".$key;
                    
                    $query_params[$key] = $value;  
                }                           
            
            }

        }

        $query .= " ORDER BY date ASC";

        echo $query;

        $sth = $dbh->prepare($query);

        $sth->execute($query_params);

        $items = $sth->fetchAll(PDO::FETCH_OBJ);

        return $items;

    }

    public function statisticsSummary() {
        
        $dbh = Registry::get('dbh'); 

        $query="SELECT                     
                    c.name AS customer_name,
                    sum(i.price * i.count) AS sum,
                    sum(1) AS tasks
                FROM 
                    `#__tasks_items` AS i 
                LEFT JOIN `#__tasks_customers` AS c
                    ON i.customer_id = c.id
                WHERE 
                    i.status = 'complete' AND i.price > 0
                GROUP BY
                    c.name                
                ORDER BY
                    c.name ASC";

        $sth = $dbh->prepare($query);

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function statisticsPreiod($date_from, $date_to) {
        
        $dbh = Registry::get('dbh'); 

        $query="SELECT                     
                    c.name AS customer_name,
                    sum(i.price * i.count) AS sum,
                    sum(1) AS tasks
                FROM 
                    `#__tasks_items` AS i 
                LEFT JOIN `#__tasks_customers` AS c
                    ON i.customer_id = c.id
                WHERE 
                    i.status = 'complete' AND i.price > 0 AND i.complete_date >= :date_from AND i.complete_date <= :date_to
                GROUP BY
                    c.name                
                ORDER BY
                    c.name ASC";

        $params = array();
        $params['date_from'] = $date_from;
        $params['date_to'] = $date_to;

        $sth = $dbh->prepare($query);

        $sth->execute($params);

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    // public function deleteItem($id) {

    //     $dbh = Registry::get('dbh');

    //     $item = $this->getItem($id);

    //     // If item already marked as deleted then delete line from db. Else only mark as deleted
    //     if ($item->deleted) {            

    //         $result = parent::deleteItem($id);
            
    //     } else {            
            
    //         $params = array('id' => $id);        
    //         $query = "UPDATE `#__tasks_items` SET deleted = 1 WHERE id = :id";        
    //         $sth = $dbh->prepare($query);            
    //         $result = $sth->execute($params);

    //     }

    //     return $result;

    // }

    public function getStatuses() {
        
        return array(
            'new' => 'Новая',
            'process' => 'На выполнении',
            'complete' => 'Выполнена',
            'paid' => 'Оплачена',
            'canceled' => 'Отменена'
        );

    }

}