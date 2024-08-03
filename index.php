<?php
require_once "includes/config.session.php";
$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";
$isLoggedId = $user_id
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogify - A Blog Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <header>
        <nav class="flex items-center justify-between p-4 bg-gray-800">
            <h1 class="text-2xl font-bold text-white">Blogify</h1>
            <ul class="flex">
                <li class="mx-4"><a href="#" class="text-white hover:text-gray-400">Home</a></li>
                <li class="mx-4"><a href="#" class="text-white hover:text-gray-400">About</a></li>
                <li class="mx-4"><a href="#" class="text-white hover:text-gray-400">Contact</a></li>
                <?php if ($isLoggedId) : ?>
                    <li class="mx-4"><a href="#" class="text-white hover:text-gray-400">Dashboard</a></li>
                    <li class="mx-4"><a href="includes/login/logout.inc.php" class="text-white hover:text-gray-400">Logout</a></li>
                <?php else : ?>
                    <li class="mx-4"><a href="login.php" class="text-white hover:text-gray-400">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <h3>Hello World</h3>
        <p>This is a sample blog post.</p>
        <h3><?php echo "User ID :" . $user_id ?></h3>
    </main>
</body>

</html>