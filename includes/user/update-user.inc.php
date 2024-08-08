<?php
require_once "user.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $dob = $_POST["dob"];
    $gender_id = $_POST["gender"];
    // $profile_picture = $_FILES["profile_picture"]["name"];

    $userModel = new User();
    $updateResult = $userModel->updateUser($user_id, $first_name, $last_name, $dob, $gender_id);
    if ($updateResult) {
        header("Location: ../../profile.php");
    } else {
        echo "Failed to update user.";
    }
}
