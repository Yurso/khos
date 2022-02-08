<?php 
// Autoloading classes
function __autoload($class_name) {
    $filename = strtolower($class_name) . '.php';
    $file = SITE_PATH . 'classes' . DIRSEP . $filename;
    
    if (file_exists($file) == false) { 
        return false;
    }

    include_once($file);
}

error_reporting (E_ALL);

if (version_compare(phpversion(), '5.1.0', '<') == true) { die ('PHP5.1 Only'); }
// Constants
define ('DIRSEP', DIRECTORY_SEPARATOR);

// Get site path
$site_path = realpath(dirname(__FILE__) . DIRSEP) . DIRSEP;
// Set basic paths definitions
define ('SITE_PATH', $site_path);
define ('COMPONENTS_PATH', SITE_PATH . 'components');

// Init DataBase handler
$db = new db;
$dbh = $db->init();
// Save handler to registry
Registry::set('dbh', $dbh);

// Get configuration data
$config = new Configuration;
// Delete DB information from config var
unset($config->dbtype);
unset($config->dbhost);		
unset($config->dbname);
unset($config->dbuser);
unset($config->dbpassword);
unset($config->dbprefix);
unset($config->dbreplace);
// Save configuration to Registry
Registry::set('config', $config);

// Set php time zone
date_default_timezone_set('Europe/Moscow');

// Start session
session_start();
// Check cookies and update user sessions
User::updateSessions();
// Save user data to Registry 
Registry::set('user', User::getUserData());

// Get router
$router = new Router;
// Route to component and delegate
$router->setPath(COMPONENTS_PATH);
$router->delegate();
