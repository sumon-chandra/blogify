<?php
require_once "../database.php";

class User
{
    private $pdo;

    function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function getAllUsers()
    {
        $query = "SELECT * FROM users ORDER BY user_id DESC;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUser($email)
    {
        $query = "SELECT * FROM users WHERE email = :email;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }
}
