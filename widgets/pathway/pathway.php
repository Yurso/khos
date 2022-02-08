<?php
Class PathwayWidget {

	public $params = array(
		'class' => ''
		);

	function display() {

		$result = '';

		$items = Registry::get('pathway');

		if (isset($items)) {

			$i = 0;
			
			foreach ($items as $key => $item) {
				$i++;
				
				if (empty($item->url)) {
					$result .= $item->title;
				} else {
					$result .= '<a href="'.$item->url.'">'.$item->title.'</a>';
				}					

				if (count($items) > $i) $result .= '<span>&nbsp;&gt;&nbsp;</span>';
			}	

			echo $result;
		}		

	}

}