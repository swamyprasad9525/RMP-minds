<?php
session_start();
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize & validate inputs
    $fname = filter_input(INPUT_POST, "first_name", FILTER_SANITIZE_STRING);
    $lname = filter_input(INPUT_POST, "last_name", FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];
    $terms = isset($_POST["terms"]);

    if (!$fname || !$lname || !$email || !$password || !$confirm || !$terms) {
        die("Please fill all fields and accept terms.");
    }

    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    // Password validation (min 6, at least 1 number)
    if (!preg_match("/^(?=.*\d).{6,}$/", $password)) {
        die("Password must be at least 6 characters and contain a number.");
    }

    $hashed_pass = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fname, $lname, $email, $hashed_pass]);

        // Send welcome email
        mail($email, "Welcome to RMP MINDS", "Hi $fname,\n\nThanks for signing up!");

        echo "Account created! You can now <a href='login.php'>Sign In</a>";
    } catch (Exception $e) {
        echo "Registration error: " . $e->getMessage();
    }
}
?>