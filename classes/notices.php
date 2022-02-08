<?php
Class Notices Extends ModelBase {

	public $table = "#__notices";

	public function add($user_id, $component, $title, $description = '', $url = '') {

        $user = User::getUserData();

        $params = array();
        $params['component'] = $component;
        $params['user_id'] = $user_id;
        $params['title'] = $title;
        $params['description'] = $description;
        $params['create_date'] = date("Y-m-d H:i:s");
        $params['viewed'] = 0;
        $params['author_id'] = $user->id;

        if (!empty($url)) {
            $params['url'] = $url;
        }

        return $this->SaveNewItem($params);  

    }

    public function addByAccessName($access_name, $component, $title, $description = '', $url = '') {

        $user = User::getUserData();

        $dbh = Registry::get('dbh'); 

        $query="SELECT users.id 
                FROM `#__users` AS users
                LEFT JOIN `#__users_access` AS access
                ON users.access = access.id
                WHERE access.name = :access_name";

        $sth = $dbh->prepare($query);

        $params = array();
        $params['access_name'] = $access_name;

        $sth->execute($params);

        $items = $sth->fetchAll(PDO::FETCH_OBJ);

        $i = 0;

        foreach ($items as $item) {
            $params = array();
            $params['component'] = $component;
            $params['user_id'] = $item->id;
            $params['title'] = $title;
            $params['description'] = $description;
            $params['create_date'] = date("Y-m-d H:i:s");
            $params['viewed'] = 0;
            $params['author_id'] = $user->id;

            if (!empty($url)) {
                $params['url'] = $url;
            }

            if ($this->SaveNewItem($params)) {
                $i++;
            }
        }

        return $i;

    }

}