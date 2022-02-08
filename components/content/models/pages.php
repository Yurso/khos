<?php
Class ContentPagesModel Extends ModelBase {

    public $table = '#__pages';

    // Defaults vars
    public $default_ordering = array('column' => 'p.create_date', 'sort' => 'DESC');

    protected function _buildItemQuery() {

        $query="SELECT p.*, u.name AS author_name
                FROM `#__pages` AS p
                LEFT JOIN `#__users` AS u
                ON p.author_id = u.id
                WHERE p.id = :id";

        return $query;

    }

    protected function _buildItemsQuery() {

        $query="SELECT p.id, p.title, p.alias, p.create_date, p.state, u.name AS author_name
                FROM `#__pages` AS p
                LEFT JOIN `#__users` AS u
                ON p.author_id = u.id";

        //$query .= ' ORDER BY p.create_date DESC, p.title ASC';

        return $query;

    }

}