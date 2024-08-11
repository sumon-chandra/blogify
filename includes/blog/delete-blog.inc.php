<?php
require_once "blog.inc.php";

try {
    $blog_id = isset($_GET['blog_id']) ? $_GET['blog_id'] : "";
    $blogObject = new Blog();
    $blogObject->deleteBlog($blog_id);

    header("Location: ../../profile.php");
} catch (PDOException $error) {
    echo "Something went wrong! Please try again " . $error->getMessage();
}
