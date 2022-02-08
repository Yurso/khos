<?php
Class SystemUserController Extends ControllerBase {

	function index() {

        // Information about user
        $id = User::getUserData('id');

        // Получаем модель
        $model = $this->getModel('users');

        // Получаем сведения о пользователе
        $user = $model->getItem(array('id' => $id));

        $tmpl = new Template;

        $tmpl->setVar('user', $user);

        $tmpl->setTitle('Пользователь');

        $tmpl->display('user_edit');

	}    

    function save() {

        $err = array();

        $model = $this->getModel('users');

        $id = User::getUserData('id');

        // Check this post
        // if (empty($_POST['name'])) {
        //     $err[] = 'Имя не может быть пустым.';
        // }

        // if (empty($_POST['email'])) {
        //     $err[] = 'Email не может быть пустым.';
        // }

        if ($_POST['password'] <> $_POST['password2']) {
            $err[] = 'Пароли не совпадают.';                
        }

        if ($id == 0 and empty($_POST['password'])) {
            $err[] = 'Пароль не может быть пустым.';
        }

        // if ($id > 0 and !$model->itemExist($id)) {
        //     $err[] = 'Пользователя с таким id не существует';
        // }

        if (!count($err)) {

            $item = array();                
            // $item['name'] = $_POST['name'];
            // $item['email'] = $_POST['email'];            

            if (!empty($_POST['password'])) $item['password'] = md5(md5($_POST['password']));                   
          
            $item['position'] = $_POST['position'];
            $item['phone'] = $_POST['phone'];
            $item['website'] = $_POST['website'];
            $item['work_email'] = $_POST['work_email'];
            //$item['image'] = $_POST['image'];

            // Saving image file
            $image = $_FILES['image'];
            $image_types = array('image/jpeg', 'image/gif', 'image/pjpeg', 'image/png');     
            $path = SITE_PATH . 'public' . DIRSEP . 'images' . DIRSEP . 'realty_agents' . DIRSEP;
            $url_path = '/public/images/realty_agents/';

            if ($image['error'] == UPLOAD_ERR_OK && in_array($image['type'], $image_types)) {

                if (!is_dir($path)) {            
                    mkdir($path, 0755, true);
                }

                $filename = $id.'-'.Main::generateCode().'-'.Main::rus2translit($image['name']);

                if (move_uploaded_file($image['tmp_name'], $path.$filename)) {
                    KhImages::resize($path.$filename, $path.$filename, 600, 0);
                    $item['image'] = $url_path.$filename;
                }

            }

            if ($model->SaveItem($id, $item)) {
                Main::redirect('/admin/system/user', 'Запись успешно сохранена.');
            } else {
                Main::redirect('/admin/system/user', 'Ошибка. Не удалось сохранить запись.');
            }
            
        } else {

            foreach ($err as $error) {
                Main::setMessage($error);
            }

            Main::redirect('/admin/system/user', 'Ошибка. Не удалось сохранить запись.');            

        }

    }

}