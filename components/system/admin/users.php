<?php
Class SystemUsersController Extends ControllerBase {

	function index() {

		$model = $this->getModel('users');
        
        $model->initUserOrdering();

        // FILTER BY NAME
        $filter = new Filter;
        $filter->name = 'name';
        $filter->column = 'u.name';
        $filter->title = 'Имя';
        $filter->operator = 'LIKE';
        
        $model->_setFilter($filter);

        // FILTER BY NAME
        $filter = new Filter;
        $filter->name = 'email';
        $filter->column = 'u.email';
        $filter->title = 'Email';
        $filter->operator = 'LIKE';
        
        $model->_setFilter($filter);

        // FILTERS CATEGORY
        $filter = new Filter;
        $filter->name = 'access';
        $filter->column = 'u.access';
        $filter->title = 'Доступ';
        $filter->setValues($model->getUsersAccessList(), 'id', 'name'); 

        $model->_setFilter($filter);

        $pagination = $model->initPagination();

        $tmpl = new Template;  

		$tmpl->setVar('users', $model->getItems());
        $tmpl->setVar('pagination', $pagination);
        $tmpl->setVar('filters', $model->filters);

        $tmpl->setTitle('Пользователи');

		$tmpl->display('users');

	}

    function create() {

        // Получаем модель компонента
        $model = $this->getModel('users');
        
        $users_access = $model->getUsersAccessTree();  

        $user = new stdClass;
        $user->id = 0;       
        $user->name = '';
        $user->login = '';
        $user->email = '';  
        $user->activated = 1;
        $user->access = 1;
        $user->position = '';  
        $user->phone = '';  
        $user->website = '';  
        $user->image = '';
        $user->work_email = '';

        $tmpl = new Template; 
        
        $tmpl->setVar('user', $user);
        $tmpl->setVar('users_access', $users_access);
        
        $tmpl->display('edit');

    }

    function edit() {

        // Получаем аргументы ссылки
        $args = Registry::get('route')->args;
        
        if (isset($args[0])) {
            // Получаем id материала
            $id = intval($args[0]);
            // Получаем модель
            $model = $this->getModel('users');

            if ($model->itemExist($id)) {
                // Получаем сведения о пользователе
                $user = $model->getItem(array('id' => $id));

                $users_access = $model->getUsersAccessTree();   

                $tmpl = new Template;                          

                $tmpl->setVar('user', $user);
                $tmpl->setVar('users_access', $users_access);
                                    
                $tmpl->display('users_edit');
            } else {
                Main::redirect('/admin/system/users', 'Пользователь с таким id не найден');
            }

        } else {
            Main::redirect('/admin/system/users', 'Не выбран пользователь для редактирования');
        }

    }

    function save() {

        $err = array();

        $model = $this->getModel('users');

        $id = (int) $_POST['id'];

        // Check this post
        if (empty($_POST['name'])) {
            $err[] = 'Имя не может быть пустым.';
        }

        if (empty($_POST['login'])) {
            $err[] = 'Login не может быть пустым.';
        }

        if (empty($_POST['email'])) {
            $err[] = 'Email не может быть пустым.';
        }

        if ($_POST['password'] <> $_POST['password2']) {
            $err[] = 'Пароли не совпадают.';                
        }

        if ($id == 0 and empty($_POST['password'])) {
            $err[] = 'Пароль не может быть пустым.';
        }

        if ($id > 0 and !$model->itemExist($id)) {
            $err[] = 'Пользователя с таким id не существует';
        }

        if (!count($err)) {

            $item = array();                
            $item['name'] = $_POST['name'];
            $item['login'] = $_POST['login'];
            $item['email'] = $_POST['email'];            

            if (!empty($_POST['password'])) $item['password'] = md5(md5($_POST['password']));       
            
            $item['activated'] = $_POST['activated'];
            $item['access'] = $_POST['access'];
            
            $item['position'] = $_POST['position'];
            $item['phone'] = $_POST['phone'];
            $item['website'] = $_POST['website'];
            $item['work_email'] = $_POST['work_email'];

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

            if ($id == 0) {                

                $id = $model->SaveNewItem($item);

                if ($id > 0) {
                    Main::redirect('/admin/system/users/edit/' . $id,'Запись успешно сохранена.');
                } else {
                    Main::redirect('/admin/system/users/', 'Ошибка. Не удалось сохранить запись.');
                }

            } elseif ($id > 0) {

                if ($model->SaveItem($id, $item)) {
                    Main::redirect('/admin/system/users/edit/' . $id, 'Запись успешно сохранена.');
                } else {
                    Main::redirect('/admin/system/users/' . $id, 'Ошибка. Не удалось сохранить запись.');
                }

            }
        } else {

            foreach ($err as $error) {
                Main::setMessage($error);
            }

            if ($id == 0) { 
                Main::redirect('/admin/system/users/create', 'Ошибка. Не удалось сохранить запись.');
            } else {
                Main::redirect('/admin/system/users/edit/' . $id, 'Ошибка. Не удалось сохранить запись.');
            }

        }

    }

    function delete() {

        $i = 0;

        $args = Registry::get('route')->args;

        $model = $this->getModel('users');

        if (isset($args[0])) {

            $id = (int) $args[0];           

            if ($model->deleteItem($id)) {
                Main::Redirect('/admin/system/users', 'Элемент успешно удален');
            }

        } elseif (isset($_POST['checked'])) {

            foreach ($_POST['checked'] as $key => $value) {
                
                $id = (int) $value;

                if ($model->deleteItem($id)) {
                    $i++;
                } else {
                    Main::setMessage('Не удалось удалить элемент id = ' . $id);
                }

            }

            Main::redirect('/admin/system/users', 'Успешно удалено ' . $i . ' элементов');

        } else {
            Main::Redirect('/admin/system/users', 'Не указан id элемента');
        }
        
    }

}