<?php
require_once "../database.php";
require_once "model.inc.php";

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
        $model = new SignupModel();
        $user = $model->checkEmailExists($email);
        $user_email = isset($user["email"]) ? $user["email"] : "";
        // echo "User Email :" . $user_email ? $user_email : "Unavailable Email";
        if ($user_email) {
            return true;
        } else {
            return false;
        }
    }
}
