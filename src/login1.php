<?php
session_start();
include "connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($name, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user'] = $name;
            header("Location: home.html");
            exit();
        } else {
            echo "Incorrect password. <a href='home.html'>Try again</a>";
        }
    } else {
        echo "Email not found. <a href='login.html'>Try again</a>";
    }

    $stmt->close();
    $conn->close();
}
?>
