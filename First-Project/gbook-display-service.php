<?php
require 'config.php';
$sql = "SELECT id, name, email, message, createdAt FROM guestbook WHERE Active=1 ORDER BY createdAt DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='entry'>";
        echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
        echo "<p>" . nl2br(htmlspecialchars($row['message'])) . "</p>";
        
        
        echo "<div class='entry-buttons'>";
        echo "<button class='update-btn' data-id='" . $row['id'] . "'>Update</button>";
        echo "<button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button>";
        echo "</div>"; 
        
        echo "</div><hr>";
    }
} else {
    echo "No entries found.";
}
?>