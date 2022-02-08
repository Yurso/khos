<?php
Class ForumIndexController Extends ControllerBase {

	function index() {

		$model = $this->getModel('topics');

		$categories = $model->getCategoriesList();

		$last_messages = $model->getLastMessages();

		$tmpl = new template;

		$tmpl->setVar('items', $categories);		
		$tmpl->setVar('last_messages', $last_messages);	

		$tmpl->display('index');

	}

	function search() {

		// If not empty search query
		if (isset($_POST['query']) && !empty($_POST['query'])) {

			$query = trim($_POST['query']);

			$min_len = 3;

			if (mb_strlen($query) < 3) {
				Main::Redirect('/admin/forum', 'Слишком короткий поисковый запрос. Поиск должен содержать минимум '.$min_len.' символа.');
			}

			// Get topics model
			$model = $this->getModel('topics');
			// Find topics
			$topics = $model->searchItems($query);
			// Get messages model
			$model = $this->getModel('messages');
			// Find topics
			$messages = $model->searchItems($query);

			// Set pathway for the page
			$pathway = new Pathway;				
			$pathway->addItem('Форум', '/admin/forum');
			$pathway->addItem('Поиск', '');			

			// Set template params
			$tmpl = new Template;

			$tmpl->setVar('topics', $topics);
			$tmpl->setVar('messages', $messages);
			$tmpl->setVar('query', $query);
			$tmpl->setVar('query_parts', explode(' ', $query));

			$tmpl->display('search');

		} else {

			Main::Redirect('/admin/forum');

		}

	}

}