<?php
Class ApiController Extends ControllerBase {

	public function __construct() {

		$conf = new Configuration;

		if (!isset($conf->EnableApi) || $conf->EnableApi == 0) {
			die ('404 Not Found');
		}

	}

	public function index() {

		echo 'CMS API';

	}

	public function test() {

		$tmpl = new Template;

		$tmpl->display('api_test', 'admin');

	}

	public function login() {

		$result = array(
			'success' => false
		);

		if ($this->_checkInput('email, password')) {

			if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {

                $email = $_POST['email'];

                $model = $this->getModel('users');                              

                $user = $model->getUserByEmail($email);            

                # checking passwords
                if (isset($user->id) && $user->activated && ($user->password === md5(md5($_POST['password']))))
                {                                    

                	if ($user->access_name == 'administrator') {
                		
                		$hash = $model->updateUserHash($user->id);
                		$result['success'] = true;
                		$result['key'] = $hash; 

                	}

                }
            }

		}

		echo stripslashes(json_encode($result));

	}

	public function getBlogPostsList() {

		$result = array(
			'success' => false
		);

		if ($this->_checkInput('key')) {

			$key = $_POST['key'];

			$model = $this->getModel('users');

			$user = $model->getUserByHash($key);

			if (isset($user->id) && $user->access_name == 'administrator') {

				$blog_model = $this->getModel('blogadmin');

				$items = $blog_model->getItems();

				$result['success'] = true;
				$result['items'] = $items;

			}

		}		

		echo stripslashes(json_encode($result));

	}

	public function getBlogCategoriesList() {

		$result = array(
			'success' => false
		);

		if ($this->_checkInput('key')) {

			$key = $_POST['key'];

			$model = $this->getModel('users');

			$user = $model->getUserByHash($key);

			if (isset($user->id) && $user->access_name == 'administrator') {

				$blog_model = $this->getModel('blogadmin');

				$items = $blog_model->getCategoriesList();

				$result['success'] = true;
				$result['items'] = $items;

			}

		}		

		echo stripslashes(json_encode($result));

	}


	public function addBlogPost() {

		$result = array(
			'success' => false
		);

		if ($this->_checkInput('key, title, category_id, state, content')) {

			$key = $_POST['key'];

			$model = $this->getModel('users');

			$user = $model->getUserByHash($key);

			if (isset($user->id) && $user->access_name == 'administrator') {

				$blog_model = $this->getModel('blogadmin');

				$data = array();
				$data['title'] = $_POST['title'];				
				
				if (empty($_POST['alias'])) {			
					$alias = Main::str2url($data['title']);	
				} else {
					$alias = $_POST['alias'];	
				}

				$data['alias'] = $blog_model->finishAlias($alias);					
				$data['category_id'] = (int) $_POST['category_id'];
				$data['state'] = (int) $_POST['state'];
				$data['content'] = $_POST['content'];
				$data['comments'] = $this->_getInput('comments', '');
				$data['author_id'] = $user->id;
				$data['tags'] = $this->_getInput('tags', '');
				$data['create_date'] = $this->_getInput('create_date', date("Y-m-d H:i:s"));
				$data['edit_date'] = $this->_getInput('edit_date', date("Y-m-d H:i:s"));
				$data['public_date'] = $this->_getInput('public_date', date("Y-m-d H:i:s"));

				$id = $blog_model->SaveNewItem($data);

				if ($id > 0) {
					$result['success'] = true;
					$result['id'] = $id;
				}

			}

		}		

		echo stripslashes(json_encode($result));

	}

	public function getPagesList() {

		$result = array(
			'success' => false
		);

		if ($this->_checkInput('key')) {

			$key = $_POST['key'];

			$users_model = $this->getModel('users');

			$user = $users_model->getUserByHash($key);

			if (isset($user->id) && $user->access_name == 'administrator') {

				$pages_model = $this->getModel('pages');

				$items = $pages_model->getItems();

				$result['success'] = true;
				$result['items'] = $items;

			}

		}		

		echo stripslashes(json_encode($result));

	}

	public function addPageItem() {

		$result = array(
			'success' => false
		);

		if ($this->_checkInput('key, title, state, content')) {

			$key = $_POST['key'];

			$users_model = $this->getModel('users');

			$user = $users_model->getUserByHash($key);

			if (isset($user->id) && $user->access_name == 'administrator') {

				$pages_model = $this->getModel('pages');

				$data = array();
				$data['title'] = $_POST['title'];				
				
				if (empty($_POST['alias'])) {			
					$alias = Main::str2url($data['title']);	
				} else {
					$alias = $_POST['alias'];	
				}

				$data['alias'] = $pages_model->finishAlias($alias);									
				$data['state'] = (int) $_POST['state'];
				$data['content'] = $_POST['content'];
				$data['comments'] = $this->_getInput('comments', '');
				$data['author_id'] = $user->id;				
				$data['create_date'] = $this->_getInput('create_date', date("Y-m-d H:i:s"));
				$data['edit_date'] = $this->_getInput('edit_date', date("Y-m-d H:i:s"));				

				$id = $pages_model->SaveNewItem($data);

				if ($id > 0) {
					$result['success'] = true;
					$result['id'] = $id;
				}

			}

		}		

		echo stripslashes(json_encode($result));

	}

	public function logout() {

		$result = array(
			'success' => false
		);

		if ($this->_checkInput('key')) {

			$key = $_POST['key'];

			$model = $this->getModel('users');

			$user = $model->getUserByHash($key);

			if (isset($user->id) && $user->access_name == 'administrator') {				

            	$hash = $model->updateUserHash($user->id);

            	$result['success'] = true;

			}

		}

		echo stripslashes(json_encode($result));

	}

	private function _checkInput($input) {

		$result = true;

		$parts = explode(', ', $input);

		foreach ($parts as $part) {

			if (!isset($_POST[$part])) {
				$result = false;
				break;
			}

			if ($_POST[$part] <> '0' && empty($_POST[$part])) {
				$result = false;
				break;
			}

		}

		return $result;

	}

	private function _getInput($key, $return = '') {

		if (!empty($key)) {

			if (isset($_POST[$key])) {
				$return = $_POST[$key];
			}

		}

		return $return;

	}

}