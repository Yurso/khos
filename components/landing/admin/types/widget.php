<?php
Class LandingWidgetType Extends LandingTypeBase {

    public function getView($item) {

        if (isset($item->params['widget_id']) && $item->params['widget_id'] > 0) {
            ob_start();            
            widgets::display($item->params['widget_id']);
            $item->content = ob_get_contents();
            ob_end_clean();
        }

        return parent::getView($item);

    }

    private function getWidgets() {

        $dbh = Registry::get('dbh');

        $query="SELECT id, title, widget
                FROM `#__widgets` 
                WHERE state > 0";

        $sth = $dbh->prepare($query);

        $sth->execute();

        $items = $sth->fetchAll(PDO::FETCH_OBJ);

        foreach ($items as $item) {
            $item->title = $item->title . ' (' . $item->widget . ')';
        }

        return $items;

    }

	public function getEditForm($item) {

        $result  = ''; 

		return $result;

	}

	public function getParamsForm($params) {

        $result = '';

        // widget_id param
        $widgets = $this->getWidgets();
        $param_name = 'widget_id';
        if (!isset($params[$param_name])) {
            $params[$param_name] = 0;
        }

        $result .= '
            <div class="block-item">
                <label>Виджет:</label><br />
                '.htmler::selectListByObjectsArray($widgets, 'id', 'title', 'params[widget_id]', $params[$param_name]).'
            </div>'; 

        // show_title param
		$param_name = 'show_title';
		if (!isset($params[$param_name])) {
			$params[$param_name] = 1;
		}

		$result .= '
			<div class="block-item">
	            <label>Показывать заголовок:</label><br />
	            '.htmler::booleanSelect($params[$param_name], 'params['.$param_name.']').'
	        </div>';  

		return $result;

	}

}