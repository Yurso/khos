<?php
Class Router {

    private $path;
    
    public function setPath($path) {

        if (substr($path, -1) <> DIRSEP) {
            $path .= DIRSEP;
        }

        if (is_dir($path) == false) {
            throw new Exception ('Invalid controller path: `' . $path . '`');
        }
        
        $this->path = $path;    

    }

    public function delegate() {
        // Analyze route
        $this->getController($file, $path, $subpath, $component, $controller, $action, $args);          

        // Adding information to Registry
        $route = new stdClass;            
        
        $route->path = $path;            
        $route->subpath = $subpath;
        $route->component = $component;
        $route->controller = $controller;
        $route->action = $action;
        $route->args = $args;

        $route->current = '/'.$route->component.'/'.$route->controller.'/'.$route->action;
        foreach ($route->args as $key => $arg) {
            $route->current .= "/$arg";
        }

        Registry::set('route', $route);         

        // File available?
        if (is_readable($file) == false) {
            die ('404 Not Found');
        }

        // Include the file
        include ($file);                
        
        // Initiate the class
        $class = str_replace("_", "", $component . $controller . 'Controller');
        $controller_class = new $class();

        // Action available?
        if (is_callable(array($controller_class, $action)) == false) {
            die ('404 Not Found');
        } 
        
        // Checking access for controller
        if (!$this->checkAccess($component, $controller, $subpath)) {  
            if (User::checkAuth()) {
                Main::redirect('/', 'У вас нет доступа в данный раздел');    
            } else {
                Main::redirect('/system/user/login?redirect='.$_SERVER['SCRIPT_URL']);    
            }                  
        }       

        // Run action
        $controller_class->$action();

    }

    private function getController(&$file, &$path, &$subpath, &$component, &$controller, &$action, &$args) {
        
        $route = (empty($_GET['route'])) ? '' : $_GET['route'];

        $route = $this->checkAliases($route);

        if (empty($route)) {            
            $route = $this->getFrontPageInfo();                
        }

        // Get separate parts
        $route = trim($route, '/\\');
        $parts = explode('/', $route);

        $subpath = '';
        // Get names of component, controller and action
        $component = array_shift($parts);
        // If administration instance change subpath
        if ($component == 'admin') {
            $subpath = 'admin' . DIRSEP;
            $component = array_shift($parts);
        }
        // Get conroller and action names
        $controller = array_shift($parts);
        $action = array_shift($parts);

        // Set default values
        if (empty($component)) { $component = 'index'; }
        if (empty($controller)) { $controller = 'index'; }
        if (empty($action)) { $action = 'index'; }

        $path = $this->path . $component . DIRSEP . $subpath;
        $file = $path . $controller . '.php';

        $args = $parts;

        // $route = (empty($_GET['route'])) ? '' : $_GET['route'];
        
        // if (empty($route)) { 
            
        //     $fpinfo = $this->getFrontPageInfo();

        //     if (isset($fpinfo->controller) && !empty($fpinfo->controller)) {
        //         $route = $fpinfo->controller . '/' . $fpinfo->action;
        //     } else {
        //         $route = 'index';     
        //     }
            
        // }
   
        // // Get separate parts
        // $route = trim($route, '/\\');
        // $parts = explode('/', $route);
        
        // // Find right controller
        // $cmd_path = '';
        // foreach ($parts as $part) {
                
        //         $fullpath = $this->path . $cmd_path . $part;

        //         // Is there a dir with this path?
        //         if (is_dir($fullpath)) {
        //                 $cmd_path .= $part . DIRSEP;
        //                 array_shift($parts);
        //                 continue;
        //         }

        //         // Find the file
        //         if (is_file($fullpath . '.php')) {
        //                 $controller = $part;
        //                 array_shift($parts);
        //                 break;
        //         }
        // }

        // if (empty($controller)) { $controller = 'index'; };

        // // Get action
        // $action = array_shift($parts);
        // if (empty($action)) { $action = 'index'; }

        // $file = $this->path . $cmd_path . $controller . '.php';
        // $args = $parts;
    }

    private function checkAccess($component, $controller, $subpath = '') {

        $result = false;
        $subpath = str_replace(DIRSEP, '/', $subpath);

        $dbh = Registry::get('dbh');

        $sth=$dbh->prepare("SELECT access
                            FROM `#__components`
                            WHERE type = 'controller'
                            AND name = :name
                            AND component = :component
                            AND state > 0");

        $params = array();
        $params['name'] = $subpath.$controller;
        $params['component'] = $component;

        $sth->execute($params);

        $data = $sth->fetch(PDO::FETCH_OBJ);

        // if component registred
        if (isset($data->access)) {

            if (User::checkUserAccess($data->access))
                $result = true;

        }

        return $result;

    }

    private function getFrontPageInfo() {

        $dbh = Registry::get('dbh');

        $url = '';

        $sth = $dbh->query("SELECT component, controller, action
                            FROM `#__menu_items`
                            WHERE frontpage = 1");        

        $item = $sth->fetch(PDO::FETCH_OBJ);

        if (isset($item->component) && !empty($item->component)) {

            $url .= '/' . $item->component . '/';
            
            if (!empty($item->controller)) {

                $parts = explode('/', $item->controller);

                if (count($parts) > 1) {
                    $url = '/' . $parts[0] . $url . $parts[1] . '/';
                } else {
                    $url .= $parts[0] . '/';
                } 

            }

            if (!empty($item->action)) {
                $url .= $item->action;
            }

        }

        return $url;

    }

    private function checkAliases($route) {          

        $dbh = Registry::get('dbh');

        $sth=$dbh->prepare("SELECT url
                            FROM `#__aliases`
                            WHERE alias = :route");

        $sth->execute(array('route' => $route));

        $data = $sth->fetch(PDO::FETCH_OBJ);

        if (isset($data->url) && !empty($data->url)) {
            $route = $data->url;
        }

        return $route;

    }

}
