<?php
require_once "blog.inc.php";
require_once "../config.session.php";
$author_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $blog_title = $_POST["blog_title"];
    $blog_content = $_POST["blog_content"];
    $blog_thumbnail = $_FILES["blog_thumbnail"]["name"];
    $blog_tags = $_REQUEST["tags"];

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

        $blog = new Blog();
        $newBlogId = $blog->createNewBlog($title, $content, $author_id);

        // Insert tags into the blog
        if (!empty($blog_tags)) {
            foreach ($blog_tags as $tag_id) {
                $blog->insertBlogTag($tag_id, $newBlogId);
            }
        }

        $allowed = ["jpg", "png", "gif", "jpeg", "webp"];
        $image_temp = $_FILES["blog_thumbnail"]["tmp_name"];
        $image_ext = strtolower(pathinfo($blog_thumbnail, PATHINFO_EXTENSION));
        $thumbnail_url = "thumbnail_" . $newBlogId . "_" . $blog_thumbnail;
        $upload_path = "../../uploads/blogs/" . $thumbnail_url;

        if ($blog_thumbnail) {
            if (!in_array($image_ext, $allowed)) {
                $errors["invalid_thumb_format"] = "Invalid thumbnail format. Only JPG, PNG, GIF, and JPEG are allowed.";
                die();
            }
            $result =  move_uploaded_file($image_temp, $upload_path);
            $blog->uploadThumbnail($thumbnail_url, $newBlogId);
        }

        header("Location: ../../blogs.php");
        die();
    } catch (PDOException $error) {
        echo "Error to create a blog : " . $error->getMessage();
    }
}
