<?php
Class Req {

    static public function string($name, $return = '', $sources = 'post, get') {

        $sources = explode(',', $sources);

        foreach ($sources as $source) {
            if (trim($source) == 'post' && isset($_POST[$name]) && !empty($_POST[$name])) {
                $return = $_POST[$name];
            }
            if (trim($source) == 'get' && isset($_GET[$name]) && !empty($_GET[$name])) {
                $return = $_GET[$name];
            }
        }

        return $return;

    }

    static public function int($name, $return = 0, $sources = 'post, get') {

        $sources = explode(',', $sources);

        foreach ($sources as $source) {
            if (trim($source) == 'post' && isset($_POST[$name])) {
                $return = intval($_POST[$name]);
            }
            if (trim($source) == 'get' && isset($_GET[$name])) {
                $return = intval($_GET[$name]);
            }
        }

        return $return;

    }

}