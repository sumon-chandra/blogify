<?php
require_once "model.inc.php";
require_once "view.inc.php";
require_once "contr.inc.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];
    $dob = $_POST['dob'];
    $gender_id = $_POST['gender'];
    $profile_picture = $_FILES['profile_picture']["name"];

    $errors = array();

    try {
        $signupModel = new SignupModel();
        $signupContr = new SignupContr();

        $isInputEmpty = $signupContr->isInputEmpty($first_name, $email, $pwd);

        if ($isInputEmpty) {
            $errors["input_empty"] = "All fields are required.";
        }

        // Validate and sanitize inputs
        $first_name = filter_var($first_name, FILTER_SANITIZE_STRING);
        $last_name = filter_var($last_name, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $dob = filter_var($dob, FILTER_SANITIZE_STRING);


        // Validate email format
        if (!$email) {
            $errors["invalid_email"] = "Invalid email format.";
            die();
        }

        // Validate password strength
        if (strlen($pwd) < 6) {
            $errors["weak_password"] = "Password must be at least 6 characters long.";
        }

        // Check is email exist 
        $isEmailExist = $signupContr->isEmailExist($email);
        if ($isEmailExist) {
            $errors["email_exist"] = "Email already exists.";
        }

        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

        // Upload profile picture


        // If no errors, save user data to the database
        if (empty($errors)) {
            echo "No error and ready to store data to database :";
            $signupModel->signup($first_name, $last_name, $email, $hashedPwd, $dob, $gender_id, $profile_picture);
            header("Location: ../../login.php");
        }

        // Display errors
        if ($errors) {
            echo "Errors :" . json_encode($errors);
        }
    } catch (PDOException $error) {
        echo "Error accord with signup : " . $error->getMessage();
    }
}
