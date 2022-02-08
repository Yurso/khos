<?php
Class TasksStatisticsModel {

    public function getPaidByYearData($date_from, $date_to) {

    	$dbh = Registry::get('dbh'); 

        $query="SELECT 
					DATE_FORMAT(items.paid_date, '%Y') AS year,
				    customers.name AS customer_name,
					SUM(items.price * items.count) AS price
				FROM 
					`#__tasks_items` AS items
				LEFT JOIN 
					`#__tasks_customers` AS customers
				ON
					items.customer_id = customers.id
				WHERE 
					items.paid_date >= :date_from
				AND
					items.paid_date <= :date_to
				AND
					items.price > 0
				GROUP BY
					DATE_FORMAT(items.paid_date, '%Y'),
				    customers.name
				ORDER BY
					items.paid_date";

        $sth = $dbh->prepare($query);

        $sth->execute(array(
        	'date_from' => $date_from,
        	'date_to' => $date_to
        ));

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

}