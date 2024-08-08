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

    public function getAllBlogs($queryConditions, $queryParameters, $query, $sort_by)
    {
        if ($queryConditions) {
            $query .= " WHERE " . implode(" AND ", $queryConditions);
        }

        $query .= " GROUP BY b.blog_id ORDER BY";

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

        // echo "Query: " . $query;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($queryParameters);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function storeThumbnail($thumbnail, $blog_id)
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

    public function getAuthors()
    {
        $query = "SELECT 
                    
                    CONCAT(u.first_name , ' ', u.last_name) AS author_name,
                    u.user_id AS author_id,
                    r.role
                    FROM users AS u 
                    INNER JOIN roles AS r 
                    ON r.role_id = u.role_id
                    WHERE u.role_id = '2'
                    ORDER BY first_name ASC;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTags()
    {
        $query = "SELECT * FROM tags;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
