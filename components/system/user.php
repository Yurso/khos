<?php
Class SystemUserController Extends ControllerBase {

	function index() {
		
		$user = new User;

		if ($user->checkAuth()) {
            Main::Redirect('/system/user/info');
        } else {
            Main::Redirect('/system/user/login');
        }

	}

	function login() {        

        $mess = array();
        $redirect = '/';        
        $logger = Logger::getLogger('user_auth'); 

        if (isset($_POST['submit'])) {

            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            
            if ($email !== false) {

                $model = $this->getModel('users');
                $sessions = $this->getModel('sessions');                              

                $data = $model->getUserByEmail($email);            

                # Сравниваем пароли
                if(isset($data->id) and ($data->password === md5(md5($_POST['password']))))
                {                                        
                    # если пользователь активирован
                    if($data->activated) {

                        $store_password = false;

                        if (isset($_POST["store_password"]) && $_POST["store_password"] == 'on') {
                            $store_password = true;
                        }

                        # обновляем запись бд
                        $session = $sessions->createSession($data->id, $store_password);
                
                        # Сохраняем данные сессии
                        $_SESSION['id'] = $session->id;
                        $_SESSION['hash'] = $session->hash;

                        # добавляем куки, если пользователь указал "Запомнить меня"
                        if ($store_password) {
                            $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
                            setcookie("session_id", $session->id, strtotime('+1 year'), '/', $domain, false);                   
                            setcookie("session_hash", $session->hash, strtotime('+1 year'), '/', $domain, false);                   
                        }

                        if (isset($_POST['redirect']))$redirect = $_POST['redirect'];

                        Pushbullet::sendPush(
                            'Khos: успешная авторизация на сайте', 
                            "$data->name успешно авторизовался на сайте \n" . 
                            "Дата: ".date("d.m.Y H:i:s")."\n".
                            "IP: ".$_SERVER['REMOTE_ADDR']
                        );

                        Main::Redirect($redirect, 'Вы успешно авторизованны');
                       
                    } else $mess[] = "Ваш аккаунт еще не активирован";                    
                } else {
                    $logger->log(
                        'Неудачная попытка авторизации (неправильный логин или пароль)' . "\n" .
                        'IP: ' . $_SERVER['REMOTE_ADDR'] . "\n" .
                        'User agent: ' . $_SERVER['HTTP_USER_AGENT'] . "\n" .
                        'Email: ' . $_POST['email']
                    );
                    $mess[] = "Неправильный логин или пароль";                  
                }
            } else $mess[] = "Не корректно введен email";
            
        }

        if(isset($_GET['redirect']))$redirect = $_GET['redirect'];

        $template = new Template;

        $template->setVar('messages', $mess);
        $template->setVar('redirect', $redirect);

		$template->display('login', 'clean');	

	}

    function logout() {

        if (isset($_SESSION['id'])) {
            // get sessions model
            $model = $this->getModel('sessions');
            // get session id
            $session_id = intval($_SESSION['id']);
            // delete session from db
            $model->deleteItem($session_id);
        }

        // delete cookies and destroy session
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        setcookie("session_id", "", time() - 3600);                   
        setcookie("session_hash", "", time() - 3600);  
        setcookie("session_id", "", time() - 3600, '/', $domain, false);                   
        setcookie("session_hash", "", time() - 3600, '/', $domain, false);  
        session_destroy();

        // redirecting
        if(isset($_GET['redirect']))
            $redirect = $_GET['redirect'];
        else
            $redirect = '/';

        Main::Redirect($redirect);

    }

    function info() {

        $user = new User;

        $user_data = $user->getUserData();

        if (isset($user_data->id)) {            

            $tmpl = new template;

            $tmpl->setVar('user_data', $user_data);

            $tmpl->display('info');

        } else {

            Main::Redirect('/system/user/login');

        }

    }

    function recovery() {

        $messages = array();
        $model = $this->getModel('users');
        $config = Registry::get('config');
        $stage = 1;
        $code = '';

        if (isset($_POST['stage']) && $_POST['stage'] == 1) {

            # Вытаскиваем из БД запись, у которой логин равен введенному
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

            if ($email !== false) {

                $user = $model->getUserByEmail($email);

                if (isset($user->id) && $user->id > 0) {

                    $stage = 2;

                    $act_code = $model->updateUserActCode($user->id);

                    $to       = $email;
                    $subject  = $config->SiteName.' - восстановление пароля.';
                    $message  = 'На сайте "'.$config->SiteName.'" поступил запрос на восстановление пароля' . "\r\n\r\n";              
                    $message .= 'Если вы не отправляли данный запрос, то просто закройте или удалите данное письмо.' . "\r\n\r\n";                               
                    $message .= 'Если вы действительно хотите восстановить свой пароль, то просто перейдите по ссылке:' . "\r\n";  
                    $message .= 'http://'.$config->BaseURL.'/user/recovery?code='.$act_code . "\r\n";
                    
                    mail($to, $subject, $message);

                    $messages[] = 'На адрес '.$email.' выслан код для подтверждения смены пароля.';

                } else {

                    $messages[] = 'Данный email не зарегистрирован на сайте.';

                }

            } else {
                
                $messages[] = 'Не верно указан email адрес.';

            }

        }

        if (isset($_GET['code'])) {

            $code = $_GET['code'];

            $user = $model->getUserByActCode($code);

            if (isset($user->id) && $user->id > 0) {

                $stage = 3;

            }

        }

        if (isset($_POST['stage']) && $_POST['stage'] == 3) {            

            $user = $model->getUserByActCode($_POST['code']);

            if (isset($user->id) && $user->id > 0) {

                if ($_POST['password'] == $_POST['check'] && !empty($_POST['password'])) {           

                    $stage = 4;         

                    $params = array();
                    $params['password'] = md5(md5($_POST['password']));
                    $params['act_code'] = md5(Main::generateCode(10));

                    if ($model->SaveItem($user->id, $params)) {
                        $messages[] = 'Ваш пароль успешно изменен';
                    } else {
                        $messages[] = 'Произошла ошибка при записи пароля. Пожалуйста, обратитесь к администратору';
                    }

                } else {

                    $stage = 3;
                    $code = $_POST['code'];
                    $messages[] = 'Пароли не совпадают';

                }
            }

        }

        $tmpl = new template;  

        $tmpl->setVar('messages', $messages);      
        $tmpl->setVar('stage', $stage);      
        $tmpl->setVar('code', $code);    

        $tmpl->display('recovery', 'clean');

    }

}