<?php
require_once "includes/database.php";
require_once "includes/blog/blog.inc.php";
require_once "includes/blog/view.inc.php";

try {
    $last_blog_id = $_POST["last_blog_id"];
    $blog = new Blog();

    $blogs = $blog->getMoreBlogs($last_blog_id);
    $data = [];
    if ($blogs) {
        $loadedData = "";
        foreach ($blogs as $blog) {
            $loadedData .= displayBlog($blog);
        }
        $data = [
            "success" => true,
            "data" => $loadedData,
            "load_more_btn" => displayLoadMoreBtn($blog['blog_id'])
        ];
    } else {
        $data = [
            "success" => false,
            "message" => "No more blogs to load!"
        ];
    }
    echo json_encode($data);
} catch (PDOException $e) {
    die("Error to get more blogs: ---------" . $e->getMessage());
}
