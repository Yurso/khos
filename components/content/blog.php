<?php
Class ContentBlogController Extends ControllerBase {

	public function index() {

		$model = $this->getModel('blog');

		$items = $model->getItems();

		foreach ($items as $key => $item) {
			$items[$key]->tags = $this->_prepareTags($item->tags);					
		}

		$template = new template;

		$template->setVar('items', $items);

		$template->display('blog');
		
	}

	public function test() {
		$template = new template;
		$template->display('test');
	}

	public function test_ajax() {

		$query = $_REQUEST['q'];

		$model = $this->getModel('test');

		$questions = $model->getQuestions($query);

		foreach ($questions as $key => $question) {
			$question->q_text = explode("\n", $question->q_text)[0];
		}

		echo json_encode($questions);

	}

	public function hello() {
		
		header("Access-Control-Allow-Origin: *");

		if (!isset($_POST['secret'])) {
		    echo 'wrong secret';
         	return false;
		}
		
		if ($_POST['secret'] !== '38cd9d175d1037cd28bd750c13b8198d') {
		    echo 'wrong secret';
         	return false;
		}

		$dbh = Registry::get('dbh');

		$query = "SELECT COUNT(q_text) AS count FROM `#__questions` WHERE q_text = :q_text";
		$sth = $dbh->prepare($query);
        $sth->execute(
            array(
                'q_text' => trim($_POST['q_text'])
            )
        );

        if ($sth->fetch(PDO::FETCH_OBJ)->count > 0) {
         	echo 'exist';
         	return false;
        }

		$query = "INSERT INTO `#__questions` (q_part, q_number, q_text, q_choices, q_answer, q_test_id) VALUES (:q_part, :q_number, :q_text, :q_choices, :q_answer, :q_test_id)";
		$sth = $dbh->prepare($query);
        $sth->execute(
         	array(
         		'q_part' => intval($_POST['q_part']),
         		'q_number' => intval($_POST['q_number']),
         		'q_text' => trim($_POST['q_text']),
         		'q_choices' => trim($_POST['q_choices']),
         		'q_answer' => trim($_POST['q_answer']),
         		'q_test_id' => intval($_POST['q_test_id']),
            )
        );
        
        echo 'ok';

	}

	public function post() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('blog');

			if ($model->itemExist($id)) {
				
				$item = $model->getItem($id);			
				$item->tags = $this->_prepareTags($item->tags);

				$comments = $model->getComments($id);

				// $pathway = new Pathway;
				// $pathway->addItem('Блог', '/blog');
				// $pathway->addItem($item->title, '');

				$tmpl = new template;

				$tmpl->setVar('item', $item);
				$tmpl->setVar('comments', $comments);

				$tmpl->setTitle($item->title);

				$tmpl->display('blog_post');	
			}

		}

	}

	public function get_comments() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$post_id = (int) $args[0];

			$model = $this->getModel('blog');

			if ($model->itemExist($post_id)) {

				$comments = $model->getComments($post_id);

				$tmpl = new Template;
				$tmpl->setVar('comments', $comments);
				$tmpl->display('blog_comments_list', 'ajax');

			}

		}

	}

	public function add_comment() {

		$result = array('success' => false, 'message' => '');

		if (isset($_POST['username']) && isset($_POST['comment']) && !empty($_POST['username']) && !empty($_POST['comment'])) {

			$model = $this->getModel('comments');

			$data = array();	
			$data['title'] = strip_tags($_POST['username']) . ' оставил комментарий';
			$data['controller'] = 'blog';
			$data['item_id'] = (int) $_POST['item_id'];
			$data['state'] = 1;
			$data['comment'] = strip_tags($_POST['comment']);
			$data['user_id'] = 0;
			$data['name'] = strip_tags($_POST['username']);
			$data['email'] = strip_tags($_POST['email']);
			$data['website'] = '';
			$data['create_date'] = date("Y-m-d H:i:s");
			$data['edit_date'] = date("Y-m-d H:i:s");

			$id = $model->SaveNewItem($data);

			if ($id > 0) {
				//Main::Redirect('/blog/post/' . $data['item_id'], 'Ваш комментарий успешно сохранен.');
				$result['success'] = true;
				$result['message'] = 'Ваш комментарий успешно сохранен.';
				$result['comment'] = $data['comment'];
				$result['username'] = $data['name'];
				$result['create_date'] = $data['create_date'];
			} else {
				//Main::Redirect('/blog/post/' . $data['item_id'], 'Произошла ошибка при записи комментария. Пожалуйста повторите еще раз.');
				$result['message'] = 'Произошла ошибка при записи комментария. Пожалуйста повторите еще раз.';
			}

		} else {

			//Main::Redirect('/blog/post/' . (int) $_POST['item_id'], 'Не заполнены все обязательные поля.');
			$result['message'] =  'Произошла ошибка при записи комментария. Пожалуйста повторите еще раз.';

		}

		echo stripslashes(json_encode($result));

	}

	public function delete_comment() {

		if (User::getUserAccessName() == 'administrator' || User::getUserAccessName() == 'manager') {

			$result = array('success' => false, 'message' => '');

			$args = Registry::get('route')->args;		

	    	if (isset($args[0])) {

	    		$model = $this->getModel('comments');

		    	$id = (int) $args[0];	    	

		    	if ($model->itemExist($id)) {

			    	if ($model->deleteItem($id)) {
			    		$result['success'] = true;
			    		$result['message'] = 'Элемент успешно удален';
			    	} else {
			    		$result['message'] = 'Не удалось удалить элемент';
			    	}

		    	} else {
		    		$result['message'] = 'Такого элемента не существует';
		    	}
		    }

		    echo stripslashes(json_encode($result));

		}
		
	}

	public function tag() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$tag = $args[0];

			$model = $this->getModel('blog');

			$items = $model->getItemsByTag($tag);	

			foreach ($items as $key => $item) {
				$items[$key]->tags = $this->_prepareTags($item->tags);					
			}

			// $pathway = new Pathway;
			// $pathway->addItem('Блог', '/blog');
			// $pathway->addItem('Поиск по тегу "' . $tag . '"', '');

			$template = new template;

			$template->setVar('items', $items);

			$template->display('blog');
		}	

	}

	public function category() {
		
		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('blog');

			$items = $model->getItemsByCategory($id);	

			foreach ($items as $key => $item) {
				$items[$key]->tags = $this->_prepareTags($item->tags);					
			}

			$category = $model->getCategoryInfo($id);

			$tmpl = new template;

			$tmpl->setVar('items', $items);

			$tmpl->setTitle($category->title);

			$tmpl->display('blog');
		}	

	}

	function actions() {

		$user = new User;

		if ($user->getUserAccessName() == 'administrator') {

			$data = array();

			$term = (isset($_GET['term'])) ? $_GET['term'] : '';

			$model = $this->getModel('blogadmin');

			$categories = $model->getCategoriesList();

			foreach ($categories as $key => $item) {				
				
				$data_item = array(
					'value' => 'category/' . $item->id . '-' . $item->alias,
					'label' => $item->id . ' - ' . $item->title,
					'category' => 'Categories'
				);

				if (!empty($term) && stripos($data_item['value'], $term) === false && stripos($data_item['label'], $term) === false) continue;

				$data[] = $data_item;

			}

			$items = $model->getItems();

			foreach ($items as $key => $item) {

				$data_item = array(
					'value' => 'post/' . $item->id . '-' . $item->alias,
					'label' => $item->id . ' - ' . $item->title,	
					'category' => 'Posts'
				);
				
				if (!empty($term) && stripos($data_item['value'], $term) === false && stripos($data_item['label'], $term) === false) continue;

				$data[] = $data_item;

			}

			echo stripslashes(json_encode($data, JSON_UNESCAPED_UNICODE));
		}

	}

	// OTHER FUNCTIONS //

	private function _prepareTags($tags) {

		$result = array();

		if (!empty($tags)) {
			$result = explode(', ', $tags);	
		}

		return $result;	

	}

}