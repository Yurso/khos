<?php
Class ForumFilesModel Extends ModelBase {

	public $table = '#__forum_files';

    protected function _buildItemsOrder() {

        return " ORDER BY type ASC, name ASC";

    }

    protected function _buildItemsWhere() {

        return " WHERE message_id = :message_id";

    }

    protected function _buildItemsLimit() {

        return '';

    }

    public function deleteItem($id) {

        $item = $this->getItem($id);
        
        if (parent::deleteItem($id)) {

            $path = SITE_PATH . 'public' . DIRSEP . 'files' . DIRSEP . 'forum' . DIRSEP;            

            unlink($path.$item->name);            

            return true;

        }

        return false;

    }

    public function SaveUploadedFiles($message_id, $input_name = 'files') {    

        $dbh    = Registry::get('dbh');
        $config = Registry::get('config');
        $result = array();
        // uploaded count
        $count  = 0;
        // maximum file size bytes
        $max_file_size = 5*1024*1024;
        // allowed file mime types
        $doc_types = array(
            'text/plain', 'text/html', 
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );        
        $img_types = array('image/jpeg', 'image/gif', 'image/pjpeg', 'image/png');        
        // generate path name
        $filepath = SITE_PATH . 'public' . DIRSEP . 'files' . DIRSEP . 'forum' . DIRSEP;        
        $url_path = '/public/files/forum/';

        $files = $_FILES[$input_name];

        // create path if path not exist
        if (!is_dir($filepath)) {
            mkdir($path, 0755, true);
        }

        // process file list
        foreach ($files["error"] as $key => $error) {

            if ($error != UPLOAD_ERR_OK) continue;

            if ($files['size'][$key] > $max_file_size) {                
                Main::setMessage('Файл ' . $files["name"][$key] . ' превышает максимально допустимый размер. Файл не сохранен.');    
                continue;
            }

            if (!in_array($files['type'][$key], $doc_types) && !in_array($files['type'][$key], $img_types)) {
                Main::setMessage('Недопустимое расширение файла ' . $files["name"][$key] . '. Файл не сохранен.');    
                continue;
            }
            
            $tmp_name = $files["tmp_name"][$key];

            $extension = strtolower(substr(strrchr($files["name"][$key],'.'),1));

            $filename = $message_id.'-'.str_replace(" ", "_", Main::rus2translit($files["name"][$key]));

            // if file saved successful
            if (move_uploaded_file($tmp_name, $filepath.$filename)) {

                $params = array();
                
                // detecting file type
                if (in_array($files['type'][$key], $img_types)) {
                    $params['type'] = 'image';
                } else {
                    $params['type'] = 'document';
                }
                
                $params['path'] = $url_path;
                $params['name'] = $filename;
                $params['message_id'] = $message_id;

                // saving file
                if (!$this->SaveNewItem($params)) {
                    unlink($filepath.$filename);
                    Main::setMessage('Не удалось создать запись в базе данных по файлу ' . $files["name"][$key] . '. Файл не сохранен.');    
                }

                $count++;                

                // resize big image
                // list($w_i, $h_i, $type) = getimagesize($path.$name);
                // if (($config->realty_images_max_width > 0 && $w_i > $config->realty_images_max_width) || ($config->realty_images_max_height > 0 && $h_i > $config->realty_images_max_height)) {
                //     KhImages::resize($path.$name, $path.$name, $config->realty_images_max_width, $config->realty_images_max_height);    
                // }

                // // create small image
                // KhImages::resize($path.$name, $thumbs_path.$name, $config->realty_images_thumbs_width, $config->realty_images_thumbs_height);

            }

        }

        return $count;
 
    }

}