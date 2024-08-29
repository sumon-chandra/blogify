<?php
require_once "includes/config.session.php";
require_once "includes/blog/blog.inc.php";
require_once "includes/user/user.inc.php";

$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";
$isLoggedId = $user_id;

$blog_tag = isset($_GET["tag"]) ? $_GET["tag"] : "";
$author_id  = isset($_GET["author_id"]) ? $_GET["author_id"] : "";
$sort_by = isset($_GET["sort_by"]) ? $_GET["sort_by"] : "";
$start_date = isset($_GET["start_date"]) ? $_GET["start_date"] : "";
$end_date = isset($_GET["end_date"]) ? $_GET["end_date"] : "";
// Search for blogs
$search_query = isset($_GET["s"]) ? $_GET["s"] : "";


$blogObject = new Blog();

// Get Blogs, Authors, Tags
$offset = isset($_GET["offset"]) ? intval($_GET["offset"]) : "";
$blogs = $blogObject->getBlogs($blog_tag, $author_id, $sort_by, $start_date, $end_date, $search_query, $offset);
$authors = $blogObject->getAuthors();
$tags = $blogObject->getTags();
$total_blogs = $blogObject->totalBlogs();

$userModel = new User();
$user_role = $isLoggedId ? $userModel->userRole($user_id) : "";
$admin = $user_role == "Admin" ? "Admin" : "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs - Blogify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <!-- <script src="js/load-more-blogs.js"></script> -->
    <script>
        $(document).ready(function() {
            $(document).on('click', '#btn-more', function() {
                let last_blog_id = $(this).data('last_blog_id');
                console.log({
                    last_blog_id
                });

                $('#btn-more').text('Loading....');
                $.ajax({
                    url: "more-blogs.php",
                    method: 'POST',
                    data: {
                        last_blog_id
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data.success) {
                            $('#blogs').append(data.data);
                            $('#load-more-div').html(data.load_more_btn);
                            $('#btn-more').text('Load More');
                        } else {
                            $('#btn-more').remove();
                            $('#load-more-div').text(data.message)
                        }
                    },
                    error: function(e) {
                        $('#failed-message').removeClass('hidden');
                        $('#failed-message').addClass('block');
                        $('#failed-message').text("Something went wrong. Please try again!", );
                    }
                })
            })
        })
    </script>
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
    <main class="p-4 lg:p-0 lg:w-[1250px] mx-auto">
        <div class="py-8 flex items-center justify-between">
            <h1 class="text-2xl font-bold">All blogs</h1>
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

        <!-- Blog List -->
        <div class="grid gap-4 grid-cols-1 md:grid-cols-4 mb-10">
            <?php if ($blogs) { ?>
                <?php $last_blog_id = ""; ?>
                <div class="md:col-span-3 space-y-4">
                    <div id="blogs" class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
                        <?php foreach ($blogs as $blog) : ?>
                            <div class="p-4 space-y-4 group rounded-md bg-white text-gray-800 shadow-md h-[20rem] flex flex-col justify-between">
                                <div class="space-y-2">
                                    <div class="h-40 overflow-hidden">
                                        <a href="blog.php?blog_id=<?= $blog["blog_id"] ?>">
                                            <img src="<?= displayThumbnail($blog["thumbnail"]) ?>" alt="blog image" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300">
                                        </a>
                                    </div>
                                    <div>
                                        <h3 class="text-sm leading-tight font-semibold">
                                            <a href="blog.php?blog_id=<?= $blog["blog_id"] ?>">
                                                <?= strlen($blog["title"]) <= 50 ? $blog["title"] : substr($blog["title"], 0, 50) . " ..." ?>
                                            </a>
                                        </h3>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center flex-wrap">
                                        <?php if (isset($blog["tags"])) : ?>
                                            <?php foreach (explode(",", $blog["tags"]) as $tag) : ?>
                                                <a href="blogs.php?tag=<?= $tag ?>">
                                                    <strong class="inline-block text-gray-500 px-2 py-1 text-xs cursor-pointer">#<?= $tag ?></strong>
                                                </a>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </div>
                                    <div class="text-xs">
                                        <p>
                                            <strong>
                                                <a href="user.php?user_id=<?= $blog["author_id"] ?>" class="w-full text-gray-800 font-semibold"> <?= $blog["author_name"] ?></a>
                                            </strong>
                                        </p>
                                        <p class=""><?= blogDate($blog["created_at"]) ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php $last_blog_id = $blog["blog_id"]; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($total_blogs > 3) : ?>
                        <div id="load-more-div" class="flex items-center justify-center flex-col mt-4">
                            <!-- Failed Message -->
                            <div id="failed-message" class="text-center text-sm font-semibold"></div>
                            <button id="btn-more" data-last_blog_id="<?= $last_blog_id ?>" class="bg-gray-800 hover:bg-gray-700 text-white font-semibold px-4 py-2 rounded-md w-52">Load More</button>
                        </div>
                    <?php endif; ?>
                </div>

            <?php } else { ?>
                <div class="md:col-span-3 grid place-items-center">
                    <h3 class="text-xl font-bold text-gray-700">No blog found!</h3>
                </div>
            <?php } ?>
            <div class="md:col-span-1">
                <div class="p-4 space-y-4 rounded-md bg-white text-gray-800 shadow-md">

                    <h3 class="text-center text-lg font-semibold border-b">Filter</h3>
                    <form action="blogs.php" method="get" class="flex flex-col gap-4">
                        <div class="flex gap-2 flex-col">
                            <label for="tag" class="font-semibold">Tag:</label>
                            <select name="tag" id="tag" class="w-full p-1.5 rounded-md bg-gray-200">
                                <option value="">Select</option>
                                <?php foreach ($tags as $tag) : ?>
                                    <option value="<?= $tag["tag_name"]; ?>" <?= ($blog_tag == $tag["tag_name"]) ?  "selected" : "" ?>># <a href="blogs.php?<?= $tag["tag_name"] ?>"><?= $tag["tag_name"]; ?></a></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="flex gap-2 flex-col">
                            <label for="author" class="font-semibold">Author:</label>
                            <select name="author_id" id="author" class="w-full p-1.5 rounded-md bg-gray-200">
                                <option value="">Select</option>
                                <?php foreach ($authors as $author) : ?>
                                    <option value="<?= $author["author_id"]; ?>" <?= ($author_id == $author["author_id"]) ?  "selected" : "" ?>>
                                        <?= $author["author_name"]; ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="flex gap-2 flex-col">
                            <label for="sort_by" class="font-semibold">Sort by:</label>
                            <select name="sort_by" id="sort_by" class="w-full p-1.5 rounded-md bg-gray-200">
                                <option value="">Select</option>
                                <option value="newly_created" <?= $sort_by == "newly_created" ? "selected" : "" ?>>New</option>
                                <option value="old_created" <?= $sort_by == "old_created" ? "selected" : "" ?>>Old</option>
                                <option value="most_likes" <?= $sort_by == "most_likes" ? "selected" : "" ?>>Most Likes</option>
                            </select>
                        </div>
                        <div class="flex gap-2 flex-col">
                            <h3 for="between_dates" class="font-semibold">Between Dates:</h3>
                            <label for="start_date" class="text-xs">From</label>
                            <input type="date" value="<?= $start_date ?>" name="start_date" id="start_date" class="w-full p-1.5 rounded-md bg-gray-200" />
                            <label for="end_date" class="text-xs">To</label>
                            <input type="date" value="<?= $end_date ?>" name="end_date" id="end_date" class="w-full p-1.5 rounded-md bg-gray-200" />
                        </div>
                        <div class="mt-4">
                            <button class="bg-gray-800 hover:bg-gray-700 text-white font-semibold px-4 py-2 rounded-md w-full">Go</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>

</html>