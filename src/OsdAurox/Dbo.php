<?php

namespace OsdAurox;

use PDO;

class Dbo
{
    public static ?Dbo $dbo_instance;

    public ?PDO $pdo;
    public string $host;
    public string $user;
    public string $dbname;
    public string $pass;
    public string $charset;
    public string $dsn;

    private function __construct()
    {
        $this->pdo = null;
    }

    private function __clone()
    {
        // sigleton
    }


    private function init(
        string $host = 'localhost',
        string $dbname = 'default_db',
        string $user = 'root',
        string $pass = '',
        string $charset = 'utf8mb4'
    )
    {

        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->charset = $charset;
        $this->dbname = $dbname;

        $this->dsn = "mysql:host=$host;dbname=$dbname;charset=$this->charset";

        $this->pdoCon();
    }

    public static function getInstance(
        string $host = 'localhost',
        string $dbname = 'default_db',
        string $user = 'root',
        string $pass = '',
        string $charset = 'utf8mb4'
    )
    {
        if (self::$dbo_instance == null) {
            self::$dbo_instance = new self();

            self::$dbo_instance->init($host, $dbname, $user, $pass, $charset);
        }

        return self::$dbo_instance;
    }

    private function pdoCon()
    {
        $dsn = $this->dsn;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];


        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (\Exception $e) {
            echo('-- [STOPPED] Oops DB Connection failed -- ');
            Log::error('-- [STOPPED] Oops DB Connection failed -- ');
            Log::error($e);
            die();
        }
    }

    public static function getPdo()
    {
        $instance = self::getInstance();

        if ($instance->pdo == null) {
            $instance->pdoCon();
        }
        return $instance->pdo;
    }


}