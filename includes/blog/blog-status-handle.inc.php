<?php
require_once "blog.inc.php";

try {
    $blog_status = isset($_GET['status']) ? $_GET['status'] : "";
    $blog_id = isset($_GET['blog_id']) ? $_GET['blog_id'] : "";

    $status_id;

    if ($blog_status == "approve") {
        $status_id = 3;
    }
    if ($blog_status == "denied") {
        $status_id = 2;
    }
    // echo "Blog status: " . $blog_status;
    // echo "<br>";
    // echo "Blog ID: " . $blog_id;

    $blog = new Blog();
    $blog->changeBlogStatus($blog_id, $status_id);

    header("Location:../../dashboard.php");
} catch (PDOException $error) {
    echo "Error to status change: " . $error->getMessage();
}
