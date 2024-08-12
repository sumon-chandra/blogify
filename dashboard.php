<?php
require_once "includes/config.session.php";
require_once "includes/user/user.inc.php";
require_once "includes/blog/blog.inc.php";
$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";
$isLoggedId = $user_id;


$userModel = new User();
$blogModel = new Blog();

$user_role = $userModel->userRole($user_id);
$admin = $user_role == "Admin" ? "Admin" : "";
if (!$isLoggedId || !$user_role) {
    header("Location: login.php");
    die();
}

$user = $userModel->getUserById($user_id);
$totalBlogs = $blogModel->totalBlogs();
$totalAuthors = $blogModel->totalAuthors();
$pendingBlogs = $blogModel->pendingBlogs();
$pending_blogs = $blogModel->getPendingBlogs();
$authors = $blogModel->getAuthors();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Blogify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function confirmation(status) {
            var result = confirm("Are you sure you want to change the blog status to " + status + "?");
            if (result) {
                return true;
            } else {
                return false;
            }
        }
    </script>
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
        <section class="my-7">
            <h2 class="text-xl text-gray-700 font-bold">Hi <span class="text-5xl font-black bg-gradient-to-r from-red-600 via-orange-600 to-yellow-600 text-transparent bg-clip-text"> <?= $user["first_name"] ?></span>, welcome to dashboard!</h2>
            <div class="flex items-center justify-between gap-10 mt-5 select-none">
                <div class="w-full text-center text-gray-200 bg-purple-600 rounded-lg p-6 cursor-pointer">
                    <h3 class="text-lg font-bold">Total Approved Blogs</h3>
                    <h2 class="text-7xl font-black"><?= $totalBlogs ?></h2>
                </div>
                <div class="w-full text-center text-gray-200 bg-orange-600 rounded-lg p-6 cursor-pointer">
                    <h3 class="text-lg font-bold">Total Authors</h3>
                    <h2 class="text-7xl font-black"><?= $totalAuthors ?></h2>
                </div>
                <div class="w-full text-center text-gray-200 bg-teal-600 rounded-lg p-6 cursor-pointer">
                    <h3 class="text-lg font-bold">Pending Blogs</h3>
                    <h2 class="text-7xl font-black"><?= $pendingBlogs ?></h2>
                </div>
            </div>
        </section>
        <!-- Table section for display pending blogs -->
        <section class="my-10">
            <h2 class="text-xl text-gray-700 font-bold">
                <a href="#pending_blogs">Pending Blogs <span class="hidden hover:inline"> #</span></a>
            </h2>
            <table class="w-full text-left text-gray-800 border-collapse border-b table-auto bg-white">
                <caption class="caption-top mb-4">Total pending blogs. Admin either will <strong>Approve</strong> or <strong>Deny</strong> the blogs.</caption>
                <thead class="border border-black">
                    <tr>
                        <th class="px-4 py-3 text-lg font-bold text-gray-100 bg-gray-400">#</th>
                        <th class="px-4 py-3 text-lg font-bold text-gray-100 bg-gray-400">Title</th>
                        <th class="px-4 py-3 text-lg font-bold text-gray-100 bg-gray-400">Author</th>
                        <th class="px-4 py-3 text-lg font-bold text-gray-100 bg-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody class="border border-black">
                    <?php
                    $row_number = 1;
                    foreach ($pending_blogs as $blog) : ?>
                        <tr>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 border-b border-black"> <?= $row_number++ ?></td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 border-b border-black"> <?= $blog["title"] ?></td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 border-b border-black">
                                <a href="profile.php?user_id=<?= $blog["author_id"] ?>"><?= $blog["author_name"] ?></a>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 border-b border-black space-x-4">
                                <button onclick="confirmation('Approve')" class="text-blue-600 hover:text-blue-400">
                                    <a href="includes/blog/blog-status-handle.inc.php?status=approve&blog_id=<?= $blog["blog_id"] ?>">Approve</a>
                                </button>
                                <button onclick="confirmation('Deny')" class="text-red-600 hover:text-red-400">
                                    <a href="includes/blog/blog-status-handle.inc.php?status=deny&blog_id=<?= $blog["blog_id"] ?>">Deny</a>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </section>

        <!-- Table section for displaying total authors -->
        <section class="my-10">
            <h3 class="text-xl text-gray-700 font-bold">
                <a href="#authors">Total Authors <span class="hidden hover:inline"> #</span></a>
            </h3>
            <table class="w-full text-left text-gray-800 border-collapse border-b table-auto bg-white">
                <caption class="caption-top mb-4">
                    Total authors. This list is represent all <strong>Authors</strong> without <strong>Admins</strong> who have authority to write blog.
                </caption>
                <thead class="border border-black">
                    <tr>
                        <th class="px-4 py-3 text-lg font-bold text-gray-100 bg-gray-400">Avatar</th>
                        <th class="px-4 py-3 text-lg font-bold text-gray-100 bg-gray-400">Name</th>
                        <th class="px-4 py-3 text-lg font-bold text-gray-100 bg-gray-400">Email</th>
                        <th class="px-4 py-3 text-lg font-bold text-gray-100 bg-gray-400">Gender</th>
                    </tr>
                </thead>
                <tbody class="border border-black">
                    <?php
                    foreach ($authors as $author) : ?>
                        <tr>
                            <td class="px-4 py-2 text-sm font-semibold text-gray-800 border-b border-black">
                                <a href="profile.php?user_id=<?= $author["author_id"] ?>">
                                    <?php if (isset($author["avatar"])) : ?>
                                        <img src="images/avatars/<?= $author["avatar"] ?>" alt="Author avatar" class="size-8 rounded-full object-cover">
                                    <?php else : ?>
                                        <img src="assets/dummy.jpg" alt="Default avatar" class="size-8 rounded-full object-cover">
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 border-b border-black"> <?= $author["author_name"] ?></td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 border-b border-black"> <?= $author["email"] ?></td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800 border-b border-black"> <?= $author["gender"] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>

</html>