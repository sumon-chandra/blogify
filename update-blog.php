<?php
require_once "includes/config.session.php";
require_once "includes/blog/blog.inc.php";
require_once "includes/user/user.inc.php";

$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";
$blog_id = isset($_GET["id"]) ? $_GET["id"] : "";
$isLoggedId = $user_id;

if (!$isLoggedId) {
    header("Location: login.php");
    die();
}

$blogObject = new Blog();
$userModel = new User();
$user_role = $userModel->userRole($user_id);
$admin = $user_role == "Admin" ? "Admin" : "";
$blog = $blogObject->getBlogById($blog_id);

if (!$blog) {
    header("Location: blogs.php");
    die();
}

$blog_title = $blog["title"];
$blog_content = $blog["content"];
$blog_id = $blog["blog_id"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create A New Blog - Blogify</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen w-full bg-gray-200">
    <header class="bg-gray-800">
        <nav class="flex items-center justify-between p-4 lg:w-[1250px] mx-auto">
            <h1 class="text-2xl font-bold text-white"><a href="index.php">Blogify</a>
            </h1>
            <ul class="flex">
                <li class="mx-4"><a href="#" class="text-white hover:text-gray-400">Home</a></li>
                <li class="mx-4"><a href="#" class="text-white hover:text-gray-400">About</a></li>
                <li class="mx-4"><a href="#" class="text-white hover:text-gray-400">Contact</a></li>
                <li class="mx-4"><a href="blogs.php" class="text-white hover:text-gray-400">Blogs</a></li>
                <?php if ($isLoggedId) : ?>
                    <?php if ($admin) : ?>
                        <li class="mx-4"><a href="dashboard.php" class="text-white hover:text-gray-400">Dashboard</a></li>
                    <?php endif; ?>
                    <li class="mx-4"><a href="profile.php" class="text-white hover:text-gray-400">Profile</a></li>
                    <li class="mx-4"><a href="includes/login/logout.inc.php" class="text-white hover:text-gray-400">Logout</a></li>
                <?php else : ?>
                    <li class="mx-4"><a href="login.php" class="text-white hover:text-gray-400">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="lg:w-[1250px] mx-auto">
        <h1 class="text-2xl font-bold text-center my-10">Update Blog</h1>
        <form action="includes/blog/update-blog.inc.php" enctype="multipart/form-data" method="post" class="lg:w-3/5 mx-auto p-6 bg-white shadow-md rounded">
            <input type="hidden" name="blog_id" value="<?php echo $blog_id; ?>">
            <div class="w-full mb-4">
                <label for="blog_title" class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                <input type="text" id="blog_title" name="blog_title" value="<?= $blog_title ?>" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600" required autofocus>
            </div>
            <div class="w-full mb-4">
                <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Content</label>
                <textarea cols="10" rows="5" id="content" name="blog_content" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600" required><?= $blog_content ?></textarea>
            </div>
            <div class="w-full mb-4">
                <label for="thumbnail" class="block text-gray-700 text-sm font-bold mb-2">Blog Thumbnail</label>
                <input type="file" id="blog_thumbnail" name="blog_thumbnail" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600">
            </div>
            <div class="flex items-center justify-center">
                <button type="submit" class="mt-4 bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md">Update Blog</button>
            </div>
        </form>
    </main>
</body>

</html>