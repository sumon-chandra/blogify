<?php
require_once "includes/database.php";
require_once "includes/blog/blog.inc.php";

try {
    $blog_id = isset($_POST['blog_id']) ? $_POST['blog_id'] : "";
    $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : "";
    $data = [];

    if (!$blog_id || !$sort_by) {
        $data = [
            "status" => "error",
            "message" => "Blog id and user id are required!"
        ];
        echo json_encode($data);
        die();
    }

    $blog = new Blog();
    $comments = $blog->getComments($blog_id, $sort_by);

    if ($comments) {
        $all_comments = "";
        foreach ($comments as $comment) {
            $all_comments .= displayComment($comment);
        }
        $data = [
            "status" => "success",
            "comments" => $all_comments
        ];
    }

    echo json_encode($data);
} catch (PDOException $error) {
    echo "Error while get comments by sorting --------:- " . $error->getMessage();
}
