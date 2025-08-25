<?php
require 'config.php';

// Check if it's a DELETE request and id parameter exists
if ($_SERVER["REQUEST_METHOD"] == "DELETE" && isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert to integer for security
    
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE guestbook SET Active=0 WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Entry deleted successfully.";
        } else {
            echo "No entry found with that ID.";
        }
    } else {
        echo "Error deleting entry.";
    }
    
    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>