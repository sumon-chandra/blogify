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
$comments = $blogObject->getComments($blog_id, "");
$total_likes = $blogObject->getTotalLikes($blog_id, $user_id);

$tags = explode(",", $blog['tags']);
$related_blogs = [];

// Get related blogs based on tags
if ($tags) {
    foreach ($tags as $tag) {
        $related_blogs_array = $blogObject->relatedBlogs($tag, $blog['blog_id']);
        if ($related_blogs_array) {
            foreach ($related_blogs_array as $related_blog) {
                array_push($related_blogs, $related_blog);
            }
        }
    }
}

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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Insert like 
            $("#like-btn").click(function() {
                let blog_id = $(this).data("blog_id");
                let user_id = $(this).data("user_id");

                $.ajax({
                    url: "handle-likes.php",
                    type: "POST",
                    data: {
                        blog_id,
                        user_id
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        $('#like-btn').attr('disabled', 'disabled');
                        $('#likes').text(response?.data?.total_likes)
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            });

            // Sort comments by date
            $('#sort_by').change(function(event) {
                let sort_by = event.target.value;
                let blog_id = $('#sort_by').data('blog_id');
                console.log({
                    sort_by,
                    blog_id
                });

                $.ajax({
                    url: "handle-comments.php",
                    method: 'POST',
                    data: {
                        blog_id,
                        sort_by
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log(data);

                        if (data.status == 'success') {
                            $('#comments').html(data.comments);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            })
        });
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
                <p class="text-gray-600">Created by <strong><a href="user.php?user_id=<?= $blog["author_id"] ?>"><?= $blog["author_name"]; ?></a></strong></p>
            </div>
            <div>
                <p><strong><?= $blog["total_views"] ?></strong> People reads the blog.</p>
            </div>
            <div>
                <?php if ($total_likes["has_liked"]) : ?>
                    <?= displayLikes($total_likes) ?>
                <?php else : ?>
                    <button id="like-btn" data-blog_id="<?= $blog_id ?>" data-user_id="<?= $user_id ?>" class="bg-gray-800 text-white px-4 py-1 rounded-md hover:bg-gray-700 disabled:bg-gray-700">Like <strong id="likes"><?= $total_likes["total_likes"] ?></strong></button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comment Section -->
        <section>
            <?php if (!empty($user_id)) : ?>
                <h3 class="text-lg font-bold">Leave a comment</h3>
                <div class="mt-5">
                    <form action="includes/blog/comments.inc.php" method="post" enctype="multipart/form-data" class="flex flex-col items-end justify-center">
                        <input type="hidden" name="user_id" value="<?= $user_id ?>">
                        <input type="hidden" name="blog_id" value="<?= $blog_id ?>">
                        <textarea name="comment" id="comment" cols="30" rows="2" placeholder="Write a comment" class="p-4 bg-white rounded-sm border w-full"></textarea>
                        <button class="bg-gray-800 text-white px-4 py-1 rounded-md hover:bg-gray-700 mt-4">Add Comment</button>
                    </form>
                </div>
            <?php endif ?>
            <?php if (!empty($comments)) : ?>
                <div class="flex items-end justify-between">
                    <h3 class="text-lg font-bold">Comments</h3>
                    <div>
                        <select name="sort_by" id="sort_by" data-blog_id="<?= $blog_id ?>" class="bg-gray-800 text-white px-4 py-1 rounded-md hover:bg-gray-700 mt-4">
                            <option value="">Sort By</option>
                            <option value="new">New</option>
                            <option value="old">Old</option>
                        </select>
                    </div>
                </div>

                <!-- Comment List -->
                <div id="comments" class="mt-4 p-3 bg-white">
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
        </section>

        <!-- Related blogs Section -->
        <?php if ($related_blogs) : ?>
            <section class="mt-5 space-y-5">
                <div class="w-full h-px bg-gray-400"></div>
                <h3 class="text-lg font-bold">You may also like these blogs</h3>
                <div class="grid gap-4 grid-cols-1 md:grid-cols-2">
                    <?php foreach ($related_blogs as $blog) : ?>
                        <a href="blog.php?blog_id=<?= $blog["blog_id"] ?>">
                            <div class="p-4 rounded-md bg-white text-gray-800 shadow-md h-auto flex justify-between">
                                <div class="flex items-start gap-4">
                                    <div class="size-16">
                                        <img src="<?= displayThumbnail($blog["thumbnail"]) ?>" alt="blog image" class="object-cover w-full h-auto">
                                    </div>
                                    <h3 class="text-sm font-semibold">
                                        <?= strlen($blog["title"]) <= 30 ? $blog["title"] : substr($blog["title"], 0, 30) . " ..." ?>
                                    </h3>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
</body>

</html>