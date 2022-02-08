<?php
Class Main {

    // URL Redurect function. You can set message.
    static public function redirect($url, $msg='', $msgType='message') {
        
        if (!empty($msg)) self::setMessage($msg, $msgType);        
        
        $url = self::SEFURL($url);

        if (headers_sent()) {
            echo "<script>document.location.href='$url';</script>\n";
        } else {
            header( 'HTTP/1.1 301 Moved Permanently' );
            header( 'Location: ' . $url );
        }

        exit();

    }

    // function generate SEF URL (/index.php?c=user&a=login&bla=blablabla --> /user/login/?bla=blablabla)
    static public function SEFURL($url) {

        // Если нашли в url'е index.php значит надо обрабатывать
        if (strpos($url, 'index.php')) {

            $component = '';
            $action = '';
            $queryparts = array();

            $urlparts = parse_url($url);                
            
            if (isset($urlparts['query'])) {
                
                parse_str($urlparts['query'], $queryparts);

                if (isset($queryparts['c']) and !empty($queryparts['c'])) {
                    
                    $component = $queryparts['c'] . '/';                        

                }

                if (isset($queryparts['a']) and !empty($queryparts['c']) and !empty($queryparts['a'])) {
                    
                    $action = $queryparts['a'] . '/';                       

                }

                unset($queryparts['c']);
                unset($queryparts['a']);

            }

            if (count($queryparts) > 0) $action .= '?';
            
            $url = '/' . $component . $action . http_build_query($queryparts);              

        }

        return $url;

    }

    // Function return system messages in array or html formats
    static public function getMessages($format = 'array') {

        if (!isset($_SESSION['messages']))$_SESSION['messages'] = array();

        if ($format == 'array') {

            $data = $_SESSION['messages'];

        } elseif ($format == 'html') {

            $data = '';

            if (count($_SESSION['messages'])) {
                $data .= '<div class="messages-inner">';
                foreach ($_SESSION['messages'] as $mess) {
                    $data .= '<div class="sysmsg sysmsg'.$mess['type'].'">'. $mess['msg'] .'</div>';
                }
                $data .= '</div>';
            }

        }

        // Reset messages
        $_SESSION['messages'] = array();

        return $data;

    }

    // register a system message 
    static public function setMessage($msg, $type = 'message') {

        if (!isset($_SESSION['messages'])) $_SESSION['messages'] = array();

        $_SESSION['messages'][] = array('msg' => $msg, 'type' => $type);       

        return true;

    }

    // Преобразует русские слова в транслит
    static public function rus2translit($string) {

        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
            
            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );

        return strtr($string, $converter);
    }

    // Преобразует строку в url, русские символы транслитит
    static public function str2url($str) {
        
        // переводим в транслит
        $str = self::rus2translit($str);
        // в нижний регистр
        $str = strtolower($str);
        // заменям все ненужное нам на "-"
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        // удаляем начальные и конечные '-'
        $str = trim($str, "-");
        
        return $str;

    }

    static public function filename2translit($filename) {

        $parts = explode(".", $filename);

        $parts_count = count($parts);

        $result = '';

        for ($i=0;$i<$parts_count;$i++) {

            if ($i < $parts_count - 1) {

                $result .= self::str2url($parts[$i]);
                $result .= '.';

            } else {
                $result .= $parts[$i];
            }

        }

        return $result;

    }

    // Старая функция склонения по числу, на входе слово и число
    static public function declension_by_number($word, $number = 1) {

        if ($number > 1 && $number < 5) {
            $word .= 'а';
        } elseif ($number > 5 || $number == 0) {
            $word .= 'ов';
        }

        return $word;

    }

    // Функция склонения по числу, на входе число и массив склонений
    static public function declOfNum($number, $after) {
        $cases = array (2, 0, 1, 1, 1, 2);
        echo $number.' '.$after[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
    }

    // Функция для генерации случайной строки
    static public function generateCode($length=6) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;  
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];  
        }
        return $code;
    }

    // Сохраняет любое значение в сессию для текущего контроллера
    static public function setState($key, $value) {

        $controller = Registry::get('route')->controller;

        if (empty($controller)) {
            
            return false;            
            
        } else {

            $_SESSION['state'][$controller][$key] = $value;

            return true;

        }


    }

    // Возвращает значение переменной сессии для текущего контроллера
    static public function getState($key, $result = '') {

        $controller = Registry::get('route')->controller;

        if (isset($_SESSION['state'][$controller])) {
            
            $state = $_SESSION['state'][$controller];            

            if (isset($state[$key])) $result = $state[$key];

        }

        return $result;

    }

    // Проверка и возврат значения из $_POST
    static public function post($key, $result = NULL) {

        if (isset($_POST[$key])) $result = $_POST[$key];

        return $result;

    }

    // Отправляет письмо от имени сайта
    static public function sendEmail($to, $subject, $message) {

        $config = Registry::get('config');

        $headers = 'From: '.$config->SiteName.' <'.$config->SiteMail.'>' . "\r\n" .
                   'Reply-To: '.$config->SiteMail . "\r\n";

        mail($to, $subject, $message, $headers);

    }

}