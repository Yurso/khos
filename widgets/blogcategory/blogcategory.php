<?php
Class BlogCategoryWidget Extends WidgetBase {

	public $params = array(
		'class' => '',
		'category_id' => 0,
		'show_title' => 1,
		'show_image' => 1,
		'orderby' => 'public_date',
		'ordering' => 'ASC'
	);

	public function display() {

		$config = Registry::get('config');
		$route = Registry::get('route');

		$result = '';

		if (isset($this->params['category_id']) && intval($this->params['category_id']) > 0) {

			$category_id = intval($this->params['category_id']);

			$items = $this->getItems($category_id);

			$result .= '<div class="w-blog-category">';

			foreach ($items as $item) {

				$url = '/content/blog/post/'.$item->id;
				$image = $config->blog_images_path.$item->image_name;
				$image_thumb = $config->blog_images_path.'thumbs/'.$item->image_name;
				$class = '';

				if ($route->current == $url) {
					$class = 'active';
				}

				$result .= '<div class="wbc-item wbc-item'.$item->id.' '.$class.'" id="">';
				
				if ($this->params['show_image'] && !empty($item->image_name)) {
					$result .= '<div class="wbc-item-image">';
					$result .= '	<a href="'.$url.'">';
					$result .= '		<img src="'.$image_thumb.'" alt="'.$item->title.'">';
					$result .= '	</a>';
					$result .= '</div>';
				}

				if ($this->params['show_title']) {
					$result .= '<div class="wbc-item-title"><a href="'.$url.'">'.$item->title.'</a></div>';
				}

				$result .= '</div>';

			}	

			$result .= '</div>';

		}

		echo $result;

	}

	private function getItems($category_id) {

		$dbh = Registry::get('dbh');

		$orderby = $this->params['orderby'];
		$ordering = $this->params['ordering'];

        $query="SELECT *
        		FROM `#__blog` AS b
        		WHERE b.state > 0 AND b.category_id = :category_id
        		ORDER BY $orderby $ordering";  

        $sth = $dbh->prepare($query);

        $params = array(
        	'category_id' => $category_id);

        $sth->execute($params);

        return $sth->fetchAll(PDO::FETCH_OBJ);

	}
	
}