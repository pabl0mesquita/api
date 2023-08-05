<?php
namespace Source\Core;

/**
 * Connect [ Sigleton Pattern ]
 */
class Connect
{
    private const CONF_DB_OPTIONS = [
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        \PDO::ATTR_CASE => \PDO::CASE_NATURAL
    ];

    /** @var \PDO */
    private static $instance;

    /**
     * @return \PDO
     */
    public static function getInstance(): ?\PDO
    {
        if(empty(self::$instance)){
            try{
                self::$instance = new \PDO(
                    "mysql:host=".CONF_DB_HOST.";dbname=".CONF_DB_NAME,
                    CONF_DB_USER,
                    CONF_DB_PASSWD,
                    self::CONF_DB_OPTIONS
                );

            }catch(\PDOException $exception){
               var_dump($exception);
               exit;
            }
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
        
    }
}