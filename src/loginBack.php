<?php
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $remember = isset($_POST["remember"]);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        session_start();
        $_SESSION["user_id"] = $user["id"];

        if ($remember) {
            setcookie("remember_user", $user["id"], time() + (86400 * 30), "/");
        }

        header("Location: homelogined.php");
        exit();
    } else {
        echo "Invalid credentials.";
    }

    $stmt->close();
    $conn->close();
}
?>