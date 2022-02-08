<?php
Class ForumTopicsController Extends ControllerBase {

	function index() {}

	function view() {

		$args = Registry::get('route')->args;
		$model = $this->getModel('topics');		
		$model->initUserOrdering();

		if (isset($args[0])) {

			$category_id = intval($args[0]);

			$category = $model->getCategoryInfo($category_id);						

			$categories = $model->getCategoriesList();

			$model->setFilter('t.category_id', '=', $category_id);

			$pagination = $model->initPagination();

			$items = $model->getItems();

			$pathway = new Pathway;					
			$pathway->addItem('Форум', '/admin/forum');
			$pathway->addItem('Список тем форума', '');

			$tmpl = new template;

			$tmpl->setVar('items', $items);
			$tmpl->setVar('category', $category);
			$tmpl->setVar('categories', $categories);
			$tmpl->setVar('pagination', $pagination);
			$tmpl->setVar('filters', $model->filters);

			$tmpl->display('topics');

		} else {

			Main::Redirect('/admin/forum', 'Не указана категория');

		}

	}

	// Topic create page
	function create() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {		

			$model = $this->getModel('topics');

			$category_id = intval($args[0]);
			
			$category = $model->getCategoryInfo($category_id);

			if (isset($category->id) && !empty($category->title)) {

				$item = new stdClass;
				$item->id = 0;
				$item->title = '';
				$item->alias = '';
				$item->author_id = 1;
				$item->category_id = $category_id;	
				$item->state = 1;
				$item->create_date = date("Y-m-d H:i:s");
				$item->edit_date = date("Y-m-d H:i:s");
				$item->message = '';

				$pathway = new Pathway;					
				$pathway->addItem('Форум', '/admin/forum');
				$pathway->addItem($category->title, '/admin/forum/topics/'.$category_id);

				$tmpl = new template;

				$tmpl->setVar('item', $item);

				$tmpl->display('topics_edit');

			} else {

				Main::Redirect('/admin/forum', 'Такой категории не существует');

			}

		} else {

			Main::Redirect('/admin/forum', 'Не указана категория');

		}

	}

	// Topic save controller
	function save() {

		$redirect = '/admin/forum/';
		$message = '';

		if (isset($_POST['title']) && !empty($_POST['title']) && isset($_POST['message']) && !empty($_POST['message'])) {

			$model_topics = $this->getModel('topics');
			$user = user::getUserData();
			$topic_saved = false;			

			$params = array();
			$params['title'] = $_POST['title'];
			$params['alias'] = $model_topics->finishAlias(Main::str2url($_POST['title']));
			$params['author_id'] = $user->id;
			$params['category_id'] = $_POST['category_id'];
			$params['state'] = 1;
			$params['ordering'] = 0;
			$params['edit_date'] = date("Y-m-d H:i:s");		
			$params['create_date'] = date("Y-m-d H:i:s");			
			$params['last_message_date'] = date("Y-m-d H:i:s");
			$params['messages_count'] = 1;

			// Saving topic
			$id = $model_topics->SaveNewItem($params);

			if ($id > 0) {					

				$model_messages = $this->getModel('messages');

				$agency = $model_messages->getUserAgency($user->id);

				// saving first message
				$params = array();
				$params['message'] = $model_messages->finishMessage($_POST['message']);
				$params['message_html'] = $model_messages->finishMessage($_POST['message'], true);
				$params['topic_id'] = $id;
				$params['author_id'] = $user->id;
				if (isset($agency->id))
					$params['agency_id'] = $agency->id;
				$params['state'] = 1;
				$params['create_date'] = date("Y-m-d H:i:s");
				$params['edit_date'] = date("Y-m-d H:i:s");
				
				$message_id = $model_messages->SaveNewItem($params);

				// saving subscriptions
				$model_topics->subscribe($id, $user->id);

				// out information
				$redirect = '/admin/forum/messages/view/'.$id;
				$message = 'Создана новая тема форума';
			
			} else {

				$redirect = '/admin/forum/topics/create';
				$message = 'Произошла ошибка при сохранении записи';

			}
			

		} else {

			$redirect = '/admin/forum/topics/create';
			$message = 'Название и описание темы не может быть пустым';

		}

		Main::Redirect($redirect, $message);

	}

	// Close topic
	function close() {

		$model = $this->getModel('topics');		
		$user = User::getUserData();
		$args = Registry::get('route')->args;

		if (!isset($args[0])) {
			Main::Redirect('/admin/forum', 'Недостаточно параметров для выполнения операции');
		}

		$id = intval($args[0]);

		if (!$model->itemExist($id)) {
			Main::Redirect('/admin/forum', 'Темы не существует');
		}

		if ($user->access_name != 'administrator') {
			Main::Redirect('/admin/forum/messages/view/'.$id, 'Недостаточно прав для выполнения операции');
		}

		$params = array();
		$params['state'] = 0;

		if ($model->SaveItem($id, $params)) {
			Main::Redirect('/admin/forum/messages/view/'.$id, 'Тема успешно закрыта');
		} else {
			Main::Redirect('/admin/forum/messages/view/'.$id, 'Не удалось закрыть тему');			
		}

	}
	
	// Open topic
	function open() {

		$model = $this->getModel('topics');		
		$user = User::getUserData();
		$args = Registry::get('route')->args;

		if (!isset($args[0])) {
			Main::Redirect('/admin/forum', 'Недостаточно параметров для выполнения операции');
		}

		$id = intval($args[0]);

		if (!$model->itemExist($id)) {
			Main::Redirect('/admin/forum', 'Темы не существует');
		}

		if ($user->access_name != 'administrator') {
			Main::Redirect('/admin/forum/messages/view/'.$id, 'Недостаточно прав для выполнения операции');
		}

		$params = array();
		$params['state'] = 1;

		if ($model->SaveItem($id, $params)) {
			Main::Redirect('/admin/forum/messages/view/'.$id, 'Тема открыта заново');
		} else {
			Main::Redirect('/admin/forum/messages/view/'.$id, 'Не удалось открыть тему');			
		}

	}

	function subscribe() {

		$args = Registry::get('route')->args;

    	if (isset($args[0])) {

    		$topic_id = intval($args[0]);

			$model = $this->getModel('topics');				

			$user = User::getUserData();

			if ($model->itemExist($topic_id)) {

				// Топик создан, добавляем запись в таблицу подписок
				if (!$model->checkSubscribtion($topic_id, $user->id)) {					
					$model->subscribe($topic_id, $user->id);
				}

				// Переходим к топику
				Main::Redirect('/admin/forum/messages/view/'.$topic_id, 'Вы успешно подписаны на тему. Теперь вы будете получать уведомления о новых сообщения в данной теме.');

			} else {

				// Топика не существует
				Main::Redirect('/admin/forum', 'Такой темы не существует');

			}

    	}

	}

	function unsubscribe() {

		$args = Registry::get('route')->args;

    	if (isset($args[0])) {

    		$topic_id = intval($args[0]);

			$model = $this->getModel('topics');				

			$user = User::getUserData();

			if ($model->itemExist($topic_id)) {

				// Топик создан, добавляем запись в таблицу подписок
				$model->unsubscribe($topic_id, $user->id);

				// Переходим к топику
				Main::Redirect('/admin/forum/messages/view/'.$topic_id, 'Вы успешно отписались от темы. Вы больше не будете получать оповещение о новых сообщениях.');

			} else {

				// Топика не существует
				Main::Redirect('/admin/forum', 'Такой темы не существует');
				
			}

    	}

	}

}