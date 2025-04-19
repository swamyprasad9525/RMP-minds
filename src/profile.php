<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT id, first_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_id, $user_name, $user_email);
$stmt->fetch();
$stmt->close();

$_SESSION["user_name"] = $user_name;
$_SESSION["user_email"] = $user_email;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-[Poppins] bg-gray-100 m-0 p-0">
<nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16 items-center">
      <div class="flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8 text-blue-600 dark:text-blue-400">
              <path d="M12 2a8 8 0 0 0-8 8c0 6 8 12 8 12s8-6 8-12a8 8 0 0 0-8-8zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
            </svg>
            <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent">RMP MINDS</span>
      </div>
        <div class="space-x-4">
          <a href="homelogined.php" class="text-gray-700 hover:text-blue-600">Home</a>
          <a href="update_profile.php" class="text-gray-700 hover:text-blue-600">Edit Profile</a>
          <a href="logout.php" class="text-red-600 hover:text-red-700 font-semibold">Logout</a>
        </div>
      </div>
    </div>
  </nav>
  <div class="max-w-3xl mx-auto my-12 p-8 bg-white rounded-xl shadow-lg">
    <div class="flex items-center mb-8">
      <div class="w-20 h-20 bg-indigo-600 text-white rounded-full flex items-center justify-center text-3xl font-semibold">
        <?php echo strtoupper(substr($user_name, 0, 1)); ?>
      </div>
      <div class="ml-5">
        <h1 class="text-2xl font-semibold text-gray-800"><?php echo htmlspecialchars($user_name); ?></h1>
        <p class="text-gray-600">Welcome to your profile!</p>
      </div>
    </div>

    <div class="mb-8">
      <h2 class="text-xl font-semibold text-indigo-600 border-b border-gray-200 pb-2 mb-4">Basic Info</h2>
      <div class="mb-4">
        <div class="font-semibold text-gray-700">Username</div>
        <div class="text-gray-800"><?php echo htmlspecialchars($user_name); ?></div>
      </div>
      <div class="mb-4">
        <div class="font-semibold text-gray-700">Email</div>
        <div class="text-gray-800"><?php echo htmlspecialchars($user_email); ?></div>
      </div>
    </div>

    <div class="mb-8">
      <h2 class="text-xl font-semibold text-indigo-600 border-b border-gray-200 pb-2 mb-4">Account Details</h2>
      <div class="mb-4">
        <div class="font-semibold text-gray-700">User ID</div>
        <div class="text-gray-800">#<?php echo htmlspecialchars($user_id); ?></div>
      </div>
    </div>

</body>
</html>
