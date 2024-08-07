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

    public function getAllBlogs($blog_tag)
    {
        $ROW_QUERY = "SELECT
                        b.*,
                         IF(
                            LENGTH(GROUP_CONCAT(t.tag_name)) > LENGTH(SUBSTRING_INDEX(GROUP_CONCAT(t.tag_name), ',', 2)),
                            CONCAT(SUBSTRING_INDEX(GROUP_CONCAT(t.tag_name), ',', 2), ' ...'), 
                            GROUP_CONCAT(t.tag_name)
                        ) AS tags
                    FROM
                        blogs AS b
                    LEFT JOIN blog_tags AS bt
                    ON b.blog_id = bt.blog_id
                    LEFT JOIN tags AS t 
                    ON bt.tag_id = t.tag_id
                    GROUP BY b.blog_id ORDER BY blog_id DESC;";

        $TAG_QUERY = "SELECT
                        b.*,
                         IF(
                            LENGTH(GROUP_CONCAT(t.tag_name)) > LENGTH(SUBSTRING_INDEX(GROUP_CONCAT(t.tag_name), ',', 2)),
                            CONCAT(SUBSTRING_INDEX(GROUP_CONCAT(t.tag_name), ',', 2), ' ...'), 
                            GROUP_CONCAT(t.tag_name)
                        ) AS tags
                    FROM
                        blogs AS b
                    LEFT JOIN blog_tags AS bt
                    ON b.blog_id = bt.blog_id
                    LEFT JOIN tags AS t 
                    ON bt.tag_id = t.tag_id
                    WHERE t.tag_name = '$blog_tag'
                    GROUP BY b.blog_id ORDER BY blog_id DESC;";

        $query = $blog_tag ? $TAG_QUERY : $ROW_QUERY;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function storeNewBlog($title, $content, $thumbnail, $author_id)
    {
        $query = "INSERT INTO blogs (title, content, thumbnail, author_id) VALUES (:title, :content, :thumbnail, :author_id);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':thumbnail', $thumbnail);
        $stmt->bindParam(':author_id', $author_id);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function getSingleBlog($blog_id)
    {
        $query = "SELECT
                        b.*,
                        CONCAT(u.first_name, ' ', u.last_name) AS author_name,
                        u.profile_picture,
                        COALESCE(l.total_likes, 0) AS total_likes,
                        GROUP_CONCAT(DISTINCT t.tag_name SEPARATOR ', ') AS tags
                    FROM blogs AS b
                    LEFT JOIN users AS u ON b.author_id = u.user_id
                    LEFT JOIN (
                        SELECT blog_id, COUNT(like_id) AS total_likes
                        FROM likes
                        GROUP BY blog_id
                    ) AS l ON b.blog_id = l.blog_id
                    LEFT JOIN blog_tags AS bt ON b.blog_id = bt.blog_id
                    LEFT JOIN tags AS t ON bt.tag_id = t.tag_id
                    WHERE b.blog_id = :blog_id
                    GROUP BY b.blog_id, u.first_name, u.last_name, u.profile_picture, l.total_likes;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':blog_id', $blog_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
