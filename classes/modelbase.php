<?php
Class ModelBase {

    // Required vars
	public $table = '';
    public $pagination;    
    public $filters = array();
    public $default_ordering = array('column' => '', 'sort' => 'ASC');
    public $ordering = array();

    // Params
    //public $use_pagination = false;
    

    // Class constuction function
	public function __construct() {
		
		if (empty($this->table)) {
			
            $routeDate = Registry::get('route');

			$this->table = '#__' . $routeDate->controller;			

		}

	}    

    protected function _buildItemQuery() {

        $query = "SELECT * FROM `$this->table` WHERE id = :id";

        return $query;

    }

	protected function _buildItemsQuery() {

		return "SELECT * FROM `$this->table`";

	}

    protected function _buildItemsWhere() {

        $dbh = Registry::get('dbh');

        $query = ' WHERE true';

        foreach ($this->filters as $filter) { 

            // do not need to use filter for empty values
            if ($filter->operator == '=' AND $filter->value == '') continue;  
            
            $query .= ' AND ';            
            
            // set type of value
            if (isset($filter->type) && !empty($filter->type)) {
                $type = $filter->type;
            } else {
                $type = 'string';
            }

            $value = $filter->value;

            // adding % for like filters
            if ($filter->operator == 'LIKE') {
                $value = "%".$value."%";    
            } 

            // adding quetes to value
            if ($type == 'string') {
                $value = $dbh->quote($value);
            }
            
            // generate query
            $query .= $filter->column.' '.$filter->operator.' '.$value;             

        }      

        return $query;

    }

    protected function _buildItemsOrder() {

        $result = '';

        // $ordering = Main::getState($this->table.'_ordering', $this->default_ordering);       

        // if (isset($_GET['ordering_column'])) {            
        //     $ordering['column'] = $_GET['ordering_column'];
        // }

        // if (isset($_GET['ordering_sort'])) {            
        //     $ordering['sort'] = $_GET['ordering_sort'];
        // }

        // if (!empty($ordering['column'])) {

        //     $result = ' ORDER BY '.$ordering['column'].' '.$ordering['sort'];

        //     Main::setState($this->table.'_ordering', $ordering);
        //     Main::setState('current_ordering', $ordering);

        // }  

        if (isset($this->ordering['column']) && !empty($this->ordering['column'])) {
            
            $result = ' ORDER BY '.$this->ordering['column'].' '.$this->ordering['sort'];

        } elseif (!empty($this->default_ordering['column'])) {
            
            $result = ' ORDER BY '.$this->default_ordering['column'].' '.$this->default_ordering['sort'];

        }  

        return $result;

    }

    protected function _buildItemsLimit() {

        $query = '';
        //$limitcount = 0;

        # Set informaton of pagination
        if (isset($this->pagination->itemsCount)) {

            // $this->pagination = new Pagination;
            // $this->pagination->itemsCount = $this->getItemsCount();    

            // // reset limit start if page higher then items count
            // if ($this->pagination->itemsCount <= $this->pagination->limitstart) {
            //     $this->pagination->limitstart = 0;                
            // }

            // $limitstart = $this->pagination->limitstart;
            //$limitcount = $this->pagination->limitcount;             

            # Limit block
            if ($this->pagination->limitcount > 0) {
                $query .= " LIMIT " . intval($this->pagination->limitstart) . ", " . intval($this->pagination->limitcount);                
            }

        }

        return $query;

    }

	public function getItem($params = array()) {

        $dbh = Registry::get('dbh');

        $query = $this->_buildItemQuery();

        $sth = $dbh->prepare($query);

        if (gettype($params) != 'array') {
            $params = array('id' => (int) $params);
        }

        $sth->execute($params);

        return $sth->fetch(PDO::FETCH_OBJ);

    }

    public function getItems($params = array()) {

		$dbh = Registry::get('dbh');

		$query  = $this->_buildItemsQuery(); 
        $query .= $this->_buildItemsWhere();
        $query .= $this->_buildItemsOrder();
        $query .= $this->_buildItemsLimit();

        $sth = $dbh->prepare($query);

        $sth->execute($params);

        $data = $sth->fetchAll(PDO::FETCH_OBJ);

        return $data;

	}

    public function getItemsCount() {

        // $dbh = Registry::get('dbh');

        // $query="SELECT COUNT(*) as count
        //         FROM $this->table";

        // // if ($use_filters) {
        //     //$query .= $this->_buildItemsWhere();    
        // // }        

        // $sth = $dbh->prepare($query);

        // $sth->execute();

        // $data = $sth->fetch(PDO::FETCH_OBJ);
        
        // return $data->count;

        $dbh = Registry::get('dbh');
        
        // Generate query
        $query  = $this->_buildItemsQuery(); 
        $query .= $this->_buildItemsWhere();
        
        // replace SELECT line                     
        $from_pos = strpos(strtoupper($query), 'FROM');                    
        $query = "SELECT COUNT(*) as count " . substr($query, $from_pos);

        $sth = $dbh->prepare($query);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_OBJ);

        return $data->count;

    }

    // public function getPagination() {

    //     $this->pagination = new Pagination;

    //     $this->pagination->itemsCount = $this->getItemsCount();
        
    //     return $this->pagination;

    // }

	public function itemExist($id) {

		$dbh = Registry::get('dbh');

        $sth = $dbh->prepare("SELECT COUNT(*) as count
                              FROM `$this->table`
                              WHERE id = :id");
        
        $sth->execute(array('id' => $id));

        $data = $sth->fetch(PDO::FETCH_OBJ);
        
        return $data->count;

	}

	public function SaveNewItem($data, $returnid = true) {

		$result = 0;

        $dbh = Registry::get('dbh');

        $query = "INSERT INTO `$this->table` ("; 

        $i = 0;

        foreach ($data as $key => $value) {

        	$i++;
        	
        	$query .= $key;

        	if (count($data) > $i) $query .= ", ";

        }

        $query .= ") VALUES (";

        $i = 0;

        foreach ($data as $key => $value) {

        	$i++;
        	
        	$query .= ':' . $key;

        	if (count($data) > $i) $query .= ", ";

        }

        $query .= ")";

        $sth = $dbh->prepare($query);

        $result = $sth->execute($data);

        if ($returnid)
            $result = $dbh->lastInsertId();            

        return $result; 

	}

	public function SaveItem($id, $data) {

		$dbh = Registry::get('dbh');

        $query = "UPDATE `$this->table` SET ";

        $i = 0;

        foreach ($data as $key => $value) {

        	$i++;
        	
        	$query .= $key . "=:" . $key;
        	
        	if (count($data) > $i) $query .= ", ";

        }

        $query .= " WHERE id = " . $id;

        $sth = $dbh->prepare($query);

        return $sth->execute($data);

	}

	public function deleteItem($id) {
        
        $dbh = Registry::get('dbh');

        $sth = $dbh->prepare("DELETE FROM `$this->table` WHERE `id` =  :id");
        
        $result = $sth->execute(array('id' => $id));
        
        return $result;
    }

    public function finishAlias($alias, $id = 0) {

        $dbh = Registry::get('dbh');

        $sth = $dbh->prepare("SELECT COUNT(*) as count
                              FROM `$this->table`
                              WHERE alias = :alias AND id <> :id");

        $sth->execute(array('alias'=> $alias,'id'=> $id));
        
        $data = $sth->fetch(PDO::FETCH_OBJ);

        if ($data->count > 0) {
            $alias = $alias . '-1';
            $alias = $this->finishAlias($alias, $id);
        }
        
        return $alias;

    }

    public function addFilter($column, $title = '', $values = array(), $value = '', $first_empty_value = true) {

        // get filter value from session
        $state_filters = Main::getState('filters', array());

        if (isset($state_filters[$column])) {
            $value = $state_filters[$column];
        }

        // replace value from post term
        if (isset($_POST['filters'][$column])) {
            $value = $_POST['filters'][$column];
        }  

        // save filters to sission
        $state_filters[$column] = $value;

        Main::setState('filters', $state_filters);

        // init filter operator
        if (empty($operator) && count($values)) {
            $operator = '=';
        } else {
            $operator = 'LIKE';
        }

        // create filter object
        $filter = new stdClass;
        $filter->column = $column;
        $filter->title = $title;
        $filter->operator = $operator;
        $filter->values = $values;    
        $filter->value = $value;    
        $filter->first_empty_value = $first_empty_value;

        // save filter to filters
        $this->filters[] = $filter;

    }

    public function setFilter($column, $operator, $value) {

        $filter = new stdClass;
        $filter->column = $column;
        $filter->title = '';
        $filter->operator = $operator;
        $filter->values = array();    
        $filter->value = $value;    
        $filter->first_empty_value = false;

        // save filter to filters
        $this->filters[] = $filter;

    }

    public function _setFilter($filter) {

        if (!$filter->hidden) {
            // get information from session state
            $state_filters = Main::getState('filters', array());

            // replace value from state
            if (isset($state_filters[$filter->name])) {
                $filter->value = $state_filters[$filter->name];
            }

            // replace value from post term
            if (isset($_POST['filters'][$filter->name])) {
                $filter->value = $_POST['filters'][$filter->name];
            }  

            // save filters to session
            $state_filters[$filter->name] = $filter->value;
            // update state
            Main::setState('filters', $state_filters);

            if (empty($filter->value)) {
                $filter->value = $filter->empty_value;
            }

            // correct type of value
            if (in_array($filter->type, array('int', 'string'))) {
                settype($filter->value, $filter->type);
            } else {
                settype($filter->value, 'string');
            }
        }

        // save filter to filters
        $this->filters[] = $filter;

        return $this->filters;

    }

    public function getFilters() {

        return $this->filters;

    }

    public function getTableColumns() {

        $dbh = Registry::get('dbh');

        $query="SHOW COLUMNS FROM `$this->table`";

        $sth = $dbh->query($query);

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function getCategoriesList($include_main_category = false, $component = '') {

        $dbh = Registry::get('dbh');
        $route = Registry::get('route');

        if (empty($component)) {
            $component = $route->component;
        }

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

    protected function sort_items_into_tree($items, $parent_id = 0, $prefix = '') {
        
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

    public function initUserOrdering() {        

        $ordering = Main::getState($this->table.'_ordering', $this->default_ordering);

        if (isset($_GET['ordering_column']) && !empty($_GET['ordering_column'])) {            

            // Some security
            $ordering_column = $_GET['ordering_column'];
            $ordering_column = str_replace(";","",$ordering_column);
            $ordering_column = str_replace(" ","",$ordering_column);
            
            $ordering_sort = 'ASC';

            if (isset($_GET['ordering_sort']) && $_GET['ordering_sort'] == 'DESC') {            
                $ordering_sort = 'DESC';
            }

            // Query to check this field
            $dbh = Registry::get('dbh');

            $query  = $this->_buildItemsQuery();
            $query .= " ORDER BY $ordering_column $ordering_sort LIMIT 1";

            $sth = $dbh->prepare($query);
            
            try {
                $sth->execute();
                $ordering['column'] = $ordering_column;
                $ordering['sort'] = $ordering_sort;
            } catch (Exception $e) {
                Main::setMessage('Данное поле не доступно для отбора');
            }
            
        }        

        if (!empty($ordering['column'])) {

            Main::setState($this->table.'_ordering', $ordering);
            Main::setState('current_ordering', $ordering);
            $this->ordering = $ordering;

        } 

    }

    public function initPagination() {

        $this->pagination = new Pagination;
       
        $this->pagination->itemsCount = $this->getItemsCount();    

        // reset limit start if page higher then items count
        if ($this->pagination->itemsCount <= $this->pagination->limitstart) {
            $this->pagination->limitstart = 0;                
        }

        return $this->pagination;

    }

}