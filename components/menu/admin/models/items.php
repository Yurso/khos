<?php
Class MenuItemsModel Extends ModelBase {

    public $table = "#__menu_items";

    public $default_ordering = array('column' => 'items.ordering', 'sort' => 'ASC');

    protected function _buildItemsQuery() {

        $query="SELECT 
                    items.id, 
                    items.title, 
                    items.parent_id,
                    items.component, 
                    items.controller, 
                    items.action, 
                    items.state, 
                    items.ordering, 
                    items.frontpage, 
                    menu.name AS menu_name,
                    user_access.name AS access_name
                FROM `#__menu_items` AS items
                LEFT JOIN `#__menu` AS menu
                ON items.menu_id = menu.id
                LEFT JOIN `#__users_access` AS user_access
                ON items.access_id = user_access.id";

        return $query;

    }

    public function getItems($params = array()) {

        $items = parent::getItems($params);

        return $this->sort_items_into_tree($items);

    }

    protected function sort_items_into_tree($items, $parent_id = 0, $prefix = '') {
        
        $output = array();

        foreach ($items as $key => $item) {

            if ($item->parent_id == $parent_id) {
                
                $item->title = $prefix . ' ' . $item->title;
                
                $output[] = $item;
                unset($items[$key]);

                $output = array_merge($output, $this->sort_items_into_tree($items, $item->id, $prefix . '--'));
            }
            
        }

        return $output;

    }

    public function getMenusList() {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__menu`");

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function getActiveControllers() {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__components`
                            WHERE type = 'controller' AND state > 0
                            ORDER BY name");

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function getActiveComponents() {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__components`
                            WHERE type = 'component' AND state > 0
                            ORDER BY name");

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function getLastOrdering() {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT ordering
                            FROM `#__menu_items`                            
                            ORDER BY ordering DESC
                            LIMIT 1");

        $data = $sth->fetch(PDO::FETCH_OBJ);

        return $data->ordering;

    }

    public function resetFrontPage() {

        $dbh = Registry::get('dbh');

        return $dbh->query("UPDATE `#__menu_items`                            
                            SET frontpage = 0");

        //return $sth->exec();

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

    public function getUsersAccessList() {        

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__users_access` AS ua");

        $items = $sth->fetchAll(PDO::FETCH_OBJ);        

        return $this->sort_users_into_tree($items);
    }

    protected function sort_users_into_tree($items, $parent_id = 0, $prefix = '') {
        
        $output = array();

        foreach ($items as $key => $item) {

            if ($item->parent_id == $parent_id) {
                
                $item->name = $prefix . $item->name;
                
                $output[] = $item;
                unset($items[$key]);

                $output = array_merge($output, $this->sort_users_into_tree($items, $item->id, $prefix . '- '));
            }
            
        }

        return $output;

    }


}