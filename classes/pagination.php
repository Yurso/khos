<?php
Class Pagination {

	public $itemsCount = 0;
	public $limitstart = 0;
	public $limitcount = 25;
	public $countOptions = array(5, 10, 15, 20, 25, 30, 50, 100, 0);
	public $showCountOptions = true;

	public function __construct() {

		$pagination = Main::getState('pagination', array());
	
		// limitstart from default
		if (isset($pagination['limitstart'])) {
			$this->limitstart = $pagination['limitstart'];
		}

		// limitcount from default
		if (isset($pagination['limitcount'])) {
			$this->limitcount = $pagination['limitcount'];
		}

		//params from get terms
		if (isset($_GET['limitstart'])) {
			$this->limitstart = (int) $_GET['limitstart'];
		} 

		//params from get terms
		if (isset($_POST['limitstart'])) {
			$this->limitstart = (int) $_POST['limitstart'];
		} 

		//params from get terms
		if (isset($_POST['limitcount'])) {
			
			$this->limitcount = (int) $_POST['limitcount'];
			// need to reset limitstart if limitcount changing 
			$this->limitstart = 0;

		}

		// save current information to session		
		Main::setState('pagination', array('limitstart' => $this->limitstart, 'limitcount' => $this->limitcount));

	}

	public function display() {		

		$result  = '<div class="pagination">';		

		if ($this->limitcount > 0) {

			$num_pages = ceil($this->itemsCount / $this->limitcount);

			$limitend = $this->limitstart + $this->limitcount;
			$cur_page = $limitend / $this->limitcount;

			if ($num_pages > 1) {

				$result .= '<ul class="pagination-pages">';

				for ($i=1; $i<=$num_pages; $i++) {

					if ($i == $cur_page) {
						$result .= '<li><span>' . $i . '</span></li>';	
					} else {
						$url = $_SERVER['SCRIPT_URL'];
						$limitstart = ($i - 1) * $this->limitcount;
						$result .= '<li><a href="'.$url.'?limitstart='.$limitstart.'">' . $i . '</a></li>';
					}		

				}

				$result .= '</ul>';
			}
		}

		if ($this->showCountOptions) {
			
			$result .= '<form method="post" action="'.$_SERVER['SCRIPT_URL'].'">';
			$result .= '<div class="pagination-counter">Строк на странце: ';

			$result .= '<select id="limitcount" name="limitcount" size="1" onchange="this.form.submit()">';

			foreach ($this->countOptions as $value) {
				
				if ($value == $this->limitcount) {
					$selected = 'selected';
				} else {
					$selected = '';
				}

				if ($value == 0) $value = 'Все';

				$result .= '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
			}

			$result .= '</select>';
			$result .= '<p>Всего объектов: <strong>' . $this->itemsCount . '</strong></p>';
			$result .= '</div>';
			$result .= '</form>';

			//$result .= '<div class="pagination-text">Страница '.$cur_page.' из '.$num_pages.'</div>';
			
		}

		$result .= '</div>';

		echo $result;

	}
}