<?php
Class ContentPagesController Extends ControllerBase {

	function index() {
		
		$model = $this->getModel('pages');

		$model->initUserOrdering();

		// FILTERS ADRESS
		$filter = new Filter;
		$filter->name = 'title';
		$filter->column = 'p.title';
		$filter->title = 'Заголовок';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		$pagination = $model->initPagination();

		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Страницы');

		$tmpl->display('pages');
	}

	function create() {

		$data = new stdClass;
		$data->id = 0;
		$data->title = '';
		$data->alias = '';
		$data->state = 1;
		$data->content = '';
		$data->comments = '';
		$data->author_name = '';
		$data->create_date = date("Y-m-d H:i:s");
		$data->edit_date = date("Y-m-d H:i:s");		

		$template = new template;

		$template->setVar('data', $data);

		$template->display('pages_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$id = (int) $args[0];

			$model = $this->getModel('pages');

			if ($model->itemExist($id)) {

				$data = $model->getItem($id);

				$template = new Template;

				$template->setVar('data', $data);

				$template->display('pages_edit');

			} else {
				Main::redirect('/admin/content/pages', 'Ошибка! Элемент с таким id не найден.');
			}

		} else {
			Main::redirect('/admin/content/pages', 'Не указан id элемента');
		}

	}

	function save() {

		$model = $this->getModel('pages');

		$id = (int) $_POST['id'];

		$user = new User;
		$userData = $user->getUserData();

		$data = array();

		$data['title'] = $_POST['title'];

		if (empty($_POST['alias'])) {			
			$data['alias'] = $model->finishAlias(Main::str2url($data['title']), $id);	
		} else {
			$data['alias'] = $model->finishAlias($_POST['alias'], $id);	
		}

		$data['state'] = $_POST['state'];
		$data['content'] = $_POST['content'];
		$data['comments'] = $_POST['comments'];
		if ($id == 0) $data['author_id'] = $userData->id;

		$data['create_date'] = (!empty($_POST['create_date'])) ? $_POST['create_date'] : date("Y-m-d H:i:s");
		$data['edit_date'] = (!empty($_POST['edit_date'])) ? $_POST['edit_date'] : date("Y-m-d H:i:s");

		# If it's new element
		if ($id == 0) {

			$id = $model->SaveNewItem($data);

			if ($id > 0) {
				Main::Redirect('/admin/content/pages/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/content/pages', 'Ошибка! Не удалось сохранить запись.');
			}
			
		} elseif ($id > 0) {

			if ($model->SaveItem($id, $data)) {
				Main::Redirect('/admin/content/pages/edit/' . $id, 'Запись успешно сохранена.');
			} else {
				Main::Redirect('/admin/content/pages/edit/' . $id, 'Ошибка! Не удалось сохранить запись.');
			}

		}

	}

	function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('pages');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/content/pages', 'Элемент успешно удален');
	    	}

	   	} elseif (isset($_POST['checked'])) {

	   		foreach ($_POST['checked'] as $key => $value) {
	   			
	   			$id = (int) $value;

	   			if ($model->deleteItem($id)) {
	    			$i++;
	    		} else {
	    			Main::setMessage('Не удалось удалить элемент id = ' . $id);
	    		}

	   		}

	   		Main::redirect('/admin/content/pages', 'Успешно удалено ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/content/pages', 'Не указан id элемента');
	   	}
		
	}

	function duplicate() {

		if (isset($_POST['checked'])) {

			$model = $this->getModel('pages');

			$user = new User;
			$user_data = $user->getUserData();

			$i = 0;

			foreach ($_POST['checked'] as $value) {

				$id = (int) $value;
				 
				$item = $model->getItem($id);

				$data = array();
				$data['title'] = $item->title . ' (copy)';
				$data['alias'] = $model->finishAlias($item->alias);
				$data['state'] = $item->state;
				$data['content'] = $item->content;
				$data['comments'] = $item->comments;
				$data['author_id'] = $user_data->id;
				$data['create_date'] = date("Y-m-d H:i:s");
				$data['edit_date'] = date("Y-m-d H:i:s");				

				if ($model->SaveNewItem($data)) {
					$i++;
				}

			}

			if ($i > 0) {
				Main::Redirect('/admin/content/pages', 'Успешно скопировано ' . $i . ' элементов.');
			} else {
				Main::Redirect('/admin/content/pages', 'Нечего копировать.');
			}

		} else {
			Main::Redirect('/admin/content/pages', 'Нечего копировать.');
		}

	}

}