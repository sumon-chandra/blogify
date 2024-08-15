<?php
require_once "../config.session.php";
require_once "blog.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment = $_POST["comment"];
    $user_id = $_POST["user_id"];
    $blog_id = $_POST["blog_id"];

    $errors = [];
    // Check comment
    if (empty($comment)) {
        $errors["comment_error"] = "Comment can not be empty! Please write a comment.";
        die();
    }

    // Store comment to database
    try {
        $blogObj = new Blog();
        $blogObj->addComment($comment, $user_id, $blog_id);

        header("Location: ../../blog.php?blog_id=$blog_id");
    } catch (PDOException $e) {
        echo "Error while comment. " . $e->getMessage();
    }
}
