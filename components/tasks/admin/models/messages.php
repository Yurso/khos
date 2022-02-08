<?php
Class TasksMessagesModel Extends ModelBase {

    public $table = "#__tasks_messages";

    public function addMessage($task_id, $message) {

    	$user = Registry::get('user');

    	return $this->SaveNewItem(array(
            'user_id' => $user->id,
            'task_id' => $task_id,
            'name' => $user->name,
            'text' => $message
        ));

    }

}