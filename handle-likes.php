<?php
require_once "includes/database.php";
require_once "includes/blog/blog.inc.php";

try {
    $blog_id = isset($_POST['blog_id']) ? $_POST['blog_id'] : "";
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : "";
    $data = [];

    // Check if user id not logged in 
    if (!$user_id) {
        $data = [
            "status" => "unauthorized",
            "message" => "User id is required for liking!"
        ];
        die();
    }

    $blog = new Blog();
    $row = $blog->likeBlog($blog_id, $user_id);

    if ($row) {
        $data = [
            "status" => "success",
            "message" => "Blog liked successfully!",
            "data" => $blog->getTotalLikes($blog_id, $user_id)
        ];
    } else {
        $data = [
            "status" => "failed",
            "message" => "Failed to like the blog!"
        ];
    }

    echo json_encode($data);
} catch (PDOException $error) {
    echo "Failed to connect to the database while like: ----- " . $error->getMessage();
}
