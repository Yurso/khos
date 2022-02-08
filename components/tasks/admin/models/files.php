<?php
Class TasksFilesModel Extends ModelBase {

    public $table = "#__tasks_files";

    public function deleteItem($id) {

        $item = $this->getItem($id);

        $filespath = Params::getParamValue('tasks_files_path', 'tmp/');
        $fullpath = SITE_PATH . $filespath;

        unlink($fullpath.$item->filename);

        return parent::deleteItem($id);

    }

    public function saveTaskFilesByInputName($task_id, $input_name) {
    	
        $files = $_FILES[$input_name];

        // file paths
        $filespath = Params::getParamValue('tasks_files_path', 'tmp/');
    	$fullpath = SITE_PATH . $filespath;

        // allowed file mime types
        $types = array(
        	'image/jpeg', 
        	'image/gif', 
        	'image/pjpeg', 
        	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        	'application/vnd.ms-excel',
        	'application/msword',
        	'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        	'application/vnd.ms-powerpoint',
        	'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        	'application/zip',
        	'application/gzip'
        );

        $ordering = $this->getTaskOrderingNumber($task_id);

        // process file list
        foreach ($files["error"] as $key => $error) {

            if ($error != UPLOAD_ERR_OK) continue;

            if (!in_array($files['type'][$key], $types)) {
            	Main::setMessage('Не удалось сохранить файл ' . $files["name"][$key] . '. Запрещено загружать файлы в формате '.$files['type'][$key]);
            	continue;
            }
            
            $tmp_name = $files["tmp_name"][$key];

            $filename = $task_id.'-'.$key.'-'.Main::filename2translit($files["name"][$key]);                        

            // if file saved successful
            if (move_uploaded_file($tmp_name, $fullpath.$filename)) {

                $params = array(
                	'task_id' => $task_id,
                	'filename' => $filename,
                    'ordering' => $ordering
                );

                $ordering++;

                if (!$this->SaveNewItem($params)) {
                    unlink($fullpath.$filename);
                    Main::setMessage('Не удалось создать запись в базе данных по файлу ' . $files["name"][$key] . '. Файл не будет сохранен.');    
                }            

            }

        }

    }

    public function getTaskOrderingNumber($task_id) {

    	$dbh = Registry::get('dbh');

    	// get last oredering number from db
        $sth = $dbh->prepare("
        	SELECT ordering 
            FROM `#__tasks_files` 
            WHERE task_id = :task_id 
            ORDER BY ordering DESC 
            LIMIT 1
        ");

        $sth->execute(array('task_id' => $task_id));

        $data = $sth->fetch(PDO::FETCH_OBJ); 

        if (isset($data->ordering)) {
            $ordering = $data->ordering + 1;
        } else {
            $ordering = 0;
        }

        return $ordering;

    }

}