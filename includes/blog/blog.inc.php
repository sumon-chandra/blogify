<?php
require_once "model.inc.php";

class Blog
{
    public function getBlogs($blog_tag)
    {
        $blogModel = new BlogModel();
        $blogs = $blogModel->getAllBlogs($blog_tag);
        return $blogs;
    }

    public function createNewBlog($title, $content, $thumbnail, $author_id)
    {
        $blogModel = new BlogModel();
        $newBlogId = $blogModel->storeNewBlog($title, $content, $thumbnail, $author_id);
        return $newBlogId;
    }

    public function getBlogById($blog_id)
    {
        $blogModel = new BlogModel();
        $blog = $blogModel->getSingleBlog($blog_id);
        return $blog;
    }
}
