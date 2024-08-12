<?php
$dbPath = dirname(__DIR__) . '/database.php';
if (file_exists($dbPath)) {
    require_once $dbPath;
}

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

    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = :email;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    public function getUserById($user_id)
    {
        $query = "SELECT
                    u.*,
                    g.gender,
                    r.role
                FROM
                    users AS u
                INNER JOIN roles AS r
                ON
                    r.role_id = u.role_id
                    INNER JOIN genders AS g 
                    ON g.gender_id = u.gender_id
                WHERE
                    u.user_id = :user_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($user_id, $first_name, $last_name, $dob, $gender_id, $avatar)
    {
        $query = "UPDATE users SET first_name = :first_name, last_name = :last_name, gender_id = :gender_id, dob= :dob, avatar = :avatar WHERE user_id = :user_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':gender_id', $gender_id);
        $stmt->bindParam(':avatar', $avatar);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function userRole($user_id)
    {
        $query = "SELECT 
                    r.role
                    FROM users AS u 
                    INNER JOIN roles AS r 
                    ON u.role_id = r.role_id
                    WHERE u.user_id = :user_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['role'];
    }
}
