<?php
require_once "user.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $dob = $_POST["dob"];
    $gender_id = $_POST["gender"];
    $avatar_file = $_FILES["avatar"]["name"];

    try {
        $userModel = new User();
        $avatar = $userModel->getUserById($user_id)["avatar"];
        if (!empty($avatar_file)) {
            $temp_name = $_FILES["avatar"]["tmp_name"];
            $ext = pathinfo($avatar_file, PATHINFO_EXTENSION);
            $allowed = ["jpg", "png", "jpeg", "webp"];
            if (!in_array($ext, $allowed)) {
                echo "Invalid profile picture format. Only JPG, PNG, JPEG, and WebP are allowed.";
                die();
            } else {
                $image_url = "avatar_" . rand(1, 20) . "_" . $avatar_file;
                $upload_path = "../../uploads/avatars/" . $image_url;
                $result = move_uploaded_file($temp_name, $upload_path);
                if ($result) {
                    $avatar = $image_url;
                }
            }
        }

        $updateResult = $userModel->updateUser($user_id, $first_name, $last_name, $dob, $gender_id, $avatar);

        header("Location: ../../profile.php");
    } catch (PDOException $error) {
        echo "Error updating user : " . $error->getMessage();
    }
}
