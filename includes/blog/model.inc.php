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

    public function getAllBlogs()
    {
        $query = "SELECT
                    b.*,
                    GROUP_CONCAT(t.tag_name) AS tags
                FROM
                    blogs AS b
                LEFT JOIN blog_tags AS bt
                ON b.blog_id = bt.blog_id
                LEFT JOIN tags AS t 
                ON bt.tag_id = t.tag_id
                GROUP BY b.blog_id ORDER BY blog_id DESC;";
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
}
