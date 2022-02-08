<?php
Class BlogModel {

	public function getItems($ordering = 'b.public_date DESC', $limitstart = 0, $limitcount = 20) {

		$dbh = Registry::get('dbh');

        $query  = $this->_prepareItemsQuery();

        $today = date("Y-m-d H:i:s");

        $query .= " WHERE b.public_date <= '$today'";

        # Ordering block
        if (!empty($ordering))
            $query .= " ORDER BY " . $ordering;          

        # Limit block
        $limitend = $limitstart + $limitcount; 

        $query .= " LIMIT " . $limitstart . ", " . $limitend;

        # Fetching
        $sth = $dbh->query($query);

        return $sth->fetchAll(PDO::FETCH_OBJ);

	}

    public function getItemsByTag($tag, $ordering = 'b.public_date DESC', $limitstart = 0, $limitcount = 20) {

        $dbh = Registry::get('dbh');
        
        $query  = $this->_prepareItemsQuery();

        $today = date("Y-m-d H:i:s");

        $query .= " WHERE b.state > 0 AND b.public_date <= '$today' AND b.tags LIKE :tag";

        # Ordering block
        if (!empty($ordering))
            $query .= " ORDER BY " . $ordering;

        # Limit block
        $limitend = $limitstart + $limitcount; 

        $query .= " LIMIT " . $limitstart . ", " . $limitend;

        # Fetching
        $sth = $dbh->prepare($query);
        $sth->execute(array('tag'=> '%'.$tag.'%'));

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function getItemsByCategory($id, $ordering = 'b.public_date DESC', $limitstart = 0, $limitcount = 20) {

        $dbh = Registry::get('dbh');

        # Main query
        $query  = $this->_prepareItemsQuery();

        $today = date("Y-m-d H:i:s");

        $query .= " WHERE b.state > 0 AND b.public_date <= '$today' AND b.category_id = " . $id;

        # Ordering block
        if (!empty($ordering))
            $query .= " ORDER BY " . $ordering;

        # Limit block
        $limitend = $limitstart + $limitcount; 

        $query .= " LIMIT " . $limitstart . ", " . $limitend;

        # Fetching
        $sth = $dbh->query($query);

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function getItem($id) {

        $dbh = Registry::get('dbh');

        $query  = $this->_prepareItemsQuery();

        $query .= " WHERE b.id = " . $id;

        # Fetching
        $sth = $dbh->query($query);

        return $sth->fetch(PDO::FETCH_OBJ);

    }

    // Main query for items
    private function _prepareItemsQuery() {

        $query = "SELECT b.*, u.name AS author_name, c.title AS category_title, c.alias AS category_alias
                  FROM `#__blog` AS b
                  LEFT JOIN `#__categories` AS c
                  ON b.category_id = c.id                
                  LEFT JOIN `#__users` AS u
                  ON b.author_id = u.id";

        return $query;

    }

    public function getCategoriesList() {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__categories`
                            WHERE state > 0 AND controller = 'blog'");

        return $sth->fetchAll(PDO::FETCH_OBJ);        
    }

    public function getCategoryInfo($id) {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__categories`
                            WHERE id = " . $id);

        return $sth->fetch(PDO::FETCH_OBJ);        
    }

	public function itemExist($id) {

		$dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT COUNT(*) as count
                            FROM `#__blog`
                            WHERE id = " . $id);
        
        $data = $sth->fetch(PDO::FETCH_OBJ);
        
        return $data->count;

	}

    public function getComments($id) {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__comments`
                            WHERE controller = 'blog' AND state > 0 AND item_id = $id
                            ORDER BY create_date DESC");

        return $sth->fetchAll(PDO::FETCH_OBJ); 

    }
	
}