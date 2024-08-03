<?php

class Database
{
    public $dsn = 'mysql:host=localhost;dbname=blogify';
    public $db_username = 'root';
    public $db_password = '';
    public $pdo;

    public function __construct()
    {
        $this->connect();
    }

    function connect()
    {
        $this->pdo = null;
        try {
            $this->pdo = new PDO($this->dsn, $this->db_username, $this->db_password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $error) {
            echo "Database connection error :-" . $error->getMessage();
            die();
        }
    }

    function getConnection()
    {
        return $this->pdo;
    }
    function closeConnection()
    {
        $this->pdo = null;
    }
}
