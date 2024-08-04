<?php
require_once "blog.inc.php";
require_once "../config.session.php";
$author_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $blog_title = $_POST["blog_title"];
    $blog_content = $_POST["blog_content"];
    // $blog_category_id = $_POST["blog_category"];
    $blog_thumbnail = $_FILES["blog_thumbnail"]["name"];

    try {
        $errors = array();
        $title = filter_var($blog_title, FILTER_SANITIZE_STRING);
        $content = filter_var($blog_content, FILTER_SANITIZE_STRING);

        if (!$title || !$content) {
            $errors["invalid_data"] = "Invalid blog data, please provide valid data.";
            die();
        }

        // Validate blog title format
        if (strlen($blog_title) < 5) {
            $errors["invalid_blog_title"] = "Blog title must be at least 5 characters long.";
            die();
        }
        // Validate blog content format
        if (strlen($blog_content) < 100) {
            $errors["invalid_blog_content"] = "Blog content must be at least 100 characters long.";
            die();
        }

        // Validate blog thumbnail format
        // Upload blog thumbnail
        $thumbnail = null;

        $blog = new Blog();
        $newBlogId = $blog->createNewBlog($title, $content, $thumbnail, $author_id);


        header("Location: ../../dashboard.php");
        die();
    } catch (PDOException $error) {
        echo "Error to create a blog : " . $error->getMessage();
    }
}
