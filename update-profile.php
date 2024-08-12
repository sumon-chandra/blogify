<?php
require_once "includes/config.session.php";
require_once "includes/user/user.inc.php";

$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";
$profile_id = isset($_GET["id"]) ? $_GET["id"] : "";
$isLoggedId = $user_id;

if (!$isLoggedId) {
    header("Location: login.php");
    exit();
}
$userModel = new User();
$user_role = $isLoggedId ? $userModel->userRole($user_id) : "";
$admin = $user_role == "Admin" ? "Admin" : "";

$user = $userModel->getUserById($profile_id);
$user_id = isset($user["user_id"]) ? $user["user_id"] : '';
$first_name = isset($user["first_name"]) ? $user["first_name"] : '';
$last_name = isset($user["last_name"]) ? $user["last_name"] : '';
$email = isset($user["email"]) ? $user["email"] : '';
$dob = isset($user["dob"]) ? $user["dob"] : '';
$gender_id = isset($user["gender_id"]) ? $user["gender_id"] : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - Blogify</title>
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
    <main class="lg:w-[1250px] mx-auto">
        <h1 class="text-2xl font-bold text-center my-10">Update profile</h1>
        <form action="includes/user/update-user.inc.php" method="POST" enctype="multipart/form-data" class="lg:w-5/12 mx-auto mt-10 bg-white p-5 rounded-md">
            <div class="mb-4 flex items-center justify-between gap-4">
                <input type="hidden" name="user_id" value="<?= $user_id ?>" id="">
                <div class="w-full">
                    <label for="first_name" class="block text-gray-700 text-sm font-bold mb-2">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?= $first_name ?>" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600" required autofocus>
                </div>
                <div class="w-full">
                    <label for="last_name" class="block text-gray-700 text-sm font-bold mb-2">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?= $last_name ?>" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600" required>
                </div>
            </div>
            <div class="mb-4 flex items-center justify-between gap-4">
                <div class="w-full">
                    <label for="dob" class="block text-gray-700 text-sm font-bold mb-2">Date of Birth</label>
                    <input type="date" id="dob" name="dob" value="<?= $dob ?>" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600">
                </div>
                <div class="w-full">
                    <label for="gender">Gender</label>
                    <select class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600" name="gender" id="gender">
                        <option value="1" <?= $gender_id == '1' ? 'selected' : '' ?>>Male</option>
                        <option value="2" <?= $gender_id == '2' ? 'selected' : '' ?>>Female</option>
                        <option value="3" <?= $gender_id == '3' ? 'selected' : '' ?>>Others</option>
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label for="avatar" class="block text-gray-700 text-sm font-bold mb-2">Profile Picture</label>
                <input type="file" id="avatar" name="avatar" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-gray-600">
            </div>
            <button type="submit" class="w-full mt-4 bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md">Update</button>
        </form>
    </main>
</body>

</html>