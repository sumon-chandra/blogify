<?php
require_once "model.inc.php";
require_once "view.inc.php";

class Blog
{
    public function getBlogs($blog_tag, $author_id, $sort_by, $start_date, $end_date, $search_query)
    {
        $query = "SELECT
                    b.*,
                    CONCAT(u.first_name, ' ', u.last_name) AS author_name,
                    IF(
                        LENGTH(GROUP_CONCAT(t.tag_name)) > LENGTH(SUBSTRING_INDEX(GROUP_CONCAT(t.tag_name), ',', 2)),
                        CONCAT(SUBSTRING_INDEX(GROUP_CONCAT(t.tag_name), ',', 2), ' ...'), 
                        GROUP_CONCAT(t.tag_name)
                    ) AS tags";

        if (!empty($sort_by)) {
            if ($sort_by == "most_likes") {
                $query .= ", COALESCE(l.total_likes, 0) AS total_likes";
            }
        }

        $query .= " FROM
                    blogs AS b
                LEFT JOIN blog_tags AS bt
                ON b.blog_id = bt.blog_id
                LEFT JOIN tags AS t 
                ON bt.tag_id = t.tag_id
                LEFT JOIN users AS u 
                ON b.author_id = u.user_id";

        if (!empty($sort_by)) {
            if ($sort_by == "most_likes") {
                $query .= " LEFT JOIN (
                            SELECT blog_id, COUNT(like_id) AS total_likes
                            FROM likes
                            GROUP BY blog_id
                        ) AS l 
                         ON b.blog_id = l.blog_id";
            }
        }

