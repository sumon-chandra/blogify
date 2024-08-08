<?php
require_once "includes/config.session.php";
require_once "includes/blog/blog.inc.php";
require_once "includes/blog/view.inc.php";

$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";
$isLoggedId = $user_id;

if (!$isLoggedId) {
    header("Location: login.php");
    exit();
}
// Search for blogs
$search_query = isset($_GET["s"]) ? $_GET["s"] : "";

$blogObject = new Blog();

// Get Blogs, Authors, Tags
$blogs = $blogObject->getBlogsById($user_id);
$author = $blogObject->getAuthor($user_id); // TODO: This should be User
$tags = $blogObject->getTags();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $author["author_name"] ?> - Blogify</title>
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
                    <li class="mx-4"><a href="profile.php" class="text-white hover:text-gray-400">Profile</a></li>
                    <li class="mx-4"><a href="includes/login/logout.inc.php" class="text-white hover:text-gray-400">Logout</a></li>
                <?php else : ?>
                    <li class="mx-4"><a href="login.php" class="text-white hover:text-gray-400">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="p-4 lg:p-0 lg:w-[1250px] mx-auto">
        <div class="mt-10 flex items-start justify-between">
            <h2 class="text-xl font-black text-gray-600">Welcome, <span class="text-5xl text-gray-800"><?= $author["author_name"] ?></span></h2>
            <a href="update-profile.php?id=<?= $user_id ?>" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md">Edit profile</a>
        </div>
        <div class="py-8 flex items-center justify-between">
            <h1 class="text-2xl font-bold">Your blogs</h1>
            <!-- Filter and search -->
            <div class="flex justify-end gap-4">
                <form action="" method="get" enctype="multipart/form-data" class="flex bg-white rounded-md">
                    <input type="text" name="s" value="<?= $search_query ?>" <?= $search_query ? "autofocus" : "" ?> placeholder="Filter by title..." class="w-full px-4 py-2 rounded-md focus:outline-none focus:ring-primary-500" />
                    <button type="submit" class="bg-primary-500 px-4 py-2 font-bold rounded-md ml-4">Search</button>
                </form>
                <div class="flex justify-end gap-4">
                    <button class="hidden bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md">Filter</button>
                    <a href="create-blog.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-md">Create New Blog</a>
                </div>
            </div>
        </div>
        <div>
        </div>
        <div class="grid gap-4 grid-cols-1 md:grid-cols-4">
            <?php if ($blogs) { ?>
                <div class="md:col-span-3 grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
                    <?php foreach ($blogs as $blog) : ?>
                        <div class="p-4 space-y-4 rounded-md bg-white text-gray-800 shadow-md h-96 flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="h-40">
                                    <img src="<?= displayThumbnail($blog["thumbnail"]) ?>" alt="blog image" class="object-cover w-full h-full">
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold">
                                        <a href="blog.php?blog_id=<?= $blog["blog_id"] ?>">
                                            <?= strlen($blog["title"]) <= 50 ? $blog["title"] : substr($blog["title"], 0, 50) . " ..." ?>
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm">
                                        <?= strlen($blog["content"]) <= 120 ? $blog["content"] : substr($blog["content"], 0, 120) . " ..." ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right mt-3 flex flex-col">
                                <!-- Display blog tags -->
                                <div class="flex items-center flex-wrap">
                                    <?php if (isset($blog["tags"])) : ?>
                                        <?php foreach (explode(",", $blog["tags"]) as $tag) : ?>
                                            <a href="blogs.php?tag=<?= $tag ?>">
                                                <strong class="inline-block text-gray-500 px-2 py-1 text-xs cursor-pointer">#<?= $tag ?></strong>
                                            </a>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p>
                                        <small class="font-semibold"><?= blogDate($blog["created_at"]) ?></small>
                                    </p>

                                    <p>
                                        <a href="blog.php?blog_id=<?= $blog["blog_id"] ?>" class="w-full text-gray-800 font-semibold">View Details</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php } else { ?>
                <div>
                    <h3>There is no blogs!</h3>
                </div>
            <?php } ?>
        </div>
    </main>
</body>

</html>