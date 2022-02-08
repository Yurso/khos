<?php
Class ShopProductsModel Extends ModelBase {

	public $table = '#__shop_products';

	public $default_ordering = array('column' => 'title', 'sort' => 'ASC');

    // protected function _buildItemQuery() {

    //     return "SELECT 
    //                 p.id, 
    //                 p.title, 
    //                 p.alias, 
    //                 p.description, 
    //                 p.price, 
    //                 p.state, 
    //                 p.default_image_id, 
    //                 u.title AS unit_title
    //             FROM 
    //                 `#__shop_products` as p
    //                 LEFT JOIN `#__shop_units` as u
    //                 ON p.unit_id = u.id";

    // }

    public function getCategoriesList($product_id = 0, $component = 'shop') {

        $dbh = Registry::get('dbh');

        $query="SELECT c.id, c.title, c.alias, pc.product_id
                FROM `#__categories` AS c
                LEFT JOIN `#__shop_products_categories` AS pc
                ON c.id = pc.category_id AND pc.product_id = $product_id
                WHERE c.state > 0 AND c.component = '$component'";

		$sth = $dbh->query($query);

        return $sth->fetchAll(PDO::FETCH_OBJ);        
    }

    public function SaveItemCategories($id, $categories) {    	

    	$dbh = Registry::get('dbh');

    	$result = true;

    	// Delete all first
    	$sth=$dbh->prepare("DELETE FROM `#__shop_products_categories`
    						WHERE `product_id` = :id");

    	$sth->execute(array('id' => $id));

    	foreach ($categories as $value) {

    		$sth=$dbh->prepare("INSERT INTO `#__shop_products_categories` (category_id, product_id)
    							VALUES (:category_id, :product_id)");

    		if (!$sth->execute(array('category_id' => $value, 'product_id' => $id))) {
    			$result = false;
    		}

    		
    	}

    	return $result;

    }

    public function getUnitsList() {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT * FROM `#__shop_units` WHERE state > 0");

        return $sth->fetchAll(PDO::FETCH_OBJ);  

    }

}