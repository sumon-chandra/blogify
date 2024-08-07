<?php
require_once "includes/config.session.php";
$errors = isset($_SESSION["login_errors"]) ? $_SESSION["login_errors"] : "";
$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";
$isLoggedId = $user_id;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blogify</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen w-full bg-gray-200">
    <header class="bg-gray-800">
        <nav class="flex items-center justify-between p-4 lg:w-[1250px] mx-auto">
            <h1 class="text-2xl font-bold text-white"><a href="index.php">Blogify</a>
            </h1>
            <ul class="flex">
                <li class="mx-4"><a href="#" class="text-white hover:text-gray-300">Home</a></li>
                <li class="mx-4"><a href="#" class="text-white hover:text-gray-300">About</a></li>
                <li class="mx-4"><a href="#" class="text-white hover:text-gray-300">Contact</a></li>
                <li class="mx-4"><a href="blogs.php" class="text-white hover:text-gray-400">Blogs</a></li>
                <?php if ($isLoggedId) : ?>
                    <li class="mx-4"><a href="dashboard.php" class="text-white hover:text-gray-400">Dashboard</a></li>
                    <li class="mx-4"><a href="includes/login/logout.inc.php" class="text-white hover:text-gray-400">Logout</a></li>
                <?php else : ?>
                    <li class="mx-4"><a href="login.php" class="text-white hover:text-gray-400">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="grid place-items-center">
        <div class="mx-auto">
            <h1 class="text-4xl font-bold text-center mt-12">Login to Your Blogify Account</h1>
            <form action="includes/login/login.inc.php" method="POST" enctype="multipart/form-data" class="w-96 mx-auto mt-10 bg-white p-5 rounded-md">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600" required autofocus>
                </div>
                <div class="mb-4">
                    <label for="pwd" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="pwd" id="pwd" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600" required>
                </div>
                <button type="submit" class="w-full mt-4 bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md">Login</button>
                <p class="text-center text-gray-400 mt-4">Don't have an account? <a href="signup.php" class="text-gray-800 hover:text-gray-700">Sign up</a></p>
            </form>
            <!-- display the errors list with foreach -->
            <?php if (!empty($errors)) : ?>
                <ul class="text-red-500">
                    <?php foreach ($errors as $error) : ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>