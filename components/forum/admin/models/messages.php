<?php
Class ForumMessagesModel Extends ModelBase {

    public $table = "#__forum_messages";

    // Defaults vars
    public $default_ordering = array('column' => 'create_date', 'sort' => 'ASC');


    protected function _buildItemsQuery() {

		$query="SELECT 
                    m.*,
                    u.name AS author_name,
                    u.image AS author_avatar,
                    ua.name AS author_access,
                    ra.name AS agency_name,
                    ra.logo AS agency_logo,
                    t.title AS topic_title
                FROM 
                    `#__forum_messages` AS m
                LEFT JOIN `#__forum_topics` AS t
                    ON m.topic_id = t.id
                LEFT JOIN `#__users` AS u
                    ON m.author_id = u.id
                LEFT JOIN `#__users_access` AS ua
                    ON u.access = ua.id
                LEFT JOIN `#__realty_agencys` AS ra
                    ON u.agency_id = ra.id";	            

        return $query;

	}

	protected function _buildItemsOrder() {
		
        return ' ORDER BY create_date ASC';

	}

    protected function _buildItemQuery() {

        $query="SELECT 
                    messages.*,
                    topics.title AS topic_title,
                    topics.last_message_date AS last_message_date,
                    topics.category_id AS category_id,
                    categories.title AS category_title,
                    users.name AS author_name
                FROM 
                    `#__forum_messages` AS messages 
                LEFT JOIN `#__forum_topics` AS topics
                    ON messages.topic_id = topics.id
                LEFT JOIN `#__categories` AS categories
                    ON topics.category_id = categories.id
                LEFT JOIN `#__users` AS users
                    ON messages.author_id = users.id
                WHERE 
                    messages.id = :id";

        return $query;

    }

	public function getTopicInfo($id) {

		$dbh = Registry::get('dbh');

		$params = array('id' => intval($id));
		
		$query="SELECT t.*, c.title AS category_title 
                FROM `#__forum_topics` AS t 
                LEFT JOIN `#__categories` AS c
                ON t.category_id = c.id
                WHERE t.id = :id";            

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

    public function getUserAgency($user_id) {

        $dbh = Registry::get('dbh');

        $params = array('user_id' => intval($user_id));
        
        $query = "SELECT * FROM `#__realty_agencys_users` AS agencys_users
                    LEFT JOIN `#__realty_agencys` AS agencys
                    ON agencys.id = agencys_users.agency_id
                    WHERE agencys_users.user_id = :user_id";            

        $sth = $dbh->prepare($query);

        $sth->execute($params);

        return $sth->fetch(PDO::FETCH_OBJ);            

    }

    public function finishMessage($text, $to_html = false) {

        // cutting spaces
        $text = trim($text);
        // clear all html tags
        $text = strip_tags($text);

        if ($to_html) {
            // replace nl to br
            $text = nl2br($text);
            // convert links to anchors
            $text = preg_replace("#(https?|ftp)://\S+[^\s.,> )\];'\"!?]#",'<a href="\\0" target="_blank">\\0</a>', $text);
        }

        return $text;

    }

    public function sendEmails($message_id) {

        $dbh = Registry::get('dbh');
        $config = Registry::get('config');

        $message = $this->getItem($message_id);

        $params = array(
            'topic_id' => $message->topic_id,
            'author_id' => $message->author_id
        );

        $query="SELECT subs.user_id, users.name, users.email
                FROM `#__forum_subscriptions` AS subs
                LEFT JOIN `#__users` AS users
                ON subs.user_id = users.id
                WHERE subs.topic_id = :topic_id AND subs.user_id <> :author_id";           

        $sth = $dbh->prepare($query);

        $sth->execute($params);

        $subscriptions = $sth->fetchAll(PDO::FETCH_OBJ);

        foreach ($subscriptions as $subscription) {

            if (!empty($subscription->email)) {

                try {
            
                    $to         = $subscription->email;
                    $subject    = $message->topic_title.' ('.$config->SiteName.')';
                    $text       = $message->author_name . ' оставил(а) новое сообщение в теме, на которую вы подписаны.' . "\r\n\r\n";
                    $text      .= 'Для просмотра перейдите по ссылке:' . "\r\n";                        
                    $text      .= $config->BaseURL.'/admin/forum/messages/'.$message->topic_id.'#m'.$message->id;
                    
                    mail($to, $subject, $text);

                } catch (Exception $e) {

                    error_log("Can't send forum subscription email to ".$to." with error: "."\n".$e->getMessage(), 3, SITE_PATH.'logs'.DIRSEP.'error_log.txt');
 
                }

            }

        }

    }

    public function searchItems($query) {

        $dbh = Registry::get('dbh');
        $params = array();

        $parts = explode(' ', $query);

        $query = $this->_buildItemsQuery();

        $query .= " WHERE 1";

        foreach ($parts as $key => $part) {
            $query .= " AND m.message LIKE :part$key";
            $params['part'.$key] = "%".$part."%";
        }

        $query .= " ORDER BY m.create_date DESC";

        $sth = $dbh->prepare($query);

        $sth->execute($params);

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

}