<?php
require_once "blog.inc.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $blog_title = $_POST["blog_title"];
    $blog_content = $_POST["blog_content"];
    $blog_id = $_POST["blog_id"];
    $blog_thumbnail = $_FILES["blog_thumbnail"]["name"];

    // Validate blog title format
    if (strlen($blog_title) < 5) {
        echo "Blog title must be at least 5 characters long.";
        exit();
    }

    // Validate blog content format
    if (strlen($blog_content) < 100) {
        echo "Blog content must be at least 100 characters long.";
        exit();
    }

    try {
        // Store blog data in database
        $blogModel = new Blog();
        $blog = $blogModel->getBlogById($blog_id);

        $blog_thumbnail_db = isset($blog["thumbnail"]) ? $blog["thumbnail"] : "";
        if (!empty($blog_thumbnail)) {
            $allowed = ["jpg", "png", "gif", "jpeg"];
            $image_temp = $_FILES["blog_thumbnail"]["tmp_name"];
            $image_ext = strtolower(pathinfo($blog_thumbnail, PATHINFO_EXTENSION));
            $thumbnail_url = "thumbnail_" . $blog_id . "_" . $blog_thumbnail;
            $upload_path = "../../uploads/blogs/" . $thumbnail_url;

            if (!in_array($image_ext, $allowed)) {
                echo "Invalid thumbnail format. Only JPG, PNG, GIF, and JPEG are allowed.";
                exit();
            }
            if ($blog_thumbnail_db) {
                $updateResult = $blogModel->updateBlogThumbnail($blog_id, $thumbnail_url);
                unlink("../../uploads/blogs/" . $blog_thumbnail_db);
            }

            $result =  move_uploaded_file($image_temp, $upload_path);
            // echo "Upload Success : " . $result;
            $blogModel->uploadThumbnail($thumbnail_url, $blog_id);
        }

        // Update blog data in database
        $blogModel->updateBlog($blog_id, $blog_title, $blog_content);

        header("Location: ../../blog.php?blog_id=" . $blog_id);
    } catch (PDOException $error) {
        echo "Failed to update blog : " . $error->getMessage();
    }
}
