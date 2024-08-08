<?php
require_once "../user/user.inc.php";

class LoginContr
{

    public function isInputEmpty($email, $pwd)
    {
        if (empty($email) || empty($pwd)) {
            return true;
        }
    }

    public function isPasswordMatch($email, $pwd)
    {
        $user = new User();
        $user_data = $user->getUserByEmail($email);
        // echo "Please enter Email Password ";
        $result = password_verify($pwd, $user_data['hashed_pwd']);
        // echo "Password match: " . $result;
        return $result;
    }

    public function isUserExist($email)
    {
        $user = new User();
        return $user->getUserByEmail($email);
    }

    public function login($email)
    {
        $user = new User();
        $user_data = $user->getUserByEmail($email);
        $newSessionId = session_create_id();
        $sessionId = $newSessionId . "_" . $user_data["user_id"];
        session_id($sessionId);
        $_SESSION["user_id"] = $user_data["user_id"];
    }
}
