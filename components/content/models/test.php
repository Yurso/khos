<?php
Class ContentTestModel Extends ModelBase {

    public $table = '#__questions';

    public function getQuestions($query) {

        $dbh = Registry::get('dbh');

        $sth = $dbh->prepare("SELECT *
                            FROM `#__tests_questions`
                            WHERE q_text LIKE :query
                            AND q_test_id = 2
                            LIMIT 10");

        $sth->execute(array('query' => '%'.$query.'%'));

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

}