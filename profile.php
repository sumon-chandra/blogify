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
// Search for blogs
$search_query = isset($_GET["s"]) ? $_GET["s"] : "";
$userId = isset($_GET["user_id"]) ? $_GET["user_id"] : "";


$blogObject = new Blog();

$userModel = new User();
$user_role = $userModel->userRole($user_session_id);
$admin = $user_role == "Admin" ? "Admin" : "";

$user_id = $userId ? $userId : $user_session_id;

// Get Blogs, Authors, Tags
$approved_blogs = $blogObject->getApprovedBlogsById($user_id);
$denied_blogs = $blogObject->getDeniedBlogsById($user_id);
$pending_blogs = $blogObject->getBlogsByStatusAndUser($user_id, "1");
$user = $userModel->getUserById($user_id);
$tags = $blogObject->getTags();

$isPostedYet = empty($approved_blogs) && empty($denied_blogs);
$user_name = $user["first_name"] . " " . $user["last_name"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $user["first_name"] ?> - Blogify</title>
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
    <main class="p-4 lg:p-0">
        <div class="py-10 bg-gradient-to-br from-gray-50 via-gray-200 to-gray-50">
            <div class="lg:w-[1250px] mx-auto flex items-start justify-between">
                <div class="space-y-5">
                    <div>
                        <?php if (isset($user["avatar"])) : ?>
                            <img src="uploads/avatars/<?= $user["avatar"] ?>" alt="Avatar" class="size-48 rounded-full">
                        <?php else : ?>
                            <img src="assets/dummy.jpg" alt="Avatar" class="size-48 rounded-full">
                        <?php endif; ?>
                    </div>
                    <div class="flex items-end justify-start gap-2">
                        <h2 class="text-3xl font-black text-gray-800"><?= $user_name ?></h2>
                        <p class="text-sm font-semibold">(<?= $user["role"] ?>)</p>
                    </div>
                </div>
                <div class="">
                    <p class="text-right">
                        <a href="update-profile.php?id=<?= $user_id ?>" class="text-gray-800 hover:text-gray-700 font-bold">Edit profile</a>
                    </p>
                    <div class="bg-white rounded-sm p-4 mt-4 space-y-2 shadow-md">
                        <h3 class="text-xl font-bold text-gray-800 border-b border-gray-800 text-center pb-1">Your Blogs</h3>
                        <div>
                            <p class="text-gray-500">Total Blogs: <strong class="text-gray-800"><?= count($approved_blogs) + count($denied_blogs) + count($pending_blogs) ?></strong></p>
                            <p class="text-gray-500">Approved Blogs: <strong class="text-gray-800"><?= count($approved_blogs) ?></strong></p>
                            <p class="text-gray-500">Pending Blogs: <strong class="text-gray-800"><?= count($pending_blogs) ?></strong></p>
                            <p class="text-gray-500">Denied Blogs: <strong class="text-gray-800"><?= count($denied_blogs) ?></strong></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="#" class="text-gray-800 hover:text-gray-700 font-bold hover:underline">View All</a> |
                            <a href="users-blogs.php?user_id=<?= $user_id ?>&status=pending" class="text-gray-800 hover:text-gray-700 font-bold hover:underline">Pending</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:w-[1250px] mx-auto">
            <?php if ($isPostedYet) : ?>
                <div class="py-8 text-center text-gray-500 mt-10">
                    <h1 class="text-xl font-bold">You haven't posted any blogs yet.</h1>
                    <p>Click the "Create New Blog" button to start writing your first blog.</p>
                    <div class="mt-7">
                        <a href="create-blog.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md">Create New Blog</a>
                    </div>
                </div>
        </div>
    <?php else : ?>
        <div class="py-8 flex items-center justify-between">
            <h1 class="text-2xl font-bold">Your Approved blogs</h1>
            <div class="flex justify-end gap-4">
                <a href="create-blog.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md">Create New Blog</a>
            </div>
        </div>
        <div class="">
            <?php if ($approved_blogs) { ?>
                <div class="space-y-6">
                    <?php foreach ($approved_blogs as $blog) : ?>
                        <div class="p-4 rounded-md bg-white text-gray-800 shadow-md h-auto flex justify-between">
                            <div class="flex items-start gap-4">
                                <div class="w-32 pt-[9px]">
                                    <img src="<?= displayThumbnail($blog["thumbnail"]) ?>" alt="blog image" class="object-contain h-auto w-full">
                                </div>
                                <div class="flex-1 flex flex-col justify-between">
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
                                                <div class="flex items-center justify-start gap-3">
                                                    <p class="text-gray-500 text-left py-1 rounded-md font-semibold">Like <strong class="text-gray-800"><?= $blog['total_likes'] ?></strong></p>
                                                    <p class="text-gray-500 text-left py-1 rounded-md font-semibold">Comment <strong class="text-gray-800"><?= $blog['total_comments'] ?></strong></p>
                                                </div>
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
            <?php } else { ?>
                <div class="grid place-items-center">
                    <h3 class="text-xl font-bold text-gray-500">You haven't any approved blogs!</h3>
                </div>
            <?php } ?>
        </div>

        <div class="my-10">
            <h1 class="text-2xl font-bold text-red-600">Your Denied blogs</h1>
            <div class="mt-10">
                <?php if (empty($denied_blogs)) : ?>
                    <div class="grid place-items-center">
                        <h3 class="text-xl font-bold text-gray-500">You haven't any denied blogs!</h3>
                    </div>
                <?php else : ?>
                    <?php foreach ($denied_blogs as $blog) : ?>
                        <div class="p-4 rounded-md bg-white text-gray-800 shadow-md h-auto flex justify-between">
                            <div class="flex gap-4">
                                <div class="h-40">
                                    <img src="<?= displayThumbnail($blog["thumbnail"]) ?>" alt="blog image" class="object-cover w-full h-full">
                                </div>
                                <div class="flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold">
                                            <a href="users-blogs.php?user_id=<?= $user_id ?>&status=denied">
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
                                                <div class="flex items-center justify-start gap-3">
                                                    <p href="#" class="text-gray-700 text-left py-1 rounded-md font-semibold cursor-pointer">Like <strong>10</strong></p>
                                                    <p href="#" class="text-gray-700 text-left py-1 rounded-md font-semibold cursor-pointer">Comment <strong>6</strong></p>
                                                    <p href="#" class="text-gray-700 text-left py-1 rounded-md font-semibold cursor-pointer">Share <strong>2</strong></p>
                                                </div>
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
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    </div>
    </main>
</body>

</html>