<?php
require 'config.php';

function CreateEntries()
{
    global $conn;

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: gbook-add.php?error=" . urlencode("Invalid request method."));
        exit;
    }

    $name = trim($_POST['Name'] ?? '');
    $email = trim($_POST['Email'] ?? '');
    $message = trim($_POST['Message'] ?? '');

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: gbook-add.php?error=" . urlencode("Please enter a valid email address."));
        exit;
    }

    $missingFields = [];
    if (empty($name)) $missingFields[] = "Name";
    if (empty($email)) $missingFields[] = "Email";
    if (empty($message)) $missingFields[] = "Message";

    if (!empty($missingFields)) {
        header("Location: gbook-add.php?error=" . urlencode("The following fields are required: " . implode(", ", $missingFields) . "."));
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO guestbook (name, email, message) VALUES (?, ?, ?)");
    if (!$stmt) {
        header("Location: gbook-add.php?error=" . urlencode("Database preparation error: " . $conn->error));
        exit;
    }

    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        $stmt->close();
        unset($_SESSION['old']); // clear old input
        header("Location: index.php?success=Successfully-added");
        exit;
    } else {
        $error = $stmt->error;
        $stmt->close();
        header("Location: gbook-add.php?error=" . urlencode("Database error: " . $error));
        exit;
    }
}


function UpdateEntries()
{
    global $conn;

    if ($_SERVER["REQUEST_METHOD"] !== "POST") return "Invalid request method.";

    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($id <= 0) return "Invalid entry ID.";
    if (empty($name) || empty($email) || empty($message)) return "All fields are required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "Please enter a valid email address.";

    $stmt = $conn->prepare("UPDATE guestbook SET name=?, email=?, message=? WHERE id=? AND Active=1");
    $stmt->bind_param("sssi", $name, $email, $message, $id);

    if ($stmt->execute()) {
        $affected = $stmt->affected_rows;
        $stmt->close();
        if ($affected > 0) return true;
        return "No entry found with that ID or entry is inactive.";
    } else {
        $error = $stmt->error;
        $stmt->close();
        return "Error updating entry: " . $error;
    }
}

function DeleteEntries()
{
    global $conn;

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
        $id = intval($_GET['id']);

        $stmt = $conn->prepare("UPDATE guestbook SET Active=0 WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
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
        $conn->close();
        header("Location: index.php?deleted=Delete-failed");
        exit;
    }
}

function GetEntry($id)
{
    global $conn;

    $stmt = $conn->prepare("SELECT id, name, email, message, createdAt FROM guestbook WHERE id=? AND Active=1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $entry = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
    $stmt->close();
    return $entry;
}

function ReadEntriesForDisplay()
{
    global $conn;

    $isDarkMode = ($_SESSION['theme'] ?? 'light') === 'dark';
    $darkClass = $isDarkMode ? ' dark' : '';

    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $countResult = $conn->query("SELECT COUNT(*) as total FROM guestbook WHERE Active=1");
    $totalEntries = $countResult ? $countResult->fetch_assoc()['total'] : 0;
    $totalPages = ceil($totalEntries / $limit);

    $sql = "SELECT id, name, email, message, createdAt FROM guestbook WHERE Active=1 ORDER BY createdAt DESC LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='entry{$darkClass}'>";
            echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
            echo "<p>" . nl2br(htmlspecialchars($row['message'])) . "</p>";

            echo "<div class='entry-buttons{$darkClass}'>";
            echo "<a href='gbook-edit.php?id=" . $row['id'] . "' class='update-btn{$darkClass}'>Update</a>";
            echo "<a href='Routes.php?action=Delete&id=" . $row['id'] . "' 
        class='delete-btn{$darkClass}' 
        onclick='return confirm(\"Are you sure you want to delete this entry?\")'>
        Delete
      </a>";
            echo "</div>";
            echo "</div><hr>";
        }

        echo "<div class='pagination{$darkClass}'>";
        if ($page > 1) echo "<a href='?page=" . ($page - 1) . "'>&laquo; Prev</a> ";
        for ($i = 1; $i <= $totalPages; $i++) {
            echo $i == $page ? "<strong>$i</strong> " : "<a href='?page=$i'>$i</a> ";
        }
        if ($page < $totalPages) echo "<a href='?page=" . ($page + 1) . "'>Next &raquo;</a>";
        echo "</div>";
    } else {
        echo "No entries found.";
    }
}
