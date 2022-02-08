<?php
Class TasksMaintenanceModel {

    public function getFilesBeforeDate($date_before) {

        $dbh = Registry::get('dbh');

        $query = "
            SELECT 
                tf.*,
                ti.title
            FROM `khos_tasks_files` AS tf
            LEFT JOIN `khos_tasks_items` AS ti
            ON tf.task_id = ti.id
            WHERE ti.date < :date_before";

        $sth = $dbh->prepare($query);

        $sth->execute(array(
            'date_before' => $date_before
        ));

        $items = $sth->fetchAll(PDO::FETCH_OBJ);

        return $items;

    }

}