<?php
Class ContentBlogModel Extends ModelBase {

    public $table = '#__blog';

    // Defaults vars
    public $default_ordering = array('column' => 'b.public_date', 'sort' => 'DESC');

    // Main query for items
    protected function _buildItemsQuery() {

        $query="SELECT b.id, b.title, b.alias, b.state, b.create_date, b.public_date, u.name AS author_name, c.title AS category_title, c.alias AS category_alias
                FROM `#__blog` AS b
                LEFT JOIN `#__categories` AS c
                ON b.category_id = c.id                
                LEFT JOIN `#__users` AS u
                ON b.author_id = u.id";

        return $query;

    }

    protected function _buildItemQuery() {
        
        $query="SELECT b.*, u.name AS author_name, c.title AS category_title, c.alias AS category_alias
                FROM `#__blog` AS b
                LEFT JOIN `#__categories` AS c
                ON b.category_id = c.id                
                LEFT JOIN `#__users` AS u
                ON b.author_id = u.id
                WHERE b.id = :id";

        return $query;

    }

    // public function getCategoriesList() {

    //     $dbh = Registry::get('dbh');

    //     $sth = $dbh->query("SELECT id, title, alias, parent_id, state, controller
    //                         FROM `#__categories`
    //                         WHERE state > 0 AND controller = 'blog'");

    //     $items = $sth->fetchAll(PDO::FETCH_OBJ);

    //     return $this->sort_items_into_tree($items);
        
    // }

    // protected function sort_items_into_tree($items, $parent_id = 0, $prefix = '') {
        
    //     $output = array();

    //     foreach ($items as $key => $item) {

    //         if ($item->parent_id == $parent_id) {
                
    //             $item->title = $prefix . $item->title;
                
    //             $output[] = $item;
    //             unset($items[$key]);

    //             $output = array_merge($output, $this->sort_items_into_tree($items, $item->id, $prefix . '- '));
    //         }
            
    //     }

    //     return $output;

    // }

    public function getCategoryInfo($id) {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__categories`
                            WHERE id = " . $id);

        return $sth->fetch(PDO::FETCH_OBJ);        
    }

}