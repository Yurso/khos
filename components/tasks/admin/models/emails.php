<?php
Class TasksEmailsModel Extends ModelBase {

    public $table = "#__tasks_customers_emails";

    protected function _buildItemsQuery() {

		$query="SELECT 
                    tce.*,
                    tc.name AS customer_name
                FROM 
                    `#__tasks_customers_emails` AS tce 
                LEFT JOIN `#__tasks_customers` AS tc
                    ON tce.customer_id = tc.id";	            

        return $query;

	}

	protected function _buildItemQuery() {

		$query = $this->_buildItemsQuery();

		$query .= ' WHERE tce.id = :id';

        return $query;

	}

}