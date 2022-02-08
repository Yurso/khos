<?php
Class SystemComponentsModel Extends ModelBase {

	public $tbale = '#__components';

    // Defaults vars
    public $default_ordering = array('column' => 'c.type', 'sort' => 'ASC');

    // Main query for items
    protected function _buildItemsQuery() {

		$query = "SELECT c.id, c.name, c.type, c.state, c.protected, c.register_date, ua.name AS access_name
                  FROM `#__components` AS c
                  LEFT JOIN `#__users_access` AS ua
                  ON c.access = ua.id";

        return $query;

    }

    protected function _buildItemsOrder() {

        $result = parent::_buildItemsOrder();

        if (!empty($result)) {
        	$result .= ', c.name ASC';
        }

        return $result;

    }

    public function getControllers($component) {

    	$dbh = Registry::get('dbh');

    	$query = "SELECT id, name, access, state
                  FROM `#__components`
                  WHERE component = :component
                  AND type = 'controller'
                  ORDER BY name ASC";

        $sth = $dbh->prepare($query);

        $params = array();
        $params['component'] = $component;

        $sth->execute($params);
        
        $items = $sth->fetchAll(PDO::FETCH_OBJ);

        return $items;

    }

	public function findControllers($component) {

		$items = array();
		
		// Site controllers
		$dir = COMPONENTS_PATH . DIRSEP . $component . DIRSEP;
		
		$files = scandir($dir);	

		foreach ($files as $key => $value) {

			if (is_dir($dir.$value)) { continue; }
		
			if (strpos($value, '.php') === false) { continue; }

			$name = str_replace(".php", "", $value);

			$items[$name] = $name;

		}

		// Administration controllers
		$dir .= 'admin'.DIRSEP;

		if (is_dir($dir)) {

			$files = scandir($dir);		

			foreach ($files as $key => $value) {

				if (is_dir($dir.$value)) { continue; }
			
				if (strpos($value, '.php') === false) { continue; }

				$name = 'admin/'.str_replace(".php", "", $value);

				$items[$name] = $name;

			}

		}

		return $items;

	}

	public function updateControllers($component) {

		$dbh = Registry::get('dbh');

    	$query = "SELECT name
                  FROM `#__components`
                  WHERE component = :component
                  AND type = 'controller'";

        $sth = $dbh->prepare($query);

        $params = array();
        $params['component'] = $component;

        $sth->execute($params);
        
        // Получаем все зарегистрированные контроллеры
        $registred = $sth->fetchAll(PDO::FETCH_OBJ);

        // Находим все контроллеры компонента
        $found = $this->findControllers($component);

        // Удаляем уже зарегестрированные контроллеры
        foreach ($registred as $controller) {
        	if (isset($found[$controller->name])) { unset($found[$controller->name]); }
        }

        // Сохраняем новый контроллеры в базу
        foreach ($found as $ctrl_name) {
        	
        	$params = array();
        	$params['name'] = $ctrl_name;
			$params['type'] = 'controller';
			$params['state'] = 1;
			$params['access'] = User::getAccessId('administrator');
			$params['register_date'] = date("Y-m-d H:i:s");
			$params['edit_date'] = date("Y-m-d H:i:s");
			$params['component'] = $component;
			// записываем данные контроллера в базу
			if ($this->SaveNewItem($params)) {
				Main::setMessage("Зарегистрирован новый контроллер: $ctrl_name");
			}

        }

	}

	public function findComponents() {
		
		$dir = COMPONENTS_PATH;
		
		$files = scandir($dir);

		array_shift($files);
		array_shift($files);

		$items = array();

		foreach ($files as $key => $file) {
			if (is_dir($dir.DIRSEP.$file)) { $items[$file] = $file; } 
		}		

		return $items;

	}

	public function findWidgets() {

		$widgets_path = SITE_PATH . 'widgets' . DIRSEP;

		$files = scandir($widgets_path);

		$items = array();

		foreach ($files as $key => $filename) {

			$widget_file = $widgets_path . $filename . DIRSEP . $filename . '.php';			
			
			if (!is_file($widget_file)) continue;			

			$items[$filename] = $filename;

		}

		return $items;

	}

	public function findThemes() {

		$themes_path = SITE_PATH . 'themes' . DIRSEP;

		$files = scandir($themes_path);

		$items = array();

		foreach ($files as $key => $filename) {

			$widget_file = $themes_path . $filename . DIRSEP . $filename . '.php';			
			
			if (!is_file($widget_file)) continue;			

			$items[$filename] = $filename;

		}

		return $items;

	}

	public function getControllerParams($name) {

		$params = array();

		$file = SITE_PATH . 'controllers' . DIRSEP . 'admin' . DIRSEP . $name . '.php';

		if (is_file($file)) {

			include_once($file);

			try {

				$class = $name . 'Controller';

				$object = new $class();

				$params = $object->params;
				
			} catch (Exception $e) {

			}

		}		

		return $params;

	}

	public function getWidgetParams($name) {

		$params = array();

		$file = SITE_PATH . 'widgets' . DIRSEP . $name . '.php';

		if (is_file($file)) {

			include_once($file);

			try {

				$class = $name . 'Widget';

				$object = new $class();

				if (isset($object->params)) {
					$params = $object->params;	
				}			

			} catch (Exception $e) {
				
			}

		}		

		return $params;

	}

	public function getUsersAccessList() {

		$dbh = Registry::get('dbh');

		$sth = $dbh->query("SELECT * FROM `#__users_access`");

		return $sth->fetchAll(PDO::FETCH_OBJ);

	}

	public function getUsersAccessTree() {                

        $items = $this->getUsersAccessList();        

        return $this->sort_items_into_tree($items);
        
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

	// public function getRegistredComponents($type = '') {

	// 	$dbh = Registry::get('dbh');

	// 	$query = "SELECT c.*, ua.name AS access_name
 //                  FROM `#__components` AS c
 //                  LEFT JOIN `#__users_access` AS ua
 //                  ON c.access = ua.id
 //                  ORDER BY type, name";

	// 	if (!empty($type)) {
	// 		$query .= " WHERE type = '" . $type . "'";
	// 	}

	// 	$sth = $dbh->query($query);

 //        return $sth->fetchAll(PDO::FETCH_OBJ);

	// }

	// public function registerComponent($data) {
  
 //        $result = 0;

 //        $dbh = Registry::get('dbh');

 //        $sth = $dbh->prepare("INSERT INTO `#__components` (name, type, state, user_id, params, register_date, edit_date) 
 //                              VALUES (:name, :type, :state, :user_id, :params, :register_date, :edit_date)"); 

 //        if ($sth->execute($data)) {
 //            $result = $dbh->lastInsertId();    
 //        }

 //        return $result; 

	// }

	// public function unregister($id) {

	// 	$dbh = Registry::get('dbh');

 //        $query = "DELETE FROM `#__components` WHERE `id` = ".$id;
        
 //        $result = $dbh->exec($query);
        
 //        return $result;

	// }

	// public function getComponentData($id) {

	// 	$dbh = Registry::get('dbh');

	// 	$query = "SELECT *
 //                  FROM `#__components`
 //                  WHERE id = " . $id;

	// 	$sth = $dbh->query($query);

 //        return $sth->fetch(PDO::FETCH_OBJ);

	// }

	// public function SaveItem($id, $data) {

	// 	$dbh = Registry::get('dbh');

 //        $sth = $dbh->prepare("UPDATE `#__components`
 //                              SET state = :state, access=:access, params = :params, edit_date = :edit_date
 //                              WHERE id = " . $id); 

 //        return $sth->execute($data);

	// }


}