<?php
Class LandingItemsModel Extends ModelBase {

    public $table = '#__landing_items';

    // Defaults vars
    public $default_ordering = array('column' => 'ordering', 'sort' => 'ASC');

    // Main query for items
    protected function _buildItemsQuery() {
        return "SELECT i.*, u.name AS author_name
                FROM $this->table AS i
                LEFT JOIN `#__users` AS u
                ON i.author_id = u.id";
    }

    protected function _buildItemQuery() {        
        $query = $this->_buildItemsQuery();
        $query .= " WHERE i.id = :id";
        return $query;
    }

    public function getItem($params = array()) {

        $item = parent::getItem($params);

        $item->params = unserialize($item->params);

        return $item;

    }

    public function getTypeClass($type_name) {

        $route = Registry::get('route');

        $file = $route->path . 'types' . DIRSEP . $type_name . '.php';

        if (is_file($file)) {
            
            include_once($file);

            $class = $route->component.$type_name.'Type';

            return new $class;  

        } else {

            trigger_error('Type "' . $type_name . '" not found in types path', E_USER_ERROR);
            
            return false;

        }   

    }

    public function getLastOrderingValue() {

        $result = 0;

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT MAX(ordering) AS ordering FROM `$this->table`");

        $data = $sth->fetch(PDO::FETCH_OBJ);

        if (isset($data->ordering) && $data->ordering > 0) {
            $result = $data->ordering;
        }

        return $result;

    }

    public function getTypesList() {

        $route = Registry::get('route');

        // Search types in types directory
        $types = array();     

        $types_dir = $route->path . 'types' . DIRSEP;
        
        $files = scandir($types_dir);
        // unset not php files and clear file type
        foreach ($files as $key => $file) {
            if (stripos($file, '.php') === false) {
                unset($files[$key]);
            } else {
                $types[] = str_replace('.php', '', $file);
            }
        }

        return $types;

    }

}