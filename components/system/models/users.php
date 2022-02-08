<?php
Class SystemUsersModel Extends ModelBase {

    public $table = '#__users';

    public $default_ordering = array('column' => 'u.name', 'sort' => 'ASC');

    protected function _buildItemQuery() {

        return "SELECT 
                    u.id, 
                    u.name,
                    u.login, 
                    u.email, 
                    u.access, 
                    u.activated, 
                    u.position, 
                    u.phone, 
                    u.website, 
                    u.image,
                    u.work_email,
                    u.agency_id,
                    ra.name AS agency_name
                FROM 
                    `#__users` AS u
                LEFT JOIN `#__realty_agencys` AS ra
                    ON u.agency_id = ra.id
                WHERE 
                    u.id = :id";

    }

    protected function _buildItemsQuery() {

        return "SELECT u.id, u.name, u.login, u.email, a.name AS access_name
                FROM `#__users` AS u
                LEFT JOIN `#__users_access` AS a
                ON u.access = a.id";

    }

	public function getUserByEmail($email) {

		$dbh = Registry::get('dbh');

		$sth=$dbh->prepare("SELECT u.id, u.name, u.password, u.activated, a.name AS access_name
                            FROM `#__users` AS u
                            LEFT JOIN `#__users_access` AS a
                            ON u.access = a.id
                            WHERE email=:email");

        $sth->execute(array('email' => $email));

		return $sth->fetch(PDO::FETCH_OBJ);

	}

    public function getUserByHash($hash) {

        $dbh = Registry::get('dbh');

        $sth=$dbh->prepare("SELECT u.id, u.activated, a.name AS access_name
                            FROM `#__users` AS u
                            LEFT JOIN `#__users_access` AS a
                            ON u.access = a.id
                            WHERE hash=:hash");

        $sth->execute(array('hash' => $hash));

        return $sth->fetch(PDO::FETCH_OBJ);

    }

	public function updateUserHash($userid, $hash = '') {

		$dbh = Registry::get('dbh');

        if (empty($hash)) {
            $hash = md5($this->generateCode(10));
        }

        # Переводим IP в строку
        $insip = ", ip=INET_ATON('".$_SERVER['REMOTE_ADDR']."')";
        
        # Записываем в БД новый хеш авторизации и IP
        $sth = $dbh->prepare("UPDATE `#__users` SET hash=:hash ".$insip." WHERE id=:id");

        if ($sth->execute(array('hash' => $hash, 'id' => $userid))) {
            return $hash;   
        } else {
            return '';
        }		

	}

    public function updateUserActCode($user_id) {

        $dbh = Registry::get('dbh');

        $act_code = md5($this->generateCode(10)); 

        $params = array(
            'act_code' => $act_code, 
            'id' => $user_id
        );       
        
        $sth = $dbh->prepare("UPDATE `#__users` SET act_code=:act_code WHERE id=:id");

        if ($sth->execute($params)) {
            return $act_code;   
        } else {
            return '';
        }       

    }

    public function getUserByActCode($act_code) {

        $dbh = Registry::get('dbh');

        $sth=$dbh->prepare("SELECT u.id, u.name
                            FROM `#__users` AS u
                            WHERE act_code=:act_code");

        $sth->execute(array('act_code' => $act_code));

        return $sth->fetch(PDO::FETCH_OBJ);

    }

    public function getUsersAccessList() {        

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__users_access` AS ua");

        $items = $sth->fetchAll(PDO::FETCH_OBJ);        

        return $this->sort_items_into_tree($items);
    }

    public function getUsersAccessTree($parent_id = 0) {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__users_access`
                            WHERE parent_id = $parent_id");

        $data = $sth->fetchAll(PDO::FETCH_OBJ);

        foreach ($data as $key => $value) {
            
            $data[$key]->childrens = $this->getUsersAccessTree($value->id);

        }

        return $data;

    }

    protected function sort_items_into_tree($items, $parent_id = 0, $prefix = '') {
        
        $output = array();

        foreach ($items as $key => $item) {

            if ($item->parent_id == $parent_id) {
                
                $item->name = $prefix . $item->name;
                
                $output[] = $item;
                unset($items[$key]);

                $output = array_merge($output, $this->sort_items_into_tree($items, $item->id, $prefix . '- '));
            }
            
        }

        return $output;

    }

    public function checkAuthByApiKey($hash) {

        $dbh = Registry::get('dbh');

        $sth=$dbh->prepare("SELECT COUNT(*) as count
                            FROM `#__users` WHERE hash=:hash AND access = 3");

        $sth->execute(array('hash' => $hash));        

        $data = $sth->fetch(PDO::FETCH_OBJ);

        return $data->count;       

    }

	// Функция для генерации случайной строки
    public function generateCode($length=6) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;  
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];  
        }
        return $code;
    }

    public function getAgencysList() {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__realty_agencys` 
                            WHERE state > 0");

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

}