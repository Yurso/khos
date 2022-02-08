<?php
Class SystemConfigurationModel {

	public function getThemesList() {

		$dbh = Registry::get('dbh');

    	$query = "SELECT id, name
                  FROM `#__components`
                  WHERE type = 'theme'
                  AND state > 0
                  ORDER BY name ASC";

        $sth = $dbh->prepare($query);

        $sth->execute();
        
        return $sth->fetchAll(PDO::FETCH_OBJ);

	}

}