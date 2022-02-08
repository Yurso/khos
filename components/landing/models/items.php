<?php
Class LandingItemsModel Extends ModelBase {

    public $table = '#__landing_items';

    protected function _buildItemsOrder() {
    	return ' ORDER BY ordering ASC ';
    }

    public function getTypeClass($type_name) {

        $route = Registry::get('route');

        $file = $route->path . 'admin' . DIRSEP . 'types' . DIRSEP . $type_name . '.php';

        if (is_file($file)) {
            
            include_once($file);

            $class = $route->component.$type_name.'Type';

            return new $class;  

        } else {

            trigger_error('Type "' . $type_name . '" not found in types path', E_USER_ERROR);
            
            return false;

        }   

    }

    public function getTypesList() {

        $route = Registry::get('route');

        // Search types in types directory
        $types = array();     

        $types_dir = $route->path . 'admin' . DIRSEP . 'types' . DIRSEP;
        
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
