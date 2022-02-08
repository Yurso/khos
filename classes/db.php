<?php
Class db
{
    public $db = false;

    public function init()
    {
        $conf = new Configuration;
        
        try {
            switch ($conf->dbtype) {
                
                case "sqlite":                    
                    $dsn = 'sqlite:'.SITE_PATH . DIRSEP . 'db' . DIRSEP . $conf->dbname . '.db';
                    $this->db = new MyPDO($dsn, Null, Null, array(), $conf->dbprefix, $conf->dbreplace);
                    break;
                
                case "mysql":
                    $dsn = 'mysql:host='.$conf->dbhost.';dbname='.$conf->dbname;
                    $options = array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    );
                    $this->db = new MyPDO($dsn, $conf->dbuser, $conf->dbpassword, $options, $conf->dbprefix, $conf->dbreplace);
                    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    break;                    
            }

        } catch (PDOException $e) {            
            die('SQL Connection Error');
        }

        return $this->db;

    }

}