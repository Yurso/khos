<?php
Class SystemWidgetsController Extends ControllerBase {

	function index() {

		$model = $this->getModel('widgets');

		$model->initUserOrdering();

		// FILTERS ADRESS
		$filter = new Filter;
		$filter->name = 'title';
		$filter->column = 'title';
		$filter->title = 'Заголовок';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// FILTERS ADRESS
		$filter = new Filter;
		$filter->name = 'position';
		$filter->column = 'position';
		$filter->title = 'Позиция';
		$filter->operator = 'LIKE';
		
		$model->_setFilter($filter);

		// FILTERS CATEGORY
		$filter = new Filter;
		$filter->name = 'widget';
		$filter->column = 'widget';
		$filter->title = 'Виджет';
		$filter->setValues($model->getWidgetsList(), 'name', 'name');		

		$model->_setFilter($filter);

		// $model->addFilter('title', 'Заголовок');

		// $widgets = $model->getWidgetsList();	
		// $filter_values = array();
		// foreach ($widgets as $widget) {
		// 	$filter_values[$widget->name] = $widget->name;
		// }
		// $model->addFilter('widget', 'Виджет', $filter_values);

		$pagination = $model->initPagination();
		
		$items = $model->getItems();

		$tmpl = new template;

		$tmpl->setVar('items', $items);
		$tmpl->setVar('pagination', $pagination);
		$tmpl->setVar('filters', $model->filters);

		$tmpl->setTitle('Виджеты');

		$tmpl->display('widgets');

	}

	private function _buildButtonsArray() {

		$buttons = array();		
		
		$buttons[] = array(
			'title' => 'Сохранить',
			'action' => '/save?close=1'
		);
		$buttons[] = array(
			'title' => 'Применить',
			'action' => '/save'
		);
		$buttons[] = array(
			'title' => 'Закрыть',
			'action' => '/'
		);

		return $buttons;

	}

	function create() {

		$model = $this->getModel('widgets');

		$widgets = $model->getWidgetsList();

		$widget = new stdClass;
		$widget->id = 0;
		$widget->title = '';
		$widget->widget = '';
		$widget->position = '';
		$widget->state = 1;
		$widget->params = array();
		$widget->show_type = 'all';
		$widget->show_list = '';

		$buttons = $this->_buildButtonsArray();

		$template = new template;

		$template->setVar('widget', $widget);
		$template->setVar('widgets', $widgets);
		$template->setVar('buttons', $buttons);

		$template->display('widgets_edit');

	}

	function edit() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$model = $this->getModel('widgets');

			$id = (int) $args[0];

			$widget = $model->getItem($id);						

			$widgets = $model->getWidgetsList();

			$buttons = $this->_buildButtonsArray();

			$template = new template;

			$template->setVar('widget', $widget);
			$template->setVar('widgets', $widgets);
			$template->setVar('buttons', $buttons);

			$template->display('widgets_edit');

		} else {
			Main::redirect('/admin/system/widgets', 'Ошибка! Не указан идентификатор виджета');
		}


	}

	function save() {

		Widgets::trigger('onBeforeWidgetSave', $_POST['widget']);

		$model = $this->getModel('widgets');

		$id = (int) $_POST['id'];

		$data = array();
		$params = array();
		
		if (isset($_POST['params'])) {
			$params = $_POST['params'];
		}
		
		$data['title'] = $_POST['wpname'];
		$data['widget'] = $_POST['widget'];
		$data['position'] = $_POST['position'];
		$data['state'] = $_POST['state'];
		$data['params'] = serialize($params);
		$data['show_type'] = $_POST['show_type'];
		$data['show_list'] = $_POST['show_list'];		

		$succes = false;
		# If it's new element
		if ($id == 0) {
			$id = $model->SaveNewItem($data);
			if ($id > 0) {
				$succes = true;
			}			
		} elseif ($id > 0) {
			if ($model->SaveItem($id, $data)) {
				$succes = true;
			}
		}

		// Additional save process
		if ($id > 0) {
			Widgets::trigger('onAfterWidgetSave', $_POST['widget']);
		}

		// Set redirect values
		if ($succes) {
			$message = 'Запись успешно сохранена.';
			$redirect = '/admin/system/widgets/edit/' . $id;
		} elseif ($id > 0) {
			$message = 'Произошла ошибка при записи. Данные не сохранены.';
			$redirect = '/admin/system/widgets/edit'.$id; 			
		} else {
			$message = 'Произошла ошибка при записи. Данные не сохранены.';
			$redirect = '/admin/system/widgets/create'; 
		}

		// redirect to items list if user press save 
		if (isset($_GET['close']) && $_GET['close'] == 1) {
			$redirect = '/admin/system/widgets';		
		}
		// another redirect
		if (isset($_GET['ref'])) {
			$redirect = trim($_GET['ref']);		
		}

		Main::Redirect($redirect, $message);

	}

    function delete() {

		$i = 0;

		$args = Registry::get('route')->args;

		$model = $this->getModel('widgets');

    	if (isset($args[0])) {

	    	$id = (int) $args[0];	    	

	    	if ($model->deleteItem($id)) {
	    		Main::Redirect('/admin/system/widgets', 'Элемент успешно удален');
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

	   		Main::redirect('/admin/system/widgets', 'Успешно удалено ' . $i . ' элементов');

	   	} else {
	   		Main::Redirect('/admin/system/widgets', 'Не указан id элемента');
	   	}
		
	}

	function params() {

		$args = Registry::get('route')->args;

		if (isset($args[0])) {

			$wname = $args[0];

			echo widgets::getParamsForm($wname, array());

			// $model = $this->getModel('widgets');

			// $params = $model->getWidgetParams($wname);

			// foreach ($params as $key => $value) {
			// 	echo '<div class="block-item">';
			// 	echo '	<label>' . $key . '</label><br />';
			// 	echo '	<input type="text" name="params[' . $key . ']" value="' . $value . '" >';
			// 	echo '</div>';
			// }

		}

	}
    
    function sort() {                  
        
        if (isset($_POST['item'])) {
	        
	        $ordering = $_POST['item'];

	        $model = $this->getModel('widgets');
	        
	        foreach ($ordering as $order => $id) {                
	            
	            $model->SaveItem($id, array('ordering' => $order));

	        }
        }

    }

}