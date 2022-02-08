<?php
Class SystemSessionsModel Extends ModelBase {

    public $table = "#__sessions";

    // Defaults vars
    public $default_ordering = array('column' => 'id', 'sort' => 'ASC');

    protected function _buildItemsQuery() {

        $query="SELECT 
                    s.id, 
                    s.user_id, 
                    s.hash, 
                    INET_NTOA(ip) AS ip, 
                    s.start_date, 
                    s.active_date, 
                    s.last_page, 
                    u.name AS user_name
                FROM `#__sessions` AS s
                LEFT JOIN `#__users` AS u
                ON s.user_id = u.id";               

        return $query;

    }

    public function createSession($user_id, $stored = false) {

    	$params = array();
    	$params['user_id'] = $user_id;
    	$params['hash'] = md5(main::generateCode(10));
    	$params['start_date'] = date("Y-m-d H:i:s");
    	$params['active_date'] = date("Y-m-d H:i:s");
        
        if ($stored) { 
            $params['stored'] = 1; 
        }

    	$id = $this->SaveNewItem($params);

    	$session = $this->getItem($id);

    	return $session;

    }

    public function onlineUsers() {

        $dbh = Registry::get('dbh');

        $query="SELECT s.user_id AS id, u.name AS name, s.start_date AS start_date
                FROM `#__sessions` AS s
                LEFT JOIN `#__users` AS u
                ON s.user_id = u.id
                GROUP BY id, name";  

        $sth = $dbh->prepare($query);

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

}