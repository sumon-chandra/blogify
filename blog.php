<?php
require_once "includes/config.session.php";
require_once "includes/blog/blog.inc.php";
require_once "includes/blog/view.inc.php";
require_once "includes/user/user.inc.php";

$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";
$blog_id = isset($_GET["blog_id"]) ? $_GET["blog_id"] : "";
$isLoggedId = $user_id;

$blogObject = new Blog();
$userObject = new User();
$blog = $blogObject->getBlogById($blog_id);
$comments = $blogObject->getComments($blog_id);

$thumbnail = "assets/dummy.jpg";
$dateTime = new DateTime($blog["created_at"]);
$formattedDate = $dateTime->format("F d Y");

$user_role =  $isLoggedId ? $userObject->userRole($user_id) : "";
$admin = $user_role == "Admin" ? "Admin" : "";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $blog["title"] ?> - Blogify</title>
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
    <main class="p-4 mb-10 lg:p-0 lg:w-[950px] mx-auto">
        <div class="py-8 text-center">
            <h1 class="text-3xl font-bold"><?= $blog["title"]; ?></h1>
        </div>
        <div>
            <div class="w-full lg:h-72">
                <img class="w-full h-full object-contain" src="<?= displayThumbnail($blog["thumbnail"]) ?>" alt="Blog thumbnail">
            </div>
            <div class="mt-10 flex items-center justify-between">
                <div class="flex items-center justify-start gap-3">
                    <?php foreach (explode(",", $blog["tags"]) as $tag) : ?>
                        <?php if ($tag) : ?>
                            <?php $blogTag = str_replace(" ", "", $tag) ?>
                            <strong class="inline-block text-gray-700 px-2 py-1 text-xs cursor-pointer">
                                <a href="blogs.php?tag=<?= $blogTag; ?>">#<?= $blogTag; ?></a>
                            </strong>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <!-- End of tags loop -->
                </div>
                <p class="text-gray-600 text-right">Published at <strong><?= $formattedDate; ?></strong></p>
            </div>
            <div class="my-10">
                <p class="lg:text-xl text-lg whitespace-pre-wrap"><?= $blog["content"]; ?></p>
            </div>
        </div>
        <div class="flex items-center justify-between py-8">
            <div>
                <p class="text-gray-600">Created by <strong><?= $blog["author_name"]; ?></strong></p>
            </div>
            <div>
                <p><strong><?= $blog["total_views"] ?></strong> People reads the blog.</p>
            </div>
            <div>
                <a href="#" class="bg-gray-800 text-white px-4 py-1 rounded-md hover:bg-gray-700">Like <strong><?= $blog["total_likes"] ?></strong></a>
                <a href="#" class="bg-gray-800 text-white px-4 py-1 rounded-md hover:bg-gray-700">Share</a>
            </div>
        </div>

        <!-- Comment Section -->
        <div>
            <h3 class="text-lg font-bold">Leave a comment</h3>
            <div class="mt-5">
                <form action="includes/blog/comments.inc.php" method="post" enctype="multipart/form-data" class="flex flex-col items-end justify-center">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <input type="hidden" name="blog_id" value="<?= $blog_id ?>">
                    <textarea name="comment" id="comment" cols="30" rows="2" placeholder="Write a comment" class="p-4 bg-white rounded-sm border w-full"></textarea>
                    <button class="bg-gray-800 text-white px-4 py-1 rounded-md hover:bg-gray-700 mt-4">Add Comment</button>
                </form>
            </div>
            <!-- Comment List -->
            <?php if (!empty($comments)) : ?>
                <h3 class="text-lg font-bold">Comments</h3>
                <!-- <div>
                    <form action="includes/blog/comments.inc.php" method="get">
                        <select name="sort_by" id="sort_by" class="bg-gray-800 text-white px-4 py-1 rounded-md hover:bg-gray-700 mt-4">
                            <option value="">Sort By</option>
                            <option value="new">New</option>
                            <option value="old">Old</option>
                        </select>
                    </form>
                </div> -->
                <div class="mt-4 p-3 bg-white">
                    <?php foreach ($comments as $comment) : ?>
                        <div class="p-4 mt-2 border border-gray-300 text-gray-800">
                            <div class="flex items-center justify-start gap-2">
                                <div>
                                    <a href="user.php?user_id=<?= $comment["user_id"] ?>">
                                        <?php if (empty($comment["author_avatar"])) : ?>
                                            <img src="assets/user.png" alt="Commented User" class="size-10 rounded-full">
                                        <?php else : ?>
                                            <img src="uploads/avatars/<?= $comment["author_avatar"] ?>" alt="Commented User" class="size-10 rounded-full">
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold"><?= $comment["comment_author"] ?></h3>
                                    <p class="text-xs"><?= $comment["created_at"] ?></p>
                                </div>
                            </div>
                            <div class="mt-2 text">
                                <p><?= $comment["comment_text"] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>