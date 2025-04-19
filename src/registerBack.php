<?php
session_start();
include "connect.php"; // Uses $conn (MySQLi)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = htmlspecialchars(trim($_POST["first_name"]));
    $lname = htmlspecialchars(trim($_POST["last_name"]));
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];
    $terms = isset($_POST["terms"]);

    if (!$fname || !$lname || !$email || !$password || !$confirm || !$terms) {
        echo "<script>alert('Please fill all fields and accept terms.'); window.history.back();</script>";
        exit();
    }

    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    if (!preg_match("/^(?=.*\d).{6,}$/", $password)) {
        echo "<script>alert('Password must be at least 6 characters and contain a number.'); window.history.back();</script>";
        exit();
    }

    $hashed_pass = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, agreed_terms) VALUES (?, ?, ?, ?, ?)");
    $agreed = 1; // true for agreed_terms
    $stmt->bind_param("ssssi", $fname, $lname, $email, $hashed_pass, $agreed);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: homelogined.php");
        exit();
    } else {
        echo "<script>alert('Registration failed: " . $stmt->error . "'); window.history.back();</script>";
    }
}
?>
