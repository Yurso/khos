<?php
Class User {
  
    // Возвращает истину если пользователь авторизован
    static public function checkAuth() {        
        
        if (isset($_SESSION['id']) and isset($_SESSION['hash']))
        {   
            $dbh = Registry::get('dbh');

            $id = intval($_SESSION['id']);
            
            $query = "SELECT id, hash, user_id, ip, INET_NTOA(ip) AS user_ip FROM `#__sessions` WHERE id = :id";
            
            $sth = $dbh->prepare($query);
            $sth->execute(array('id' => $id));
            
            $session = $sth->fetch(PDO::FETCH_ASSOC);

            if (($session['hash'] == $_SESSION['hash']) and ($session['id'] == $_SESSION['id'])) {
                return true;    
            }
        }

        return false;             

    }    
    
    // Возвращает данные пользователя (если он авторизован) или пустой массив
    static public function getUserData($field = '') {

        $result = NULL;
        
        if (isset($_SESSION['id']) and isset($_SESSION['hash'])) {        
            
            $dbh = Registry::get('dbh');

            $params = array(
                'session_id' => intval($_SESSION['id']),
                'session_hash' => $_SESSION['hash']
            );  

            $query="SELECT users.*, users_access.name AS access_name
                    FROM `#__sessions` AS sessions
                    LEFT JOIN `#__users` AS users
                    ON sessions.user_id = users.id
                    LEFT JOIN `#__users_access` AS users_access
                    ON users.access = users_access.id 
                    WHERE sessions.id = :session_id AND sessions.hash = :session_hash";
            
            $sth = $dbh->prepare($query);

            $sth->execute($params);
            
            $data = $sth->fetch(PDO::FETCH_OBJ);

            if (isset($data->id)) {      
                
                unset($data->password);

                if ($field == '') {                    
                    
                    $result = $data;    

                } elseif (isset($data->$field)) {
                    
                    $result = $data->$field;

                } 

            } 

        }

        return $result;

    }      
    
    // Функция возвращения AccessName
    static public function getUserAccessName() {
        
        $user = self::getUserData();
        
        if (count($user)) {

            $dbh = Registry::get('dbh');            
        
            $query = "SELECT name FROM `#__users_access` WHERE id = '". $user->access ."'";
            
            $data = $dbh->query($query)->fetch(PDO::FETCH_OBJ);
        
            return $data->name;
        
        } else {
        
            return 'unregistered';
        
        }
    }

    static public function updateSessions() {

        $config = Registry::get('config');
        $dbh    = Registry::get('dbh'); 
        
        // Kill old sessions
        if ($config->sessions_live_time > 0) {
            
            $sessions_live_date = date("Y-m-d H:i:s", strtotime('-'.$config->sessions_live_time.' minutes'));

            $query="DELETE FROM `#__sessions` WHERE active_date < :sessions_live_date AND stored = 0";

            $sth = $dbh->prepare($query);

            $sth->execute(array('sessions_live_date' => $sessions_live_date));   

        }

        // Checking session data in cookie
        if (!isset($_SESSION['id']) && isset($_COOKIE["session_id"]) && isset($_COOKIE["session_hash"])) {

            $_SESSION['id'] = $_COOKIE["session_id"];
            $_SESSION['hash'] = $_COOKIE["session_hash"];

        }

        // Update last_active of user
        if (self::checkAuth()) {  

            //$user = self::getUserData();

            $id = intval($_SESSION['id']);
            $hash = $_SESSION['hash'];
            $ip = $_SERVER['REMOTE_ADDR'];

            $query="UPDATE `#__sessions` 
                    SET `ip` = INET_ATON('$ip'), `active_date`=:active_date, `last_page`=:last_page 
                    WHERE id = :id 
                    AND hash = :hash";

            $sth = $dbh->prepare($query);

            // Get last page url
            if (isset($_SERVER['SERVER_NAME']) or isset($_SERVER['REQUEST_URI'])) {
                $last_page = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
            } else {
                $last_page = 'undefined';
            }

            $params = array(
                'active_date' => date("Y-m-d H:i:s"),
                'last_page' => $last_page,
                'id' => $id,
                'hash' => $hash
            );

            $sth->execute($params);

        } 

    }

    // Return true if user have an access for access_id
    static public function checkUserAccess($access_id) {

        $user_access = 1;

        $user_data = self::getUserData();

        if (isset($user_data->access))
            $user_access = $user_data->access;

        if ($user_access == $access_id)
            return true;

        // checking childrens
        $childrens = self::getAccessChildrens($access_id); 

        foreach ($childrens as $children) {

            if ($user_access == $children->id) return true;

        }

    }

    static public function getAccessChildrens($parent_id) {

        $access_list = self::getAccessList();

        $items = self::filter_by_parent($access_list, $parent_id);

        return $items;

    }

    static public function getAccessList() {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT id, name, parent_id FROM `#__users_access`");

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    static private function filter_by_parent($items, $parent_id, $output = array()) {

        foreach ($items as $key => $item) {
            
            if ($item->parent_id == $parent_id) {
                $output[] = $item;
                $output = array_merge($output, self::filter_by_parent($items, $item->id));
            }

        }

        return $output;

    }

    static public function getAccessId($access_name) {

        $id = 0;

        $dbh = Registry::get('dbh');

        $sth = $dbh->prepare("SELECT id FROM `#__users_access` WHERE name = :name");

        $sth->execute(array('name' => $access_name));

        $data = $sth->fetch(PDO::FETCH_OBJ);

        if (isset($data->id)) { $id = $data->id; }

        return $id;

    }

    static public function getAccessIds() {

        $user = self::getUserData();

        if (isset($user->id)) {

            $dbh = Registry::get('dbh');

            $sth = $dbh->prepare("SELECT id, parent_id FROM `#__users_access`");

            $sth->execute();

            $items = $sth->fetchAll(PDO::FETCH_OBJ);

            $access_list = $user->access;

            $access_list .= self::getAccessIdsTree($items, $user->access);

        } else {
            $access_list = '0,1';
        }
        
        return $access_list;

    }

    static private function getAccessIdsTree($items, $id) {

        $access_list = '';

        foreach ($items as $item) {
            
            if ($item->id == $id) { 
                $access_list .= ",".$item->parent_id;
                $access_list .= self::getAccessIdsTree($items, $item->parent_id);
            }

        }

        return $access_list;

    }
            
}