<?php
Class AdminMenuWidget Extends WidgetBase {

	private $items = array();
	private $user;

	public $params = array(
		'menu_id' => 0,
		'class' => 'menu',
		'display_childrens' => 0
	);

	public function display() {	
		
		$route = Registry::get('route');
		$this->user = Registry::get('user');

		//$result = '<div class="menu-title">Главное меню</div>';
		$result = '<ul class="menu-vertical">';

		//print_r($this->items);

		$items = $this->getItems(4);

		foreach ($items as $item) {			

			$class = '';
			// $counter = 0;	
			$counter_html = '';

			$req_uri = $_SERVER['REQUEST_URI'];

			if ($item->component == $route->component && stripos($req_uri, $item->url) === 0) {
				$class = 'active';
			}
			
			// $subpath = '';
			// $controller = '';
			
			// // getting url parts
			// $controller_parts = explode('/', $item->controller);
			// foreach ($controller_parts as $part) {
			// 	$subpath .= $controller;
			// 	$controller = $part;
			// }

			// if (($item->component == $route->component && $controller == $route->controller) || ($item->component == $route->component && $controller == 'index')) {				
			// 	$class = 'active';
			// }

			//execute counter query
			if (!empty($item->counter_query)) {
				$counter = $this->execCounterQuery($item->counter_query);	
				if ($counter > 0) {
					$counter_html = ' <span class="menu-item-counter">'.$counter.'</span>';
				}
			}
			// // generating url
			// $url = '/'.$subpath.'/'.$item->component .'/'. $controller;

			// // icon
			// $image = '';
			// if (!empty($item->image)) {
			// 	$image = '<i class="fa '.$item->image.'" aria-hidden="true"></i> ';
			// }

			// Collect all information to result
			if ($item->component == 'separator') {
				$result .= '<li class="menu-item-separator"></li>';
			} else {
				$result .= '<li>';
				$result .= '<a href="'.$item->url.'" class="'.$class.'" target="'.$item->target.'">'.$item->title.$counter_html.'</a>';
				if (count($item->chilrens)) {
					$result .= $this->display_childrens($item->chilrens);
				}
				$result .= '</li>';
			}

		}

		$result .= '</ul>';

		echo $result;

	}

	private function display_childrens($items) {

		$route = Registry::get('route');

		$result = '<ul class="item-childrens">';

		foreach ($items as $item) {

			//if (!User::checkUserAccess($item->access_id)) { continue; }

			$class = '';
			$counter = 0;	
			$counter_html = '';
			
			$subpath = '';
			$controller = '';
			
			// getting url parts
			$controller_parts = explode('/', $item->controller);
			foreach ($controller_parts as $part) {
				$subpath .= $controller;
				$controller = $part;
			}

			// if (($item->component == $route->component && $controller == $route->controller) || ($item->component == $route->component && $controller == 'index')) {			
			// 	$class = 'active';
			// }

			if (empty($item->action)) {
				if (($item->component == $route->component && $controller == $route->controller) || ($item->component == $route->component && $controller == 'index')) {			
					$class = 'active';
				}
			} else {
				if ($item->component == $route->component && $controller == $route->controller && $item->action == $route->action) {			
					$class = 'active';
				}
			}

			// generating url
			$url = '/'.$subpath.'/'.$item->component .'/'. $controller . '/' . $item->action;
			// collect all information to result
			$result .= '<li>';
			$result .= '<a href="'.$url.'" class="'.$class.'" target="'.$item->target.'">'.$item->title.$counter_html.'</a>';
			$result .= '</li>';

		}

		$result .= '</ul>';

		return $result;

	}

	private function addItem($url, $controller, $title) {

		$item = new stdClass;

		$item->url = $url;
		$item->controller = $controller;		
		$item->title = $title;

		$this->items[] = $item;

	}

	private function getItems($menu_id) {

		$dbh = Registry::get('dbh');
		$access_list = user::getAccessIds();

		$query="SELECT 
			        items.id, 
			        items.title, 
			        items.parent_id,
			        items.component, 
			        items.controller, 
			        items.action, 
			        items.url, 
			        items.target, 
			        items.image,
			        items.state, 
			        items.ordering, 
			        items.frontpage,
			        items.access_id, 
			        items.counter_query, 
			        menu.name AS menu_name
			    FROM 
			    	`#__menu_items` AS items
			    LEFT JOIN 
			    	`#__menu` AS menu
			    ON 
			    	items.menu_id = menu.id
				WHERE items.menu_id = :menu_id
				AND items.state > 0
				AND items.access_id IN ($access_list)
				ORDER BY items.ordering";

		$sth = $dbh->prepare($query);

		$params = array();
		$params['menu_id'] = $menu_id;
		//$params['access_list'] = $access_list;

        $sth->execute($params);

       	$items = $sth->fetchAll(PDO::FETCH_OBJ);

       	return $this->sort_items_into_tree($items);

	}

	private function sort_items_into_tree($items, $parent_id = 0, $prefix = '') {
        
        $output = array();

        foreach ($items as $key => $item) {

            if ($item->parent_id == $parent_id) {
                
                //$item->title = $prefix . $item->title;
                $item->chilrens = $this->sort_items_into_tree($items, $item->id, $prefix . '');
                
                $output[] = $item;
                unset($items[$key]);

                //$output = array_merge($output, $this->sort_items_into_tree($items, $item->id, $prefix . '-- '));
            }
            
        }

        return $output;

    }

    private function execCounterQuery($query) {

    	$result = 0;

    	try {
	    	$dbh = Registry::get('dbh');

			$sth = $dbh->prepare($query);

	        $sth->execute(array(
				'user_id' => $this->user->id
			));

	       	$data = $sth->fetch(PDO::FETCH_OBJ);

	       	if (isset($data->counter)) {
	       		$result = intval($data->counter);
	       	}

	    } catch (Exception $e) {}

       	return $result;

    }

}