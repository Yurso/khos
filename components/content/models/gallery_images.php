<?php
Class GalleryImagesModel Extends ModelBase {

	public $table = '#__gallery_images';

	public $default_ordering = array('column' => 'id', 'sort' => 'ASC');

	protected function _buildItemsQuery() {

		$query = "SELECT gi.*, c.title as category_title
                  FROM `#__gallery_images` AS gi
                  LEFT JOIN `#__categories` AS c
                  ON gi.category_id = c.id";

        return $query;

    }

	// public function getCategoriesList() {

 //        $dbh = Registry::get('dbh');

 //        $sth = $dbh->query("SELECT id, title, alias, parent_id, state, controller
 //                            FROM `#__categories`
 //                            WHERE state > 0 AND controller = 'gallery'");

 //        $items = $sth->fetchAll(PDO::FETCH_OBJ);

 //        return $this->sort_items_into_tree($items);
        
 //    }

 //    protected function sort_items_into_tree($items, $parent_id = 0, $prefix = '') {
        
 //        $output = array();

 //        foreach ($items as $key => $item) {

 //            if ($item->parent_id == $parent_id) {
                
 //                $item->title = $prefix . $item->title;
                
 //                $output[] = $item;
 //                unset($items[$key]);

 //                $output = array_merge($output, $this->sort_items_into_tree($items, $item->id, $prefix . '- '));
 //            }
            
 //        }

 //        return $output;

 //    }

    public function deleteItem($id) {

        $item = $this->getItem($id);
        
        if (parent::deleteItem($id)) {

            $path = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'gallery' . DIRSEP;

            unlink($path.$item->filename);
            unlink($path.'thumbs'.DIRSEP.$item->filename);

            return true;

        }

        return false;

    }

    public function image_resize($file_input, $file_output, $w_o, $h_o, $percent = false) {

        list($w_i, $h_i, $type) = getimagesize($file_input);        
        if (!$w_i || !$h_i) {
            trigger_error('Can not get the length and width of the image.', E_USER_NOTICE);            
            return;
        }
        $types = array('','gif','jpeg','png');
        $ext = $types[$type];
        if ($ext) {
            $func = 'imagecreatefrom'.$ext;
            $img = $func($file_input);
        } else {
            trigger_error('Image_resize: incorrect file type', E_USER_NOTICE);
            return;
        }
        if ($percent) {
            $w_o *= $w_i / 100;
            $h_o *= $h_i / 100;
        }
        if (!$h_o) $h_o = $w_o/($w_i/$h_i);
        if (!$w_o) $w_o = $h_o/($h_i/$w_i);

        $img_o = imagecreatetruecolor($w_o, $h_o);
        imagecopyresampled($img_o, $img, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);
        if ($type == 2) {
            return imagejpeg($img_o,$file_output,100);
        } else {
            $func = 'image'.$ext;
            return $func($img_o,$file_output);
        }

    }

    public function image_crop($file_input, $file_output, $crop = 'square', $percent = false) {
        list($w_i, $h_i, $type) = getimagesize($file_input);
        if (!$w_i || !$h_i) {
            trigger_error('Can not get the length and width of the image.', E_USER_NOTICE);
            return;
        }
        $types = array('','gif','jpeg','png');
        $ext = $types[$type];
        if ($ext) {
            $func = 'imagecreatefrom'.$ext;
            $img = $func($file_input);
        } else {
            trigger_error('Image_resize: incorrect file type', E_USER_NOTICE);  
            return;
        }
        if ($crop == 'square') {
            $min = $w_i;
            if ($w_i > $h_i) $min = $h_i;
            $w_o = $h_o = $min;
        } else {
            list($x_o, $y_o, $w_o, $h_o) = $crop;
            if ($percent) {
                $w_o *= $w_i / 100;
                $h_o *= $h_i / 100;
                $x_o *= $w_i / 100;
                $y_o *= $h_i / 100;
            }
            if ($w_o < 0) $w_o += $w_i;
                $w_o -= $x_o;
            if ($h_o < 0) $h_o += $h_i;
            $h_o -= $y_o;
        }
        $img_o = imagecreatetruecolor($w_o, $h_o);
        imagecopy($img_o, $img, 0, 0, $x_o, $y_o, $w_o, $h_o);
        if ($type == 2) {
            return imagejpeg($img_o,$file_output,100);
        } else {
            $func = 'image'.$ext;
            return $func($img_o,$file_output);
        }
    
    }

}