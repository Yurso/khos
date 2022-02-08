<?php
Class MenuWidget Extends WidgetBase {

	public function display() {
		
		$result = "\n";

		$items = $this->getData($this->params['menu_id']);

		$route = Registry::get('route');

		if (count($items)) {

			$result .= '<ul class="'.$this->params['class'].'">' . "\n";

			foreach ($items as $key => $item) {

				$class = '';
				$url = '';

				if ($route->controller == $item->controller && $route->component == $item->component && $route->action == $item->action) {
					$class = 'active';
				}				

				if (!$item->frontpage) {				

					if (!empty($item->component)) {

						$url .= '/' . $item->component . '/';

						if (!empty($item->controller)) $url .= $item->controller . '/';

					}
					
					$url .= $item->action;

				} else {

					$url = '/';
					
				}

				$result .= '	<li class="item'.$item->id.'">';
				$result .= '		<a href="'.$url.'" class="'.$class.'">';				
				
				if (empty($item->image)) {
					$result .= $item->title;	
				} else {
					$result .= '<img src="/public/images/' . $item->image . '" alt="' . $item->title . '" />';	
				}

				$result .= '		</a>';
				
				if ($this->params['display_childrens'])
					$result .= $this->displayChildrens($item->childrens);	

				$result .= '	</li>';
				
			}

			$result .= '</ul>' . "\n";

		}

		echo $result;

	}

	private function getData($menu_id) {

		$dbh = Registry::get('dbh');

		$sth = $dbh->query("SELECT id, title, parent_id, component, controller, action, url, image, ordering, frontpage
							FROM `#__menu_items`
							WHERE menu_id = $menu_id AND state > 0
							ORDER BY ordering ASC");

		$items = $sth->fetchAll(PDO::FETCH_OBJ);

		return $this->sort_items_into_tree($items);

	}

	private function sort_items_into_tree($items, $parent_id = 0, $output = array()) {

        foreach ($items as $key => $item) {

            if ($item->parent_id == $parent_id) {
                
                $item->childrens = $this->sort_items_into_tree($items, $item->id);
                
                $output[] = $item;
                unset($items[$key]);

            }
            
        }

        return $output;

    }

    private function displayChildrens($items) {

    	$result = '';

    	if (count($items)) {

	    	$result .= '<ul>';

	    	foreach ($items as $key => $item) {
	    		
	    		$result .= '<li>';

	    		$result .= $item->title;

	    		$result .= $this->displayChildrens($item->childrens);

	    		$result .= '</li>';

	    	}

	    	$result .= '</ul>';

    	}

    	return $result;

    }

    public function buildParamsForm($params) {

    	$result = '';

    	// Menu parameter data
    	$menus = $this->getMenusList();

    	$menu_id = 0;
    	if (isset($params['menu_id'])) {
    		$menu_id = intval($params['menu_id']);
    	}
    	// Menu parameter view
    	$result .= '<div class="block-item">';
        $result .= '	<label>Меню:</label><br>';
        $result .= htmler::SelectList($menus, 'params[menu_id]', null, null, $menu_id);
        $result .= '</div>';

        // Display childrens parameter
        $display_childrens = 0;
        if (isset($params['display_childrens'])) {
        	$display_childrens = intval($params['display_childrens']);
        }
        $result .= '<div class="block-item">';
        $result .= '	<label>Показывать подпункты:</label><br>';
        $result .= htmler::booleanSelect($display_childrens, 'params[display_childrens]');
        $result .= '</div>';

        $class = '';
        if (isset($params['class'])) {
        	$class = $params['class'];
        }
        $result .= '<div class="block-item">';
        $result .= '	<label>Класс:</label><br>';
       	$result .= htmler::inputText('params[class]', $class);
       	$result .= '</div>';

    	return $result;

    }

    private function getMenusList() {

    	$dbh = Registry::get('dbh');

		$sth = $dbh->query("SELECT id, name FROM `#__menu`");

		$items = $sth->fetchAll(PDO::FETCH_OBJ);

		$result = array();

		foreach ($items as $item) {
			$result[$item->id] = $item->name;			
		}

		return $result;

    }
	
}