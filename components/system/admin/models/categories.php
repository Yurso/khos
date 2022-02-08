<?php
Class SystemCategoriesModel Extends ModelBase {

    public $table = '#__categories';

    public $default_ordering = array('column' => 'ordering', 'sort' => 'ASC');

    protected function _buildItemsQuery() {

        $query="SELECT *
                FROM `#__categories`";

        return $query;

    }

    protected function _buildItemsLimit() {

        return '';

    }

    public function getItems($params = array()) {

        $items = parent::getItems($params);

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

    // protected function _buildItemsWhere() {

    //     $query = parent::_buildItemsWhere();

    //     if (empty($query)) {
    //         $query = " WHERE parent_id = :parent_id";
    //     } else {
    //         $query .= " AND parent_id = :parent_id";
    //     }

    //     return $query;

    // }

    // public function getItemsTree($parent_id = 0, $level = 1) {

    //     $dbh = Registry::get('dbh');

    //     $query  = $this->_buildItemsQuery(); 
        
    //     $query .= " WHERE parent_id = :parent_id AND controller = :controller AND title LIKE :title";       

    //     $sth = $dbh->prepare($query);

    //     $filters = Main::getState('filters', array());

    //     $params = array();
    //     $params['parent_id'] = $parent_id;
    //     $params['controller'] = $filters['controller'];
    //     $params['title'] = '%'.$filters['title'].'%';

    //     $sth->execute($params);

    //     $items = array();

    //     while ($item = $sth->fetch(PDO::FETCH_OBJ)) {

    //         $item->level = $level;

    //         $items[] = $item;

    //         $items = array_merge($items, $this->getItemsTree($item->id, $level+1));

    //     }

    //     return $items;

    // }

    // public function getItemChildrens($parent_id) {

    //     $dbh = Registry::get('dbh');

    //     $query  = $this->_buildItemsQuery();
    //     $query .= " WHERE parent_id = :parent_id";

    //     $sth = $dbh->prepare($query);

    //     $sth->execute(array('parent_id' => $parent_id));

    //     $items = $sth->fetchAll(PDO::FETCH_OBJ);

    //     foreach ($items as $item) {
    //         $item->childrens = $this->getItemChildrens($item->id);
    //     }

    //     return $items;

    // }

    public function getActiveComponents() {

        $dbh = Registry::get('dbh');

        $sth = $dbh->query("SELECT *
                            FROM `#__components`
                            WHERE type = 'component' AND state > 0
                            ORDER BY name");

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function finishAlias($alias, $id = 0) {

        $dbh = Registry::get('dbh');

        $sth = $dbh->prepare("SELECT COUNT(*) as count
                              FROM `#__categories`
                              WHERE component = 'blog' AND alias = :alias AND id <> :id");

        $sth->execute(array('alias'=> $alias,'id'=> $id));
        
        $data = $sth->fetch(PDO::FETCH_OBJ);

        if ($data->count > 0) {
            $alias = $alias . '-1';
            $alias = $this->finishAlias($alias, $id);
        }
        
        return $alias;

    }

}