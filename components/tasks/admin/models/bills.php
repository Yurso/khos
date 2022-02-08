<?php
Class TasksBillsModel Extends ModelBase {

    public $table = "#__tasks_bills";

    public $default_ordering = array('column' => 'create_date', 'sort' => 'DESC');

    public function addItemToBill($bill_id, $task_id) {

    	$dbh = Registry::get('dbh');

    	$sth = $dbh->prepare("INSERT INTO `#__tasks_bills_items` (bill_id, task_id) VALUES (:bill_id, :task_id)");

    	return $sth->execute(array(
    			'bill_id' => $bill_id,
    			'task_id' => $task_id
    			));

    }

    public function getBillItems($bill_id) {

        $dbh = Registry::get('dbh'); 

        $query="SELECT 
                    i.*,
                    c.name AS customer_name,
                    p.title AS project_title,
                    t.title AS type_title
                FROM
                    `#__tasks_bills_items` AS bi 
                LEFT JOIN `#__tasks_items` AS i
                    ON bi.task_id = i.id 
                LEFT JOIN `#__tasks_customers` AS c
                    ON i.customer_id = c.id
                LEFT JOIN `#__tasks_projects` AS p
                    ON i.project_id = p.id
                LEFT JOIN `#__tasks_types` AS t
                    ON i.type_id = t.id
                WHERE
                    bi.bill_id = :bill_id
                ORDER BY
                    i.date ASC";

        $sth = $dbh->prepare($query);

        $sth->execute(array(
            'bill_id' => $bill_id
        ));

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function getBillSum($bill_id) {

        $result = 0;

        $dbh = Registry::get('dbh'); 

        $query="SELECT 
                    bi.bill_id AS bill_id,
                    sum(i.price*i.count) AS bill_sum
                FROM
                    `#__tasks_bills_items` AS bi 
                LEFT JOIN `#__tasks_items` AS i
                    ON bi.task_id = i.id 
                WHERE
                    bi.bill_id = :bill_id
                GROUP BY
                    bi.bill_id";

        $sth = $dbh->prepare($query);

        $sth->execute(array(
            'bill_id' => $bill_id
        ));

        $data = $sth->fetch(PDO::FETCH_OBJ);

        if (isset($data->bill_sum) && $data->bill_sum > 0) {
            $result = $data->bill_sum;
        }

        return $result;

    }

}