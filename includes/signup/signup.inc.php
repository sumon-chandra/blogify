<?php
require_once "model.inc.php";
require_once "view.inc.php";
require_once "contr.inc.php";
require_once "../config.session.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];
    $dob = $_POST['dob'];
    $gender_id = $_POST['gender'];
    $avatar_file = $_FILES['avatar']["name"];

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
        $avatar = null;
        if ($avatar_file) {
            $allowed = ["jpg", "png", "jpeg", "gif", "webp"];
            $temp_name = $_FILES["avatar"]["tmp_name"];
            $ext = strtolower(pathinfo($avatar_file, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $errors["invalid_avatar_format"] = "Invalid profile picture format. Only JPG, PNG, JPEG, GIF, and WebP are allowed.";
            } else {
                $image_url = "avatar_" . rand(1, 20) . "_" . $avatar_file;
                $upload_path = "../../uploads/avatars/" . $image_url;
                $result = move_uploaded_file($temp_name, $upload_path);
                if ($result) {
                    $avatar = $image_url;
                } else {
                    $errors["avatar_upload_failed"] = "Failed to upload profile picture.";
                }
            }
        }

        // If no errors, save user data to the database
        if (empty($errors)) {
            $signupModel->signup($first_name, $last_name, $email, $hashedPwd, $dob, $gender_id, $avatar);
            header("Location: ../../login.php");
        }

        // Display errors
        if ($errors) {
            $_SESSION["signup_errors"] = $errors;
        }
    } catch (PDOException $error) {
        echo "Error accord with signup : " . $error->getMessage();
    }
}