        $queryConditions = [];
        $queryParameters = [];
        if (!empty($blog_tag)) {
            $queryConditions[] = " b.blog_id IN (
                                    SELECT bt.blog_id FROM blog_tags AS bt 
                                    INNER JOIN tags AS t 
                                    ON bt.tag_id = t.tag_id 
                                    WHERE t.tag_name = :tag_name
                                    )
                               ";
            $queryParameters['tag_name'] = $blog_tag;
        }
        if (!empty($author_id)) {
            $queryConditions[] = "author_id = :author_id";
            $queryParameters['author_id'] = $author_id;
        }
        if (!empty($start_date)) {
            $queryConditions[] = "created_at >= :start_date";
            $queryParameters['start_date'] = $start_date;
        }
        if (!empty($end_date)) {
            $queryConditions[] = "created_at <= :end_date";
            $queryParameters['end_date'] = $end_date;
        }
        if (!empty($start_date) && !empty($end_date)) {
            // Check if start date is before end date
            if (strtotime($start_date) > strtotime($end_date)) {
                die("Invalid date range. Start date must be before end date.");
            }

            $queryConditions[] = "created_at BETWEEN :start_date AND :end_date";
            $queryParameters['start_date'] = $start_date;
            $queryParameters['end_date'] = $end_date;
        }

        if (!empty($start_date)) {
            $queryConditions[] = "created_at >= :start_date";
            $queryParameters['start_date'] = $start_date;
        }

        if (!empty($end_date)) {
            $queryConditions[] = "created_at <= :end_date";
            $queryParameters['end_date'] = $end_date;
        }
        if (!empty($search_query)) {
            $queryConditions[] = "title LIKE :search_query";
            $queryParameters['search_query'] = "%{$search_query}%";
        }

        if ($queryConditions) {
            $query .= " WHERE " . implode(" AND ", $queryConditions) . " AND b.status_id = '3' GROUP BY b.blog_id ORDER BY";
        } else {
            $query .= " WHERE b.status_id = '3' GROUP BY b.blog_id ORDER BY";
        }


        if (!empty($sort_by)) {
            if ($sort_by == "newly_created") {
                $query .= " blog_id DESC";
            } else if ($sort_by == "old_created") {
                $query .= " blog_id ASC";
            } else if ($sort_by == "most_likes") {
                $query .= " blog_id DESC";
            }
        } else {
            $query .= " blog_id DESC";
        }

        $query .= " LIMIT 3;";
        // echo $query;
        $blogModel = new BlogModel();
        $blogs = $blogModel->getAllBlogs($queryParameters, $query);
        return $blogs;
    }

    public function getMoreBlogs($last_blog_id)
    {
        $blogModel = new BlogModel();
        $blogs = $blogModel->getMoreBlogs($last_blog_id);
        return $blogs;
    }

    public function relatedBlogs($tag_name, $blog_id)
    {
        $blogModel = new BlogModel();
        $relatedBlogs = $blogModel->relatedBlogs($tag_name, $blog_id);
        return $relatedBlogs;
    }

    public function totalBlogs()
    {
        $blogModel = new BlogModel();
        $totalBlogs = $blogModel->totalBlogs();
        return $totalBlogs;
    }

    public function pendingBlogs()
    {
        $blogModel = new BlogModel();
        $pendingBlogs = $blogModel->pendingBlogs();
        return $pendingBlogs;
    }

    public function getBlogsByStatusAndUser($user_id, $status_id)
    {
        $blogModel = new BlogModel();
        $pendingBlogs = $blogModel->getBlogsByStatusAndUser($user_id, $status_id);
        return $pendingBlogs;
    }

    function getPendingBlogs()
    {
        $blogModel = new BlogModel();
        $pendingBlogs = $blogModel->getPendingBlogs();
        return $pendingBlogs;
    }

    public function getApprovedBlogsById($user_id)
    {
        $blogModel = new BlogModel();
        $blogs = $blogModel->getAllApprovedBlogsById($user_id);
        return $blogs;
    }

    public function getDeniedBlogsById($user_id)
    {
        $blogModel = new BlogModel();
        $blogs = $blogModel->getAllDeniedBlogsById($user_id);
        return $blogs;
    }

    public function createNewBlog($title, $content, $author_id)
    {
        $blogModel = new BlogModel();
        $newBlogId = $blogModel->storeNewBlog($title, $content, $author_id);
        return $newBlogId;
    }

    public function insertBlogTag($tag_id, $newBlogId)
    {
        $blogModel = new BlogModel();
        $blogModel->storeBlogTag($tag_id, $newBlogId);
    }

    public function likeBlog($blog_id, $user_id)
    {
        $blogModel = new BlogModel();
        return $blogModel->storeBlogLike($blog_id, $user_id);
    }

    public function getTotalLikes($blog_id, $user_id)
    {
        $blogModel = new BlogModel();
        return $blogModel->getTotalLikes($blog_id, $user_id);
    }

    public function uploadThumbnail($thumbnail, $blog_id)
    {
        $blogModel = new BlogModel();
        $blogModel->storeThumbnail($thumbnail, $blog_id);
    }

    public function updateBlogThumbnail($blog_id, $thumbnail)
    {
        $blogModel = new BlogModel();
        $blogModel->updateThumbnail($blog_id, $thumbnail);
    }

    public function getBlogById($blog_id)
    {
        $blogModel = new BlogModel();
        $blog = $blogModel->getSingleBlog($blog_id);

        // Manage blog view count
        if (!isset($_COOKIE["view_count"])) {
            setcookie("view_count", $blog["blog_id"], time() + (86400 * 30), "/"); // 86400 = 1 day (The count will increase every 30 days)
            $blogModel->updateViewCount($blog_id);
        } else if (isset($_COOKIE["view_count"]) && $_COOKIE["view_count"] != $blog["blog_id"]) {
            setcookie("view_count", $blog["blog_id"], time() + (86400 * 30), "/");
            $blogModel->updateViewCount($blog_id);
        }
        return $blog;
    }

    public function getAuthors()
    {
        $blogModel = new BlogModel();
        $authors = $blogModel->getAuthors();
        return $authors;
    }

    public function totalAuthors()
    {
        $blogModel = new BlogModel();
        $totalAuthors = $blogModel->totalAuthors();
        return $totalAuthors;
    }

    public function getAuthor($author_id)
    {
        $blogModel = new BlogModel();
        $author = $blogModel->getAuthor($author_id);
        return $author;
    }

    public function getTags()
    {
        $blogModel = new BlogModel();
        $tags = $blogModel->getTags();
        return $tags;
    }

    public function updateBlog($blog_id, $title, $content)
    {
        $blogModel = new BlogModel();
        $blogModel->updateBlog($blog_id, $title, $content);
    }

    public function deleteBlog($blog_id)
    {
        $blogModel = new BlogModel();
        $blogModel->deleteBlog($blog_id);
        $blogModel->deleteBlogTags($blog_id);
    }

    public function changeBlogStatus($blog_id, $status_id)
    {
        $blogModel = new BlogModel();
        $blogModel->changeBlogStatus($blog_id, $status_id);
    }

    public function addComment($comment_text, $user_id, $blog_id)
    {
        $blogModel = new BlogModel();
        $blogModel->insertComments($comment_text, $user_id, $blog_id);
    }

    public function getComments($blog_id)
    {
        $blogModel = new BlogModel();
        $data =  $blogModel->selectComments($blog_id);
        return $data;
    }
}
