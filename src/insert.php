<?php
include "connect.php";

$name = "Alice Johnson";
$email = "alice@example.com";
$password = password_hash("alice123", PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
    echo "User added successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
