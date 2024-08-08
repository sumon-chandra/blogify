<?php
require_once "../database.php";
require_once "../user/user.inc.php";

class SignupContr
{
    public function isInputEmpty($first_name, $email, $pwd)
    {
        if (empty($first_name) || empty($email) || empty($pwd)) {
            return true;
        }
    }

    public function isEmailExist($email)
    {
        $user = new User();
        $user = $user->getUserByEmail($email);
        $user_email = isset($user["email"]) ? $user["email"] : "";
        if ($user_email) {
            return true;
        } else {
            return false;
        }
    }
}
