<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "rmp_minds_data";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Request method: " . $_SERVER["REQUEST_METHOD"] . "<br>";
echo "POST data: ";
print_r($_POST);
echo "<br>Content-Type: " . ($_SERVER["CONTENT_TYPE"] ?? "Not set") . "<br>";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($name) || empty($email) || empty($message)) {
        die("All fields are required.");
    }

    $sql = "INSERT INTO contact_form (name, email, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sss", $name, $email, $message);
    $stmt->close();
} else {
    echo "No valid form data received. Ensure the form uses method='POST' and action points to this file.";
}

$conn->close();
?>