<?php
require_once "includes/config.session.php";
require_once "includes/blog/blog.inc.php";
require_once "includes/blog/view.inc.php";
require_once "includes/user/user.inc.php";

$user_session_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";
$isLoggedId = $user_session_id;
$user_id = isset($_GET["user_id"]) ? $_GET["user_id"] : "";

$blogModel = new Blog();
$userModel = new User();

// User Role and Admin Privileges
$user_role = $userModel->userRole($user_id);
$admin = $user_role == "Admin" ? "Admin" : "";

// Get Blogs, Authors, Tags
$approved_blogs = $blogModel->getApprovedBlogsById($user_id);
$user = $userModel->getUserById($user_id);
$tags = $blogModel->getTags();

$isPostedYet = empty($approved_blogs);
$user_name = $user["first_name"] . " " . $user["last_name"];

// Avatar
$avatar = "assets/user.png";
if (!empty($user["avatar"])) {
    $avatar = "uploads/avatars/" . $user["avatar"];
}

// Gender
$gender = "";
if ($user["gender"] == "Male") {
    $gender = "His";
} else if ($user["gender"] == "Female") {
    $gender = "Her";
} else {
    $gender = "It's";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $user_name ?> - Blogify</title>
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
                        <img src="<?= $avatar ?>" alt="Avatar" class="size-48 rounded-full">
                    </div>
                    <div class="flex items-end justify-start gap-2">
                        <h2 class="text-3xl font-black text-gray-800"><?= $user_name ?></h2>
                        <p class="text-sm font-semibold">(<?= $user["role"] ?>)</p>
                    </div>
                </div>
                <div class="">
                    <div class="bg-white rounded-sm p-4 mt-4 space-y-2 shadow-md">
                        <h3 class="text-xl font-bold text-gray-800 border-b border-gray-800 text-center pb-1">
                            <?= $gender ?> Blogs
                        </h3>
                        <div>
                            <p class="text-gray-500">Total Blogs: <strong class="text-gray-800"><?= count($approved_blogs) ?></strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:w-[1250px] mx-auto">
            <?php if ($isPostedYet) : ?>
                <div class="py-8 text-center text-gray-500 mt-10">
                    <h1 class="text-xl font-bold">Haven't posted any blogs yet.</h1>
                </div>
        </div>
    <?php else : ?>
        <div class="py-8 flex items-center justify-between">
            <h1 class="text-2xl font-bold"><?= $gender ?> blogs</h1>
        </div>
        <div class="">
            <?php if ($approved_blogs) { ?>
                <div class="">
                    <?php foreach ($approved_blogs as $blog) : ?>
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
                                                <div class="flex items-center justify-start gap-3">
                                                    <p href="#" class="text-gray-500 text-left py-1 rounded-md font-semibold cursor-pointer">Like <strong class="text-gray-800">10</strong></p>
                                                    <p href="#" class="text-gray-500 text-left py-1 rounded-md font-semibold cursor-pointer">Comment <strong class="text-gray-800">6</strong></p>
                                                    <p href="#" class="text-gray-500 text-left py-1 rounded-md font-semibold cursor-pointer">Share <strong class="text-gray-800">2</strong></p>
                                                </div>
                                                <p>
                                                    <small class="font-semibold"><?= blogDate($blog["created_at"]) ?></small>
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
    <?php endif; ?>
    </div>
    </main>
</body>

</html>