<?php
require_once "contr.inc.php";
require_once "../config.session.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $pwd = $_POST["pwd"];

    try {
        $errors = array();

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["invalid_email"] = "Invalid email format";
            die();
        }

        // Validate password strength method 01
        // if (strlen($pwd) < 6 ||!preg_match('/[A-Z]/', $pwd) ||!preg_match('/[a-z]/', $pwd) ||!preg_match('/[0-9]/', $pwd)) {
        //     $errors["weak_password"] = "Password must be at least 6 characters long and contain at least one uppercase letter, one lowercase letter, and one number";
        //     die();
        // }

        // Validate password strength method 02
        if (strlen($pwd) < 6) {
            $errors["weak_password"] = "Password must be at least 6 characters long";
            die();
        }

        $loginContr = new LoginContr();
        $isInputEmpty = $loginContr->isInputEmpty($email, $pwd);
        if ($isInputEmpty) {
            $errors["empty_fields"] = "All fields are required";
            die();
        }
        // Check if the user exist
        $isUserExist = $loginContr->isUserExist($email);
        if (!$isUserExist) {
            $errors["wrong_credentials"] = "User does not exist!";
            header("Location: ../../login.php");
            die();
        }

        // Check if the password is match
        $isMatch = $loginContr->isPasswordMatch($email, $pwd);
        if (!$isMatch) {
            $errors["wrong_credentials"] = "Incorrect password!";
            header("Location: ../../login.php");
            die();
        }

        // Set errors to session
        if ($errors) {
            $_SESSION["login_errors"] = $errors;
            header("Location: ../../login.php");
            die();
        }

        // Login the user
        $loginContr->login($email);
        $_SESSION["last_regenerate_time"] = time();


        header("Location: ../../index.php");
        die();
    } catch (PDOException $error) {
        echo "Something went wrong with Login : " . $error->getMessage();
    }
}
