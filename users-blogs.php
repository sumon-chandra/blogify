<?php
require_once "includes/config.session.php";
require_once "includes/blog/blog.inc.php";
require_once "includes/blog/view.inc.php";
require_once "includes/user/user.inc.php";

$user_session_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";
$isLoggedId = $user_session_id;

if (!$isLoggedId) {
    header("Location: login.php");
    exit();
}
$user_id = isset($_GET["user_id"]) ? $_GET["user_id"] : "";
$status = isset($_GET["status"]) ? $_GET["status"] : "";
$status_id = "";

if ($status == "pending") {
    $status_id = 1;
} else if ($status == "denied") {
    $status_id = 2;
}

$blogObject = new Blog();
$userModel = new User();

$user_role = $userModel->userRole($user_session_id);
$admin = $user_role == "Admin" ? "Admin" : "";

// Get Blogs, Authors, Tags
$blogs = $blogObject->getBlogsByStatusAndUser($user_id, $status_id);



$user = $userModel->getUserById($user_id);
$tags = $blogObject->getTags();

$user_name = $user["first_name"] . " " . $user["last_name"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $user["first_name"] ?>'s Pending Blogs - Blogify</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen w-full bg-gray-200">
    <header class="bg-gray-800">
        <nav class="flex items-center justify-between p-4 lg:w-[1250px] mx-auto">
            <h1 class="text-2xl font-bold text-white"><a href="index.php">Blogify</a>
            </h1>
            <ul class="flex">
                <li class="mx-4"><a href="index.php" class="text-white hover:text-gray-400">Home</a></li>
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
    <main class="p-4 lg:p-0 lg:w-[1250px] mx-auto space-y-10">
        <div class="mt-10">
            <h3 class="text-2xl"><?= ucfirst($status) ?> Blogs for <strong><?= $user_name ?></strong></h3>
        </div>
        <?php if ($blogs) : ?>
            <div class="space-y-4">
                <?php foreach ($blogs as $blog) : ?>
                    <div class="p-4 rounded-md bg-white text-gray-800 shadow-md h-auto flex justify-between">
                        <div class="flex gap-4">
                            <div class="h-40">
                                <img src="<?= displayThumbnail($blog["thumbnail"]) ?>" alt="blog image" class="object-cover w-full h-full">
                            </div>
                            <div class="flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold">
                                        <a href="blog.php?blog_id=<?= $blog["blog_id"] ?>">
                                            <?= strlen($blog["title"]) <= 50 ? $blog["title"] : substr($blog["title"], 0, 50) . " ..." ?>
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm">
                                        <?= strlen($blog["content"]) <= 220 ? $blog["content"] : substr($blog["content"], 0, 220) . " ..." ?>
                                    </p>
                                </div>
                                <div class="mt-3 flex flex-col">
                                    <div class="flex items-center flex-wrap">
                                        <?php if (isset($blog["tags"])) : ?>
                                            <?php foreach (explode(",", $blog["tags"]) as $tag) : ?>
                                                <a href="blogs.php?tag=<?= $tag ?>">
                                                    <strong class="inline-block text-gray-500 px-2 py-1 text-xs cursor-pointer">#<?= $tag ?></strong>
                                                </a>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </div>
                                    <div class="grid grid-cols-4">
                                        <div class="col-span-3 flex items-center justify-start gap-7">
                                            <p>
                                                <small class="font-semibold"><?= blogDate($blog["created_at"]) ?></small>
                                            </p>
                                        </div>
                                        <div class="col-span-1 flex items-center justify-end gap-4 text-xs mt-2">
                                            <p>
                                                <a href="update-blog.php?id=<?= $blog["blog_id"] ?>" class="w-full text-gray-700 text-left py-1 rounded-md font-semibold underline">Edit Blog</a>
                                            </p>
                                            <p>
                                                <a href="includes/blog/delete-blog.inc.php?blog_id=<?= $blog["blog_id"] ?>" class="w-full text-red-600 text-right py-1 rounded-md font-semibold underline">Delete Blog</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else : ?>
            <div class="grid place-items-center">
                <div class="py-8 text-center text-gray-500 mt-10">
                    <h1 class="text-xl font-bold">You haven't any pending blogs.</h1>
                    <p>Click the "Create New Blog" button to start writing a blog.</p>
                    <div class="mt-7">
                        <a href="create-blog.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md">Create New Blog</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>