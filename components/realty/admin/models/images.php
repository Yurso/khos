<?php
Class RealtyImagesModel Extends ModelBase {

	public $table = '#__realty_images';

    protected function _buildItemsOrder() {

        return " ORDER BY ordering ASC, image_name ASC";

    }

    protected function _buildItemsWhere() {

        return " WHERE object_id = :object_id";

    }

    protected function _buildItemsLimit() {

        return '';

    }

    public function deleteItem($id) {

        $item = $this->getItem($id);
        
        if (parent::deleteItem($id)) {

            $path = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'realty' . DIRSEP;
            $thumbs_path = $path . 'thumbs' . DIRSEP;

            unlink($path.$item->image_name);
            unlink($thumbs_path.$item->image_name);

            return true;

        }

        return false;

    }

    public function SaveUploadedImages($object_id, $input_name = 'images') {    

        $dbh = Registry::get('dbh');
        $config = Registry::get('config');

        $result = array();

        $images = $_FILES[$input_name];

        // allowed file mime types
        $types = array('image/jpeg', 'image/gif', 'image/pjpeg');
        
        // generate path name
        $path = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'realty' . DIRSEP;
        $thumbs_path = $path . 'thumbs' . DIRSEP;
        $url_path = '/public/images/realty/';

        // create path if path not exist
        if (!is_dir($thumbs_path)) {
            
            if (!mkdir($thumbs_path, 0755, true)) {
                Main::setMessage('Не удалось создать категорию для загрузки изображений.');
                return;
            }

        }

        // get last oredering number from db
        $sth = $dbh->query("SELECT ordering 
                            FROM `#__realty_images` 
                            WHERE object_id = $object_id 
                            ORDER BY ordering DESC 
                            LIMIT 1");

        $data = $sth->fetch(PDO::FETCH_OBJ); 

        if (isset($data->ordering)) {
            $ordering = $data->ordering + 1;
        } else {
            $ordering = 0;
        }

        // get count of images
        $sth = $dbh->query("SELECT count(id) AS count
                            FROM `#__realty_images` 
                            WHERE object_id = $object_id");

        $data = $sth->fetch(PDO::FETCH_OBJ); 

        if (isset($data->count)) {
            $count = $data->count;
        } else {
            $count = 0;
        }

        // process file list
        foreach ($images["error"] as $key => $error) {

            if ($error != UPLOAD_ERR_OK) continue;

            if (!in_array($images['type'][$key], $types)) continue;

            if ($count >= $config->realty_max_images) {
                Main::setMessage('Превышено максимальное количество изображений ('.$config->realty_max_images.')');
                break;    
            }
            
            $tmp_name = $images["tmp_name"][$key];

            $extension = strtolower(substr(strrchr($images["name"][$key],'.'),1));

            $name = $object_id.'-'.Main::generateCode(10).'.'.$extension;

            // if file saved successful
            if (move_uploaded_file($tmp_name, $path.$name)) {

                $params = array();
                $params['object_id'] = $object_id;
                $params['image_name'] = $name;
                $params['ordering'] = $ordering++;

                if (!$this->SaveNewItem($params)) {
                    unlink($path.$name);
                    Main::setMessage('Не удалось создать запись в базе данных по файлу ' . $images["name"][$key] . '. Файл не будет сохранен.');    
                }

                $count++;                

                // resize big image
                list($w_i, $h_i, $type) = getimagesize($path.$name);
                if (($config->realty_images_max_width > 0 && $w_i > $config->realty_images_max_width) || ($config->realty_images_max_height > 0 && $h_i > $config->realty_images_max_height)) {
                    KhImages::resize($path.$name, $path.$name, $config->realty_images_max_width, $config->realty_images_max_height);    
                }

                // create small image
                KhImages::resize($path.$name, $thumbs_path.$name, $config->realty_images_thumbs_width, $config->realty_images_thumbs_height);

            }

        }

    }

    public function _SaveUploadedImages($object_id, $post_key = 'uploaded') {

        if (!isset($_POST[$post_key])) {
            return;
        }

        $dbh = Registry::get('dbh');
        $config = Registry::get('config');

        $tempFolder = SITE_PATH .'public' . DIRSEP . 'images' . DIRSEP . 'tmp' . DIRSEP; 
        $storeFolder = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'realty' . DIRSEP;        
        $thumbsFloder = $storeFolder . 'thumbs' . DIRSEP;

        // get last oredering number from db
        $sth = $dbh->query("SELECT ordering 
                            FROM `#__realty_images` 
                            WHERE object_id = $object_id 
                            ORDER BY ordering DESC 
                            LIMIT 1");

        $data = $sth->fetch(PDO::FETCH_OBJ); 

        if (isset($data->ordering)) {
            $ordering = $data->ordering + 1;
        } else {
            $ordering = 0;
        }

        // get count of images
        $sth = $dbh->query("SELECT count(id) AS count
                            FROM `#__realty_images` 
                            WHERE object_id = $object_id");

        $data = $sth->fetch(PDO::FETCH_OBJ); 

        if (isset($data->count)) {
            $count = $data->count;
        } else {
            $count = 0;
        }
        $max_files_message = false;
        // for all files in temp folder
        foreach ($_POST[$post_key] as $filename) {
            // checking max files count
            if ($count >= $config->realty_max_images) {
                if ($max_files_message == false) {
                    Main::setMessage('Превышено максимальное количество изображений ('.$config->realty_max_images.')');
                    $max_files_message = true;
                }
                unlink($tempFile);
                unlink($tempThumbFile);
                continue;    
            }
            // add object id to filename for batter view
            $newfilename = $object_id.'-'.$filename;
            // set folders values
            $tempFile = $tempFolder.$filename;
            $storeFile = $storeFolder.$newfilename;
            $tempThumbFile = $tempFolder.'thumb_'.$filename;
            $storeThumbFile = $thumbsFloder.$newfilename;
            // copy file from temp folder to store folder
            if (!copy($tempFile, $storeFile)) {
                Main::setMessage('Не удалось скопировать файл '.$filename);  
                unlink($tempFile);  
                unlink($tempThumbFile);
                continue;
            }     
            copy($tempThumbFile, $storeThumbFile);
            // prepare information of file
            $params = array();
            $params['object_id'] = $object_id;
            $params['image_name'] = $newfilename;
            $params['ordering'] = $ordering++;
            // save file information to db
            if (!$this->SaveNewItem($params)) {                
                Main::setMessage('Не удалось создать запись в базе данных по файлу '.$filename.'. Файл не будет сохранен.');    
                unlink($storeFile);
                unlink($tempFile);                
                unlink($tempThumbFile);
                continue;
            }
            // remove temp file 
            unlink($tempFile);
            unlink($tempThumbFile);

            $count++;

        }

    }

    public function getNoUsedImages() {

        $dbh = Registry::get('dbh');

        $query="SELECT
                    realty_images.id,
                    realty_images.object_id,
                    realty_images.image_name   
                FROM `#__realty_images` AS realty_images
                LEFT JOIN `#__realty` AS realty
                ON realty_images.object_id = realty.id
                WHERE realty.id IS NULL";             

        $sth = $dbh->prepare($query);

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

    public function getObjectInTrashImages() {

        $dbh = Registry::get('dbh');

        $query="SELECT
                    realty_images.id,
                    realty_images.object_id,
                    realty_images.image_name
                FROM `#__realty_images` AS realty_images
                LEFT JOIN `#__realty` AS realty
                ON realty_images.object_id = realty.id
                WHERE realty.deleted > 0";            

        $sth = $dbh->prepare($query);

        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_OBJ);

    }

}