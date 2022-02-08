<?php
Class Table {

	public $table = '';

	public $head = array();
	public $items = array();

	public function display() {

		$result  = '<table class="main-table">';

		$result .= '	<thead>';
		$result .= '		<tr>';

		foreach ($this->head as $key => $value) {
			$result .= '			<td>'.$value.'</td>';
		}

		$result .= '		</tr>';
		$result .= '	</thead>';

		$result .= '	<tbody>';

		foreach ($this->items as $key => $item) {
			
			$result .= '		<tr>';

			foreach ($this->head as $key => $value) {
				
				if (gettype($item) == 'object') {
					$result .= '			<td>'.$item->$key.'</td>';	
				} else {
					$result .= '			<td>'.$item[$key].'</td>';						
				}
				
			}

			$result .= '		</tr>';

		}

		$result .= '	</tbody>';

		$result .= '</table>';

		echo $result;

	}

    public function getItems() {

		$dbh = Registry::get('dbh');

		$query  = $this->_buildItemsQuery();
        $query .= $this->_buildItemsOrder();
        //$query .= $this->_buildItemsLimit();

        $sth = $dbh->query($query);

        $this->items = $sth->fetchAll(PDO::FETCH_OBJ);

	}

	protected function _buildItemsQuery() {

		return "SELECT * FROM `$this->table`";

	}

	protected function _buildItemsWhere() {

		return " WHERE id = :id";

	}

    protected function _buildItemsOrder() {

        return "";

    }

    protected function _buildItemsLimit() {

        $query = '';
        $limitcount = 0;

        # Get informaton of pagination
        if (isset($this->pagination)) {

            $limitstart = $this->pagination->limitstart;
            $limitcount = $this->pagination->limitcount;  

        } 

        # Limit block
        if ($limitcount > 0) {
                    
            $limitend = $limitstart + $limitcount; 

            $query .= " LIMIT " . $limitstart . ", " . $limitend;
        }

        return $query;

    }


}