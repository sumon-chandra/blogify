<?php
// require_once "../database.php";
$dbPath = dirname(__DIR__) . '/database.php';
if (file_exists($dbPath)) {
    require_once $dbPath;
}

class BlogModel
{
    private $pdo;

    function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function getAllBlogs($queryParameters, $query)
    {
        // echo "Query : ---- " . $query;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($queryParameters);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMoreBlogs($last_blog_id)
    {
        $query = "SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) AS author_name, IF( LENGTH(GROUP_CONCAT(t.tag_name)) > LENGTH(SUBSTRING_INDEX(GROUP_CONCAT(t.tag_name), ',', 2)), CONCAT(SUBSTRING_INDEX(GROUP_CONCAT(t.tag_name), ',', 2), ' ...'), GROUP_CONCAT(t.tag_name) ) AS tags FROM blogs AS b LEFT JOIN blog_tags AS bt ON b.blog_id = bt.blog_id LEFT JOIN tags AS t ON bt.tag_id = t.tag_id LEFT JOIN users AS u ON b.author_id = u.user_id WHERE b.status_id = '3' AND b.blog_id < " . $last_blog_id . " GROUP BY b.blog_id ORDER BY blog_id DESC LIMIT 3;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function relatedBlogs($tag_name, $blog_id)
    {
        $query = "SELECT
                    b.*,
                    CONCAT(u.first_name, ' ', u.last_name) AS author_name,
                    IF(
                        LENGTH(GROUP_CONCAT(t.tag_name)) > LENGTH(SUBSTRING_INDEX(GROUP_CONCAT(t.tag_name), ',', 2)),
                        CONCAT(SUBSTRING_INDEX(GROUP_CONCAT(t.tag_name), ',', 2), ' ...' ),
                        GROUP_CONCAT(t.tag_name)
                    ) AS tags
                    FROM blogs AS b
                    LEFT JOIN blog_tags AS bt
                    ON b.blog_id = bt.blog_id
                    LEFT JOIN tags AS t
                    ON bt.tag_id = t.tag_id
                    LEFT JOIN users AS u
                    ON b.author_id = u.user_id
                    WHERE b.blog_id IN (
                            SELECT 
                                bt.blog_id 
                            FROM blog_tags AS bt  
                            INNER JOIN tags AS t 
                            ON bt.tag_id = t.tag_id 
                            WHERE t.tag_name = :tag_name
                        ) AND b.status_id = '3' AND NOT b.blog_id = :blog_id
                    GROUP BY b.blog_id
                    ORDER BY b.blog_id DESC;";
        // echo "Related blog Query: " . $query;
        // echo "Tag Name: " . $tag_name;
        // echo "</=>";
        // echo "Blog ID: " . $blog_id;
        // echo "</br>";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':tag_name', $tag_name);
        $stmt->bindParam(':blog_id', $blog_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingBlogs()
    {
        $query = "SELECT 
                    b.blog_id,
                    b.title,
                    DATE_FORMAT(b.created_at, '%d %M %Y - %l:%i %p') AS created_at,
                    CONCAT(u.first_name, ' ', u.last_name) AS author_name,
                    u.user_id AS author_id
                    FROM blogs AS b 
                    LEFT JOIN users AS u 
                    ON u.user_id = b.author_id 
                    WHERE b.status_id = '1' ORDER BY b.blog_id DESC;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBlogsByStatusAndUser($author_id, $status_id)
    {
        $query = "SELECT 
                    b.*,
                    CONCAT(u.first_name, ' ', u.last_name) AS author_name,
                    u.user_id AS author_id
                    FROM blogs AS b 
                    LEFT JOIN users AS u 
                    ON u.user_id = b.author_id 
                    WHERE b.status_id = :status_id AND b.author_id = :author_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':status_id', $status_id);
        $stmt->bindParam(':author_id', $author_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function totalBlogs()
    {
        $query = "SELECT COUNT(*) as total FROM blogs WHERE status_id = '3';";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function totalAuthors()
    {
        $query = "SELECT COUNT(*) as total FROM users WHERE role_id = '2';";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    public function getAllApprovedBlogsById($user_id)
    {
        $query = "SELECT 
                    b.*,
                    COALESCE(l.total_likes, 0) AS total_likes,
                    COALESCE(c.total_comments, 0) AS total_comments,
                    GROUP_CONCAT(DISTINCT t.tag_name SEPARATOR ', ') AS tags
                    FROM blogs AS b 
                    LEFT JOIN (
                        SELECT blog_id, COUNT(like_id) AS total_likes FROM likes GROUP BY blog_id
                    ) AS l ON b.blog_id = l.blog_id
                    LEFT JOIN (
                        SELECT blog_id, COUNT(comment_id) AS total_comments FROM comments GROUP BY blog_id
                    ) AS c ON b.blog_id = c.blog_id
                    LEFT JOIN blog_tags AS bt ON b.blog_id = bt.blog_id
                    LEFT JOIN tags AS t ON bt.tag_id = t.tag_id
                    WHERE b.author_id = :user_id AND b.status_id = '3'
                    GROUP BY b.blog_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllDeniedBlogsById($user_id)
    {
        $query = "SELECT * FROM blogs WHERE author_id = :user_id AND status_id = '2';";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pendingBlogs()
    {
        $query = "SELECT COUNT(*) AS total FROM blogs WHERE status_id = '1';";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)["total"];
    }

    public function storeNewBlog($title, $content, $author_id)
    {
        $query = "INSERT INTO blogs (title, content, author_id) VALUES (:title, :content, :author_id);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':author_id', $author_id);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function storeBlogLike($blog_id, $user_id)
    {
        $query = "INSERT INTO likes (blog_id, user_id) VALUES (:blog_id, :user_id);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':blog_id', $blog_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function storeBlogTag($tag_id, $newBlogId)
    {
        $query = "INSERT INTO blog_tags (blog_id, tag_id) VALUES (:blog_id, (SELECT tag_id FROM tags WHERE tag_id = :tag_id));";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':blog_id', $newBlogId);
        $stmt->bindParam(':tag_id', $tag_id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function storeThumbnail($thumbnail, $blog_id)
    {
        $query = "UPDATE blogs SET thumbnail = :thumbnail WHERE blog_id = :blog_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':thumbnail', $thumbnail);
        $stmt->bindParam(':blog_id', $blog_id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function updateThumbnail($blog_id, $thumbnail)
    {
        $query = "UPDATE blogs SET thumbnail = :thumbnail WHERE blog_id = :blog_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':thumbnail', $thumbnail);
        $stmt->bindParam(':blog_id', $blog_id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function getSingleBlog($blog_id)
    {
        $query = "SELECT
                        b.*,
                        CONCAT(u.first_name, ' ', u.last_name) AS author_name,
                        u.avatar,
                        GROUP_CONCAT(DISTINCT t.tag_name SEPARATOR ', ') AS tags
                    FROM blogs AS b
                    LEFT JOIN users AS u ON b.author_id = u.user_id
                    LEFT JOIN blog_tags AS bt ON b.blog_id = bt.blog_id
                    LEFT JOIN tags AS t ON bt.tag_id = t.tag_id
                    WHERE b.blog_id = :blog_id
                    GROUP BY b.blog_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':blog_id', $blog_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalLikes($blog_id, $user_id)
    {
        $query = "SELECT 
                    COUNT(*) AS total_likes,
                    SUM(CASE WHEN user_id = :user_id THEN 1 ELSE 0 END) AS has_liked
                FROM likes
                WHERE blog_id = :blog_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':blog_id', $blog_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAuthors()
    {
        $query = "SELECT 
                    CONCAT(u.first_name , ' ', u.last_name) AS author_name,
                    u.user_id AS author_id,
                    u.email,
                    u.avatar AS avatar,
                    r.role,
                    g.gender
                    FROM users AS u 
                    INNER JOIN roles AS r 
                    ON r.role_id = u.role_id
                    INNER JOIN genders AS g
                    ON g.gender_id = u.gender_id
                    WHERE u.role_id = '2'
                    ORDER BY u.created_at DESC;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAuthor($author_id)
    {
        $query = "SELECT
                    CONCAT(u.first_name, ' ', u.last_name) AS author_name,
                    u.dob,
                    u.email,
                    u.updated_at,
                    u.user_id AS author_id,
                    u.avatar,
                    g.gender,
                    r.role
                FROM
                    users AS u
                INNER JOIN roles AS r
                ON
                    r.role_id = u.role_id
                    INNER JOIN genders AS g 
                    ON g.gender_id = u.gender_id
                WHERE
                    u.user_id = :author_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':author_id', $author_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTags()
    {
        $query = "SELECT * FROM tags;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateBlog($blog_id, $title, $content)
    {
        $query = "UPDATE blogs SET title = :title, content = :content WHERE blog_id = :blog_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':blog_id', $blog_id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function deleteBlog($blog_id)
    {
        $query = "DELETE FROM blogs WHERE blog_id = :blog_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':blog_id', $blog_id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function deleteBlogTags($blog_id)
    {
        $query = "DELETE FROM blog_tags WHERE blog_id = :blog_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':blog_id', $blog_id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function changeBlogStatus($blog_id, $status_id)
    {
        $query = "UPDATE blogs SET status_id = :status_id WHERE blog_id = :blog_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':status_id', $status_id);
        $stmt->bindParam(':blog_id', $blog_id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function updateViewCount($blog_id)
    {
        $query = "UPDATE blogs SET total_views = total_views + 1 WHERE blog_id = :blog_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':blog_id', $blog_id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function insertComments($comment_text, $user_id, $blog_id)
    {
        $query = "INSERT INTO comments (comment_text, user_id, blog_id) VALUES (:comment_text, :user_id, :blog_id);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":comment_text", $comment_text);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":blog_id", $blog_id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function selectComments($blog_id, $sort_by)
    {
        $query = "SELECT 
                    c.comment_id,
                    c.comment_text,
                    c.user_id, 
                    DATE_FORMAT(c.created_at , '%d %b %Y - %i:%I %p')  AS created_at,
                    CONCAT(u.first_name, ' ', u.last_name) AS comment_author,
                    u.avatar AS author_avatar
                    FROM comments AS c 
                    LEFT JOIN users AS u 
                    ON u.user_id = c.user_id
                    WHERE c.blog_id = :blog_id ORDER BY c.created_at";
        if ($sort_by == "new") {
            $query .= " DESC;";
        } else if ($sort_by == "old") {
            $query .= " ASC;";
        } else {
            $query .= " DESC;";
        }
        // echo $query;

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":blog_id", $blog_id);
        $stmt->execute();
        $allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $allComments;
    }
}
