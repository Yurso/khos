<?php
Class ShopProductsImagesModel Extends ModelBase {

	public $table = '#__shop_products_images';

    protected function _buildItemsOrder() {

        return " ORDER BY ordering ASC, image_name ASC";

    }

    protected function _buildItemsWhere() {

        return " WHERE product_id = :product_id";

    }

    protected function _buildItemsLimit() {

        return '';

    }

    public function deleteItem($id) {

        $item = $this->getItem($id);
        
        if (parent::deleteItem($id)) {

            $path = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'shop' . DIRSEP . 'products' . DIRSEP;
            $thumbs_path = $path . 'thumbs' . DIRSEP;

            unlink($path.$item->image_name);
            unlink($thumbs_path.$item->image_name);

            return true;

        }

        return false;

    }

    public function SaveUploadedImages($product_id, $input_name = 'images') {

        $result = array();

        $images = $_FILES[$input_name];

        // allowed file mime types
        $types = array('image/jpeg', 'image/gif', 'image/pjpeg');
        
        // generate path name
        $path = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'shop' . DIRSEP . 'products' . DIRSEP;
        $thumbs_path = $path . 'thumbs' . DIRSEP;
        $url_path = '/public/images/shop/products/';

        // create path if path not exist
        if (!is_dir($thumbs_path)) {
            
            if (!mkdir($thumbs_path, 0755, true)) {
                Main::setMessage('Не удалось создать категорию для загрузки изображений.');
                return;
            }

        }

        $dbh = Registry::get('dbh');

        // get last oredering number from db
        $sth = $dbh->query("SELECT ordering 
                            FROM `#__shop_products_images` 
                            WHERE product_id = $product_id 
                            ORDER BY ordering DESC 
                            LIMIT 1");

        $data = $sth->fetch(PDO::FETCH_OBJ);        

        if (isset($data->ordering)) {
            $ordering = $data->ordering + 1;
        } else {
            $ordering = 0;
        }

        // process file list
        foreach ($images["error"] as $key => $error) {

            if ($error != UPLOAD_ERR_OK) continue;

            if (!in_array($images['type'][$key], $types)) continue;
            
            $tmp_name = $images["tmp_name"][$key];

            $extension = strtolower(substr(strrchr($images["name"][$key],'.'),1));

            $name = $product_id.'-'.Main::generateCode(10).'.'.$extension;

            // if file saved successful
            if (move_uploaded_file($tmp_name, $path.$name)) {

                $params = array();
                $params['product_id'] = $product_id;
                $params['image_name'] = $name;
                $params['ordering'] = $ordering++;

                if (!$this->SaveNewItem($params)) {
                    unlink($path.$name);
                    Main::setMessage('Не удалось создать запись в базе данных по файлу ' . $images["name"][$key] . '. Файл не будет сохранен.');    
                }

                // create small image
                KhImages::resize($path.$name, $thumbs_path.$name, 200, 0);

            }

        }

        return;

    }

}