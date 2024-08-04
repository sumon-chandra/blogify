<?php
require_once "model.inc.php";

class Blog
{
    public function getBlogs()
    {
        $blogModel = new BlogModel();
        $blogs = $blogModel->getAllBlogs();
        return $blogs;
    }

    public function createNewBlog($title, $content, $thumbnail, $author_id)
    {
        $blogModel = new BlogModel();
        $newBlogId = $blogModel->storeNewBlog($title, $content, $thumbnail, $author_id);
        return $newBlogId;
    }
}
