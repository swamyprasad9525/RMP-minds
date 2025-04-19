<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];
$user_email = "";

$stmt = $conn->prepare("SELECT first_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_name, $user_email);
$stmt->fetch();
$stmt->close();

$_SESSION["user_name"] = $user_name;
$_SESSION["user_email"] = $user_email;

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST["username"]);
    $new_email = trim($_POST["email"]);
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($new_name)) {
        $error_message = "Username cannot be empty";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address";
    } elseif (!empty($new_password) && $new_password != $confirm_password) {
        $error_message = "New passwords do not match";
    } else {
        $password_verified = true;

        if (!empty($current_password) || !empty($new_password)) {
            $check_stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $check_stmt->bind_param("i", $user_id);
            $check_stmt->execute();
            $check_stmt->bind_result($hashed_password);
            $check_stmt->fetch();
            $check_stmt->close();

            if (!password_verify($current_password, $hashed_password)) {
                $error_message = "Current password is incorrect";
                $password_verified = false;
            }
        }

        if ($password_verified) {
            $password_changed = false;

            if (!empty($new_password)) {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET first_name = ?, email = ?, password = ? WHERE id = ?");
                $update_stmt->bind_param("sssi", $new_name, $new_email, $new_hashed_password, $user_id);
                $password_changed = true;
            } else {
                $update_stmt = $conn->prepare("UPDATE users SET first_name = ?, email = ? WHERE id = ?");
                $update_stmt->bind_param("ssi", $new_name, $new_email, $user_id);
            }

            if ($update_stmt->execute()) {
                $_SESSION["user_name"] = $new_name;
                $_SESSION["user_email"] = $new_email;
                $user_name = $new_name;
                $user_email = $new_email;
                $success_message = "Profile updated successfully!";
            } else {
                $error_message = "Error updating profile: " . $conn->error;
            }

            $update_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RMP Minds - Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="bg-gradient-to-r from-blue-100 to-purple-100 min-h-screen font-sans">

  <!-- Navbar -->
  <nav class="bg-white shadow-md p-4">
  <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8 text-blue-600 dark:text-blue-400">
              <path d="M12 2a8 8 0 0 0-8 8c0 6 8 12 8 12s8-6 8-12a8 8 0 0 0-8-8zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
            </svg>
            <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent">RMP MINDS</span>
            </div>
    
    <div class="flex items-center space-x-4">
        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($user_name); ?></span>
      <a href="homelogined.php" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow transition duration-200">
        <i class="fas fa-home mr-2"></i> Home
      </a>
    </div>
  </div>
</nav>

  <!-- Profile Container -->
  <main class="max-w-3xl mx-auto p-6 mt-10 bg-white rounded-2xl shadow-xl">
    <div class="flex items-center space-x-4 mb-6">
      <div class="w-16 h-16 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 text-white flex items-center justify-center text-2xl font-bold shadow">
        <?php echo strtoupper(substr($user_name, 0, 1)); ?>
      </div>
      <div>
        <h1 class="text-2xl font-semibold text-gray-800">Update Your Profile</h1>
        <p class="text-sm text-gray-500">Change your account settings</p>
      </div>
    </div>

    <?php if (!empty($success_message)): ?>
      <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4 flex items-center">
        <i class="fas fa-check-circle mr-2"></i> <?php echo $success_message; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
      <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4 flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error_message; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-700 border-l-4 border-indigo-500 pl-3 mb-4">Personal Information</h2>

        <label class="block mb-2 text-sm font-medium text-gray-700">Username</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user_name); ?>" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400"/>

        <label class="block mt-4 mb-2 text-sm font-medium text-gray-700">Email Address</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400"/>
      </div>

      <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-700 border-l-4 border-indigo-500 pl-3 mb-4">Change Password</h2>

        <label class="block mb-2 text-sm font-medium text-gray-700">Current Password</label>
        <input type="password" name="current_password" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400"/>

        <label class="block mt-4 mb-2 text-sm font-medium text-gray-700">New Password</label>
        <input type="password" name="new_password" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400"/>

        <label class="block mt-4 mb-2 text-sm font-medium text-gray-700">Confirm New Password</label>
        <input type="password" name="confirm_password" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400"/>
      </div>

      <div class="flex flex-wrap gap-4 mt-6">
        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Save Changes</button>
        <a href="dashboard.php" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancel</a>
      </div>
    </form>
  </main>

</body>
</html>
