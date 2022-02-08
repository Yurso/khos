<?php
Class LandingSliderType Extends LandingTypeBase {

    public function getView($item) {

        if (isset($item->params['category_id'])) {

            $catid = intval($item->params['category_id']);

            $item->content = $this->getImages($catid);

        }

        return parent::getView($item);

    }

    private function getImages($catid) {

        $dbh = Registry::get('dbh');

        $query="SELECT * 
                FROM `#__gallery_images` 
                WHERE category_id = :category_id 
                AND state > 0";

        $sth = $dbh->prepare($query);

        $sth->execute(array(
            'category_id' => $catid
        ));

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

	public function getEditForm($item) {

        $params = $item->params;

        $category_id = isset($params['category_id']) ? $params['category_id'] : 0;

		$categories = $this->getCategoriesList();

        $result  = '<div class="block-item">';
        $result .= '    <span class="small">Загрузите изображения в </span>';
        $result .= '    <a href="/admin/content/gallery" target="_blank" style="font-weight:normal;"><span class="small">Галерею</span></a>';
        $result .= '    <span class="small"> и выберите категорию для показа</span>';
        $result .= '</div>';

		$result .= '<div class="block-item">';
        $result .= '	<label>Категория галереи:</label><br>';
        $result .= htmler::selectListByObjectsArray($categories, 'id', 'title', 'params[category_id]', $category_id);
        $result .= '</div>';

		return $result;

	}

	public function getParamsForm($params) {

		$param_name = 'show_title';

		if (!isset($params[$param_name])) {
			$params[$param_name] = 1;
		}

		$result = '
			<div class="block-item">
	            <label>Показывать заголовок:</label><br />
	            '.htmler::booleanSelect($params[$param_name], 'params['.$param_name.']').'
	        </div>';

        // FULLSCREEN
        $param_name = 'fullscreen';

        if (!isset($params[$param_name])) {
            $params[$param_name] = 0;
        }

        $result .= '
            <div class="block-item">
                <label>Во весь экран:</label><br />
                '.htmler::booleanSelect($params[$param_name], 'params['.$param_name.']').'
            </div>';

        // CLASS
        $param_name = 'class';

        if (!isset($params[$param_name])) {
            $params[$param_name] = '';
        }

        $result .= 
            '<div class="block-item">
                <label>Класс блока:</label><br />
                <input type="text" name="params['.$param_name.']" value="'.$params[$param_name].'" />
            </div>';

        // ID
        $param_name = 'id';

        if (!isset($params[$param_name])) {
            $params[$param_name] = '';
        }

        $result .= 
            '<div class="block-item">
                <label>id блока:</label><br />
                <input type="text" name="params['.$param_name.']" value="'.$params[$param_name].'" />
            </div>';

        return $result;

	}

	public function onBeforeItemSave() {

	}

	public function onAfterItemSave($item) {


	}

	public function onItemDelete($item) {
		
	}	

	public function onItemDuplicate($item) {
		
	}

	private function getCategoriesList($include_main_category = false) {

        $dbh = Registry::get('dbh');

        $component = 'content';

        $query="SELECT id, title, alias, parent_id, state, component
                FROM `#__categories`";

        if ($include_main_category)
            $query .= " WHERE state > 0 AND (component = '$component' OR id = 1)";
        else
            $query .= " WHERE state > 0 AND component = '$component'";

        $query .= " ORDER BY ordering ASC, title ASC";

        $sth = $dbh->query($query);

        $items = $sth->fetchAll(PDO::FETCH_OBJ);

        return $this->sort_items_into_tree($items);
        
    }

    private function sort_items_into_tree($items, $parent_id = 0, $prefix = '') {
        
        $output = array();

        foreach ($items as $key => $item) {

            if ($item->parent_id == $parent_id) {
                
                $item->title = $prefix . $item->title;
                
                $output[] = $item;
                unset($items[$key]);

                $output = array_merge($output, $this->sort_items_into_tree($items, $item->id, $prefix . '- '));
            }
            
        }

        return $output;

    }

}