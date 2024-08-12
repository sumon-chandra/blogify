<?php
require_once "../database.php";

class SignupModel
{
    private $pdo;

    function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function signup($first_name, $last_name, $email, $hashed_pwd, $dob, $gender_id, $avatar)
    {
        $query = "INSERT INTO users (first_name, last_name, email, hashed_pwd, dob, avatar, gender_id) VALUES (:first_name, :last_name, :email, :hashed_pwd, :dob, :avatar, :gender_id);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':hashed_pwd', $hashed_pwd);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':avatar', $avatar);
        $stmt->bindParam(':gender_id', $gender_id);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
}
