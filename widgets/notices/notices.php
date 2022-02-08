<?php
Class NoticesWidget {

	public $params = array(
		'class' => ''
		);

	public function display() {

		$result = file_get_contents(SITE_PATH . 'widgets' . DIRSEP . 'tmpl' . DIRSEP . 'notices.php');

		echo $result; 			

	}

}