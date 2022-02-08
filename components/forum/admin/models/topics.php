<?php
Class ForumTopicsModel Extends ModelBase {

    public $table = "#__forum_topics";

    // Defaults vars
    public $default_ordering = array('column' => 'last_message_date', 'sort' => 'DESC');

 //    protected function _buildItemQuery() {

 //        $query  = $this->_buildItemsQuery();

 //        $query .= " WHERE r.id = :id";

 //        return $query;

 //    }

    protected function _buildItemsQuery() {

		$query="SELECT 
                    t.*,
                    c.title AS category_title,
                    u.name AS user_name
                FROM 
                    `$this->table` AS t 
                LEFT JOIN `#__users` AS u
                    ON t.author_id = u.id
                LEFT JOIN `#__categories` AS c
                    ON t.category_id = c.id";	            

        return $query;

	}

    public function getCategoryInfo($id) {

        $dbh = Registry::get('dbh');

        $params = array('id' => intval($id));

        $query="SELECT id, title, alias, parent_id, state, component
                FROM `#__categories`
                WHERE id = :id";           

        $sth = $dbh->prepare($query);

        $sth->execute($params);

        return $sth->fetch(PDO::FETCH_OBJ);

    }

    public function checkSubscribtion($topic_id, $user_id) {

        $dbh = Registry::get('dbh');

        $params = array(
            'topic_id' => intval($topic_id),
            'user_id' => intval($user_id)
        );

        $query="SELECT COUNT(*) as count FROM `#__forum_subscriptions`
                WHERE topic_id = :topic_id AND user_id=:user_id";           

        $sth = $dbh->prepare($query);

        $sth->execute($params);

        $data = $sth->fetch(PDO::FETCH_OBJ);
        
        return $data->count;

    }

    public function subscribe($topic_id, $user_id) {

        $dbh = Registry::get('dbh');

        $params = array(
            'topic_id' => intval($topic_id),
            'user_id' => intval($user_id)
        );

        $query="INSERT INTO `#__forum_subscriptions` (topic_id, user_id)
                VALUES (:topic_id, :user_id)";           

        $sth = $dbh->prepare($query);

        return $sth->execute($params);

    }

    public function unsubscribe($topic_id, $user_id) {

        $dbh = Registry::get('dbh');

        $params = array(
            'topic_id' => intval($topic_id),
            'user_id' => intval($user_id)
        );

        $query="DELETE FROM `#__forum_subscriptions`
                WHERE topic_id = :topic_id AND user_id=:user_id";           

        $sth = $dbh->prepare($query);

        return $sth->execute($params);

    }

    public function searchItems($query) {

        $dbh = Registry::get('dbh');
        $params = array();

        $parts = explode(' ', $query);

        $query = $this->_buildItemsQuery();

        $query .= " WHERE 1";

        foreach ($parts as $key => $part) {
            $query .= " AND t.title LIKE :part$key";
            $params['part'.$key] = "%".$part."%";
        }

        $sth = $dbh->prepare($query);

        $sth->execute($params);

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function getLastMessages($count = 5) {

        $dbh = Registry::get('dbh');

        $query  =  "SELECT 
                        messages.*,
                        topics.title AS topic_title,
                        users.name AS author_name,
                        topics.category_id AS category_id,
                        categories.title AS category_title
                    FROM 
                        `#__forum_messages` AS messages
                    LEFT JOIN `#__forum_topics` AS topics
                        ON messages.topic_id = topics.id
                    LEFT JOIN `#__users` AS users
                        ON messages.author_id = users.id
                    LEFT JOIN `#__categories` AS categories 
                        ON topics.category_id = categories.id
                    WHERE 
                        messages.state > 0 
                    ORDER BY 
                        messages.create_date DESC
                    LIMIT $count";

        $sth = $dbh->prepare($query);

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

}