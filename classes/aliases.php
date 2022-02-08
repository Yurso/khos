<?php
Class Aliases {

	static public function setAlias($alias, $controller, $action = 'index', $args = array()) {

		$router	= Registry::get('router');
		$dbh	= Registry::get('dbh');

		// Prepare alias value
		$alias = Main::str2url($alias);

		// Prepare url value
		$url = $route->component.'/'.$controller.'/'.$action;

		foreach ($args as $arg) {
			$url .= '/'.$arg;
		}

		// Check if this alias already exist
        $sth=$dbh->prepare("SELECT count(*) AS count FROM `#__aliases` WHERE alias = :alias");

        $params = array();
        $params['alias'] = $alias;

        $sth->execute($params);

        $data = $sth->fetch(PDO::FETCH_OBJ);

        // Return if alias exited
        if ($data->count) {
        	Main::setMessage('Страница с таким алиасом уже существует.');
        	return 0;
        }

        // Saving alias to db
       	$sth=$dbh->prepare("INSERT INTO `#__aliases` (alias, url) VALUES (:alias, :url)");

       	$params = array();
        $params['alias'] = $alias;
        $params['url'] = $url;
        
        if ($sth->execute($params)) {
            return $dbh->lastInsertId(); 
        } else {
            Main::setMessage('Не удалось записать алиас в базу данных');
            return 0;
        }

	}

}