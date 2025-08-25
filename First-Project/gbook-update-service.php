<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    if ($id <= 0) {
        echo "Invalid entry ID.";
        exit;
    }
    if (empty($name) || empty($email) || empty($message)) {
        echo "All fields are required.";
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please enter a valid email address.";
        exit;
    }
    $stmt = $conn->prepare("UPDATE guestbook SET name=?, email=?, message=? WHERE id=? AND Active=1");
    $stmt->bind_param("sssi", $name, $email, $message, $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Entry updated successfully!";
        } else {
            echo "No entry found with that ID or entry is inactive.";
        }
    } else {
        echo "Error updating entry: " . $conn->error;
    }
    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
