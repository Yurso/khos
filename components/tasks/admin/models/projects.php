<?php
Class TasksProjectsModel Extends ModelBase {

    public $table = "#__tasks_projects";

    protected function _buildItemsQuery() {

		$query="SELECT 
                    p.*,
                    c.name AS customer_name
                FROM 
                    `#__tasks_projects` AS p 
                LEFT JOIN `#__tasks_customers` AS c
                    ON p.customer_id = c.id";	            

        return $query;

	}

}