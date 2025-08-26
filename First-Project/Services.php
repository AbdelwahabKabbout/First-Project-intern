<?php
require 'config.php';

function CreateEntries() {
    global $conn;

    try {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "Invalid request method.";
        }

        $name = trim($_POST['Name'] ?? '');
        $email = trim($_POST['Email'] ?? '');
        $message = trim($_POST['Message'] ?? '');

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Please enter a valid email address.";
        }

        $missingFields = [];
        if (empty($name)) $missingFields[] = "Name";
        if (empty($email)) $missingFields[] = "Email";
        if (empty($message)) $missingFields[] = "Message";

        if (!empty($missingFields)) {
            return "The following fields are required: " . implode(", ", $missingFields) . ".";
        }

        $stmt = $conn->prepare("INSERT INTO guestbook (name, email, message) VALUES (?, ?, ?)");
        if (!$stmt) {
            return "Database preparation error: " . $conn->error;
        }

        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $stmt->close();
            return true; 
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Database error: " . $error;
        }

    } catch (Exception $e) {
        return "An error occurred: " . $e->getMessage();
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
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
        $stmt = $conn->prepare("UPDATE guestbook SET Active=0 WHERE id=?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                $conn->close();
                header("Location: index.php?deleted=Successfully-deleted");
                exit;
            } else {
                $stmt->close();
                $conn->close();
                header("Location: index.php?deleted=Delete-failed");
                exit;
            }
        } else {
            $stmt->close();
            $conn->close();
            header("Location: index.php?deleted=Delete-failed");
            exit;
        }
    } else {
        $conn->close();
        header("Location: index.php?deleted=Delete-failed");
        exit;
    }
}


function ReadEntriesForDisplay() {
    global $conn;

    // Check if dark mode is active
    $isDarkMode = ($_SESSION['theme'] ?? 'light') === 'dark';
    $darkClass = $isDarkMode ? ' dark' : '';

    // Paging setup
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    // Get total number of active entries for pagination
    $countResult = $conn->query("SELECT COUNT(*) as total FROM guestbook WHERE Active=1");
    $totalEntries = $countResult ? $countResult->fetch_assoc()['total'] : 0;
    $totalPages = ceil($totalEntries / $limit);

    // Fetch entries for current page
    $sql = "SELECT id, name, email, message, createdAt FROM guestbook WHERE Active=1 ORDER BY createdAt DESC LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class='entry{$darkClass}'>";
            echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
            echo "<p>" . nl2br(htmlspecialchars($row['message'])) . "</p>";

            echo "<div class='entry-buttons{$darkClass}'>";
            echo "<a href='gbook-edit.php?id=" . $row['id'] . "' class='update-btn{$darkClass}'>Update</a>";
            echo "<a href='index.php?delete_id=" . $row['id'] . "' class='delete-btn{$darkClass}' onclick='return confirm(\"Are you sure you want to delete this entry?\")'>Delete</a>";
            echo "</div>";

            echo "</div><hr>";
        }

        
        echo "<div class='pagination{$darkClass}'>";
        if ($page > 1) {
            echo "<a href='?page=" . ($page - 1) . "'>&laquo; Prev</a> ";
        }
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $page) {
                echo "<strong>$i</strong> ";
            } else {
                echo "<a href='?page=$i'>$i</a> ";
            }
        }
        if ($page < $totalPages) {
            echo "<a href='?page=" . ($page + 1) . "'>Next &raquo;</a>";
        }
        echo "</div>";

    } else {
        echo "No entries found.";
    }
    
}


function ReadEntries() {
    global $conn;

    // Get current page from query string, default to 1
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    // Get total number of active entries for pagination
    $countResult = $conn->query("SELECT COUNT(*) as total FROM guestbook WHERE Active=1");
    $totalEntries = $countResult ? $countResult->fetch_assoc()['total'] : 0;
    $totalPages = ceil($totalEntries / $limit);

    // Fetch entries for current page
    $sql = "SELECT id, name, email, message, createdAt FROM guestbook WHERE Active=1 ORDER BY createdAt DESC LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
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

        // Pagination links
        echo "<div class='pagination'>";
        if ($page > 1) {
            echo "<a href='?page=" . ($page - 1) . "'>&laquo; Prev</a> ";
        }
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $page) {
                echo "<strong>$i</strong> ";
            } else {
                echo "<a href='?page=$i'>$i</a> ";
            }
        }
        if ($page < $totalPages) {
            echo "<a href='?page=" . ($page + 1) . "'>Next &raquo;</a>";
        }
        echo "</div>";

    } else {
        echo "No entries found.";
    }
    // Do not close $conn here
}
?>