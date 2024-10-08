<?php
require_once "includes/config.session.php";
$errors = isset($_SESSION["signup_errors"]) ? $_SESSION["signup_errors"] : "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Blogify</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen w-full bg-gray-200">
    <header class="bg-gray-800">
        <nav class="flex items-center justify-between p-4  lg:w-[1250px] mx-auto">
            <h1 class="text-2xl font-bold text-white"><a href="index.php">Blogify</a>
            </h1>
            <ul class="flex">
                <li class="mx-4"><a href="index.php" class="text-white hover:text-gray-300">Home</a></li>
                <li class="mx-4"><a href="#" class="text-white hover:text-gray-300">About</a></li>
                <li class="mx-4"><a href="#" class="text-white hover:text-gray-300">Contact</a></li>
                <li class="mx-4"><a href="login.php" class="text-white hover:text-gray-300">Login</a></li>
            </ul>
        </nav>
    </header>
    <main class="grid place-items-center">
        <div class="lg:w-5/12 mx-auto mt-20 bg-white p-5 rounded-md space-y-5">
            <h1 class="text-lg font-bold text-center">Signup to <a href="index.php">Blogify</a>
            </h1>
            <!-- Display errors -->
            <?php if (!empty($errors)) : ?>
                <div class="mt-4 text-center font-xs font-semibold text-red-500">
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?= $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php
            if (isset($_SESSION["signup_errors"])) {
                unset($_SESSION["signup_errors"]);
            }
            ?>

            <form action="includes/signup/signup.inc.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div class="w-full">
                        <label for="first_name" class="block text-gray-700 text-sm font-bold mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600" required autofocus>
                    </div>
                    <div class="w-full">
                        <label for="last_name" class="block text-gray-700 text-sm font-bold mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600" required autofocus>
                    </div>
                </div>
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div class="w-full">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" id="email" name="email" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600" required autofocus>
                    </div>
                    <div class="w-full">
                        <label for="pwd" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                        <input type="password" name="pwd" id="pwd" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600" required>
                    </div>
                </div>
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div class="w-full">
                        <label for="dob" class="block text-gray-700 text-sm font-bold mb-2">Date of Birth</label>
                        <input type="date" id="dob" name="dob" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600">
                    </div>
                    <div class="w-full">
                        <label for="gender">Gender</label>
                        <select class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600" name="gender" id="gender">
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                            <option value="3">Others</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="avatar" class="block text-gray-700 text-sm font-bold mb-2">Profile Picture</label>
                    <input type="file" id="avatar" name="avatar" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600">
                </div>
                <button type="submit" class="w-full mt-4 bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md">Signup</button>
                <p class="text-center text-gray-400 mt-4">Already have an account? <a href="login.php" class="text-gray-800 hover:text-gray-700">Login</a></p>
            </form>
        </div>
    </main>
</body>

</html>