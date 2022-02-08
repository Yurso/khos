<?php
Class Template {
	
	private $tmpl_path = '';
	private $tmpl_title = '';
	private $tmpl_head_additions = '';
	private $tmpl_head_meta = '';
	public $tmpl_page_title = '';

	// Main display function
	public function display($tmpl, $theme = '') {

		# get site config from registry
		$conf = Registry::get('config');
		$route = Registry::get('route');

		# mobile detecting
		require_once SITE_PATH . 'includes' . DIRSEP . 'Mobile-Detect-2.8.20' . DIRSEP . 'Mobile_Detect.php';
		$detect = new Mobile_Detect;
		
		# generate controller view path		
		$this->tmpl_path = $route->path . 'views' . DIRSEP . $tmpl . '.php';

		# if site title is empty, site title = site name from configuration
		if (empty($this->tmpl_title)) { $this->tmpl_title = $conf->SiteName; }

		# if theme not selected, find theme in database or get default theme (sory for my english)
		if (empty($theme)) { $theme = $this->_getTheme($route->subpath); }

		# if empty them than set default theme from configuration file
		if (empty($theme)) { $theme = $conf->DefaultTheme; }

		if (!isset($_SESSION['fullview'])) { $_SESSION['fullview'] = 0; }

		if (isset($_GET['fullview'])) { $_SESSION['fullview'] = $_GET['fullview']; }
		 
		// Any mobile device (phones or tablets).
		if ( $detect->isMobile() && !$_SESSION['fullview']) { $theme = 'mobile'; }

		if (isset($_GET['theme']) && !empty($_GET['theme'])) { $theme = $_GET['theme']; }

		// if this theme has replacement view for this component
		$theme_view_path = SITE_PATH . 'themes' . DIRSEP . $theme . DIRSEP . 'views' . DIRSEP . $route->component . DIRSEP . $tmpl . '.php';
		if (is_file($theme_view_path)) {
			$this->tmpl_path = $theme_view_path;
		}

		# generate path to theme file
		$theme_path = SITE_PATH . 'themes' . DIRSEP . $theme . DIRSEP . $theme . '.php';

		# include theme, if it is file
		if (is_file($theme_path)) {
			include($theme_path);
		} else {
			trigger_error('Theme "' . $theme . '" not found in theme path', E_USER_NOTICE);
		}

	}

	// Sets site title
	public function setTitle($title) {

		$this->tmpl_page_title = $title;

		$conf = new Configuration;

		if ($conf->SiteNamePosition == 'before') {

			$title = $conf->SiteName . ' - ' . $title;

		} elseif ($conf->SiteNamePosition == 'after') {

			$title = $title . ' - ' . $conf->SiteName;

		}

		$this->tmpl_title = $title;

	}

	// Sets vars for use in templates
    public function setVar($varname, $value, $overwrite = false) {
        
        if (isset($this->$varname) == true AND $overwrite == false) {
            trigger_error ('Unable to set var `' . $varname . '`. Already set, and overwrite not allowed.', E_USER_NOTICE);
            return false;
        }
        $this->$varname = $value;
        
        return true;

    }

    // Unset vars from Controller Class
    public function unsetVar($varname) {

        unset($this->$varname);

    }

	// Display content
	public function content() {

		if (is_file($this->tmpl_path)) {
			
			include($this->tmpl_path);

		} else {
			
			trigger_error('View file "' . $this->tmpl_path . '" not found in component path', E_USER_NOTICE);

		}		

	}

	public function addScript($url) {
		$this->tmpl_head_additions .= '<script type="text/javascript" src="'.$url.'"></script>';
	}

	public function addStyle($url) {
		$this->tmpl_head_additions .= '<link rel="stylesheet" href="'.$url.'" />';
	}

	// Include widget to position
	// public function position($name) {

	// 	$result = '';

	// 	$items = $this->_getPositionWidgets($name);

	// 	foreach ($items as $item) {
			
	// 		$widget_file = SITE_PATH . 'widgets' . DIRSEP . $item->widget . '.php';

	// 		if (is_file($widget_file)) {

	// 			require_once($widget_file);

	// 			$class = $item->widget . 'Widget';

	// 			$widget = new $class();

	// 			if (is_callable(array($class, 'setParams'))) {
	// 				$widget->setParams($item->params);										
	// 			}

	// 			if (is_callable(array($class, 'display'))) {
	// 				$result = $widget->display();					
	// 			}

	// 		} else {
	// 			trigger_error('Widget "' . $item->widget . '" not found in widget path', E_USER_NOTICE);
	// 		}

	// 	}

	// }

	// // Returns number of widgets in this position
	// public function countpos($name) {

	// 	$dbh = Registry::get('dbh');

	// 	$sth = $dbh->query("SELECT COUNT(*) as count
	// 						FROM `#__widgets`
	// 						WHERE position = '" . $name . "' AND state = 1");

	// 	$data = $sth->fetch(PDO::FETCH_OBJ);
        
 //        return $data->count;

	// }

	// // Returns widget positions list
	// private function _getPositionWidgets($name) {

	// 	$dbh = Registry::get('dbh');

	// 	$sth = $dbh->query("SELECT wp.widget, wp.params
	// 						FROM `#__widgets` AS wp
	// 						LEFT JOIN `#__components` AS c
	// 						ON wp.widget = c.name
	// 						WHERE wp.position = '" . $name . "' AND wp.state = 1 AND c.state > 0 AND c.type = 'widget'");

	// 	return $sth->fetchAll(PDO::FETCH_OBJ);
	// }

	// Returns theme name by controller path
	private function _getTheme($ctrl_path) {

		$dbh = Registry::get('dbh');

		$sth = $dbh->query("SELECT name
							FROM `#__templates`
							WHERE state > 0 AND ctrl_path = '" . $ctrl_path . "'");

		if ($data = $sth->fetch(PDO::FETCH_OBJ)) {
			return $data->name;
		} else {
			return '';
		}

	}

}