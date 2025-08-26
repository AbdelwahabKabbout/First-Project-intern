<?php
require 'config.php';

function CreateEntries() {
    global $conn;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['Name'];
        $email = $_POST['Email'];
        $message = $_POST['Message'];

        if(empty($name) || empty($email) || empty($message)) {
            die("All fields are required.");
        }

        $stmt = $conn->prepare("INSERT INTO guestbook (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Invalid request method.";
    }
}

function UpdateEntries(){
    global $conn;
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
}

function DeleteEntries(){
    global $conn;
    if ($_SERVER["REQUEST_METHOD"] == "DELETE" && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
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
}

function ReadEntries(){
    global $conn;
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
    $conn->close();
}
?>