<?php
Class Widgets {

	// Display widget item
	static public function display($widget_id) {
		
		// Secure widget_id
		$widget_id = intval($widget_id);
		
		// Get widget information
		$widget_item = self::getWidgetItem($widget_id);
		
		// Get widget class
		$widget_class = self::getWidgetClass($widget_item->widget);		
		
		// Add widget item values to widget class
		$widget_class->title = $widget_item->title;
		$widget_class->params = $widget_item->params;

		// Display widget information
		$widget_class->display();							

	}

	// Display widgets by position name
	static public function position($position_name) {

		// Get position widgets
		$items = self::getPositionItems($position_name);

		foreach ($items as $item) {

			if ($item->show_type == 'list' || $item->show_type == 'excl') {
				
				$show_list_parts = explode(',', $item->show_list);				
				
				$route = Registry::get('route');

				// if route not in list and type = show only
				if ($item->show_type == 'list' && !in_array($route->current, $show_list_parts)) {
					continue;
				}
				// if route in list and type = exclude
				if ($item->show_type == 'excl' && in_array($route->current, $show_list_parts)) {
					continue;
				}
			}

			self::display($item->id);

		}

	}

	// Returns number of widgets in this position
	static public function position_count($position_name) {

		$items = self::getPositionItems($position_name);

		$i = 0;

		foreach ($items as $item) {

			if ($item->show_type == 'list' || $item->show_type == 'excl') {
				
				$show_list_parts = explode(',', $item->show_list);				
				
				$route = Registry::get('route');

				// if route not in list and type = show only
				if ($item->show_type == 'list' && !in_array($route->current, $show_list_parts)) {
					continue;
				}
				// if route in list and type = exclude
				if ($item->show_type == 'excl' && in_array($route->current, $show_list_parts)) {
					continue;
				}
			}

			$i++;
		}

		return $i;

	}

	// Return widget items by position name
	static private function getPositionItems($position_name) {

		$dbh = Registry::get('dbh');

        $query="SELECT w.id, w.title, w.widget, w.params, w.show_type, w.show_list
                FROM `#__widgets` AS w
                WHERE w.state > 0 AND w.position = :position_name
                ORDER BY w.ordering";  

        $sth = $dbh->prepare($query);

        $params = array(
        	'position_name' => $position_name);

        $sth->execute($params);

        return $sth->fetchAll(PDO::FETCH_OBJ);

	}

	// Return widget class by widget name
	static private function getWidgetClass($widget_name) {

		$widget_path = SITE_PATH . 'widgets' . DIRSEP . $widget_name . DIRSEP . $widget_name . '.php';

		if (file_exists($widget_path) == false) { 
	        return false;
	    }

		include_once($widget_path);

		$class_name = $widget_name . 'Widget';

		$widget_class = new $class_name;

		$widget_class->widget_name = $widget_name;

		return $widget_class;

	}

	// Return widget item by widget_id
	static private function getWidgetItem($widget_id) {

		$dbh = Registry::get('dbh');

        $query="SELECT *
                FROM `#__widgets` AS w
                WHERE w.id = :widget_id";  

        $sth = $dbh->prepare($query);

        $sth->execute(array(
        	'widget_id' => $widget_id));

        $item = $sth->fetch(PDO::FETCH_OBJ);

        $item->params = unserialize($item->params);

        return $item;

	}

	// Return params form in html by widget name. Required widget params array
	static public function getParamsForm($widget_name, $widget_params = array()) {

		$result = '';

		$widget = self::getWidgetClass($widget_name);

		$class_name = $widget_name . 'Widget';

		if (is_callable(array($class_name, 'buildParamsForm'))) {
			$result = $widget->buildParamsForm($widget_params);
		}

		return $result;

	}

	// Run additional functions by trigger name
	static public function trigger($trigger_name, $widget_name) {

		$widget_class = self::getWidgetClass($widget_name);

		$class_name = $widget_name . 'Widget';

		if (is_callable(array($class_name, $trigger_name))) {
			$widget_class->$trigger_name();
		}

	}

}