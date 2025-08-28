<?php
session_start();
require 'config.php';

// Theme handling
$isDarkMode = ($_SESSION['theme'] ?? 'light') === 'dark';

// View + pagination
$view = $_GET['view'] ?? 'main';
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? intval($_GET['page']) : 1;

// Messages
$successMsg = $_GET['success'] ?? '';
$deleteMsg  = $_GET['deleted'] ?? '';
$actionError = '';

// Global variables for form data persistence
$formName = '';
$formEmail = '';
$formMessage = '';
$formId = 0;


function getFormValue($field, $fallback = '')
{
    global $formName, $formEmail, $formMessage, $formId;

    switch ($field) {
        case 'Name':
            return htmlspecialchars($formName ?: $fallback);
        case 'Email':
            return htmlspecialchars($formEmail ?: $fallback);
        case 'Message':
            return htmlspecialchars($formMessage ?: $fallback);
        case 'name':
            return htmlspecialchars($formName ?: $fallback);
        case 'email':
            return htmlspecialchars($formEmail ?: $fallback);
        case 'message':
            return htmlspecialchars($formMessage ?: $fallback);
        case 'id':
            return $formId ?: $fallback;
        default:
            return htmlspecialchars($fallback);
    }
}

// Function to save form data to global vars
function saveFormData($data)
{
    global $formName, $formEmail, $formMessage, $formId;

    $formName = trim($data['Name'] ?? $data['name'] ?? '');
    $formEmail = trim($data['Email'] ?? $data['email'] ?? '');
    $formMessage = trim($data['Message'] ?? $data['message'] ?? '');
    $formId = intval($data['id'] ?? 0);
}

// Function to clear form data
function clearFormData()
{
    global $formName, $formEmail, $formMessage, $formId;

    $formName = '';
    $formEmail = '';
    $formMessage = '';
    $formId = 0;
}

function CreateEntry()
{
    global $conn;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return "Invalid request";
    }

    $name    = trim($_POST['Name'] ?? '');
    $email   = trim($_POST['Email'] ?? '');
    $message = trim($_POST['Message'] ?? '');

    // Save form data in case of errors
    saveFormData($_POST);

    $missing = [];
    if ($name === '')    $missing[] = 'Name';
    if ($email === '')   $missing[] = 'Email';
    if ($message === '') $missing[] = 'Message';

    if ($missing) {
        return "Required: " . implode(', ', $missing);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email";
    }

    $stmt = $conn->prepare("INSERT INTO guestbook (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        clearFormData(); // Clear on success
        return true;
    } else {
        return "Database error";
    }
}

function UpdateEntry()
{
    global $conn;

    $id      = intval($_POST['id'] ?? 0);
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Save form data in case of errors
    saveFormData($_POST);

    if ($id <= 0) return "Invalid ID";
    if ($name === '' || $email === '' || $message === '') return "All fields required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "Invalid email";

    $stmt = $conn->prepare("UPDATE guestbook SET name=?, email=?, message=? WHERE id=? AND Active=1");
    $stmt->bind_param("sssi", $name, $email, $message, $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        clearFormData(); // Clear on success
        return true;
    }
    return "Update failed";
}

function DeleteEntry()
{
    global $conn;

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) return false;

    $stmt = $conn->prepare("UPDATE guestbook SET Active=0 WHERE id=?");
    $stmt->bind_param("i", $id);

    return ($stmt->execute() && $stmt->affected_rows > 0);
}

function GetEntry($id)
{
    global $conn;

    $stmt = $conn->prepare("SELECT id, name, email, message, createdAt FROM guestbook WHERE id=? AND Active=1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->num_rows > 0 ? $res->fetch_assoc() : null;
}

function DisplayEntries($page = 1)
{
    global $conn;

    $limit  = 10;
    $offset = ($page - 1) * $limit;

    $countResult  = $conn->query("SELECT COUNT(*) AS total FROM guestbook WHERE Active=1");
    $totalEntries = (int)($countResult->fetch_assoc()['total'] ?? 0);
    $totalPages   = max(1, (int)ceil($totalEntries / $limit));

    $result = $conn->query("SELECT id, name, email, message, createdAt 
                            FROM guestbook 
                            WHERE Active=1 
                            ORDER BY createdAt DESC 
                            LIMIT $limit OFFSET $offset");

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='entry'>";
            echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
            echo "<p>" . nl2br(htmlspecialchars($row['message'])) . "</p>";
            echo "<div class='entry-buttons'>";
            echo "<a href='index.php?view=edit&id=" . (int)$row['id'] . "' class='update-btn'>Update</a>";
            echo "<a href='index.php?action=Delete&id=" . (int)$row['id'] . "' class='delete-btn' onclick='return confirm(\"Delete this entry?\")'>Delete</a>";
            echo "</div></div><hr>";
        }

        // Pagination
        echo "<div class='pagination'>";
        if ($page > 1) echo "<a href='index.php?page=" . ($page - 1) . "'>&laquo; Prev</a> ";
        for ($i = 1; $i <= $totalPages; $i++) {
            echo $i == $page ? "<strong>$i</strong> " : "<a href='index.php?page=$i'>$i</a> ";
        }
        if ($page < $totalPages) echo "<a href='index.php?page=" . ($page + 1) . "'>Next &raquo;</a>";
        echo "</div>";
    } else {
        echo "<p>No entries found.</p>";
    }
}

// Unified request handler
$action = $_GET['action'] ?? '';

// Theme toggle
if (isset($_GET['toggle_theme'])) {
    $_SESSION['theme'] = ($isDarkMode ? 'light' : 'dark');
    $redirect = $_GET['redirect'] ?? 'index.php';
    header("Location: " . $redirect);
    exit;
}

// POST handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_entry'])) {
        $result = CreateEntry();
        if ($result === true) {
            header("Location: index.php?success=Entry added");
            exit;
        } else {
            $actionError = $result;
            $view = 'add';
        }
    } elseif (isset($_POST['update_entry'])) {
        $result = UpdateEntry();
        if ($result === true) {
            header("Location: index.php?success=Entry updated");
            exit;
        } else {
            $actionError = $result;
            $view = 'edit';
        }
    }
}

// GET: delete
if ($action === 'Delete') {
    $ok = DeleteEntry();
    header("Location: index.php?" . ($ok ? "deleted=Entry deleted" : "deleted=Delete failed"));
    exit;
}

// Edit view: fetch entry and populate form data
$entry = null;
if ($view === 'edit') {
    $entryId = intval($_GET['id'] ?? 0);
    if ($entryId > 0) {
        $entry = GetEntry($entryId);
        if ($entry && empty($actionError)) {
            // Only populate from DB if no error (no form data to preserve)
            saveFormData($entry);
        }
        if (!$entry) $actionError = "Entry not found";
    } else {
        $actionError = "Invalid ID";
    }
}

// Clear form data when displaying fresh forms (no errors)
if (in_array($view, ['add']) && empty($actionError)) {
    clearFormData();
}

// CSS
$cssFile = $isDarkMode ? 'Dark.css' : 'Light.css';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Guestbook</title>
    <link rel="stylesheet" href="<?= $cssFile ?>">
</head>
<body<?= $isDarkMode ? ' class="dark-mode"' : '' ?>>

    <a href="index.php?toggle_theme=1&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="DarkLight">
        <?= $isDarkMode ? '☀️ Light' : '🌙 Dark' ?>
    </a>

    <?php if ($view === 'main'): ?>
        <h1>Welcome to Gbook</h1>
        <a href="index.php?view=add" class="Add">Make a message!</a>

        <div class="MessageContainer">
            <?php
            switch (true) {
                case ($successMsg === 'Entry added'):
                    echo '<div class="success-message">Entry created successfully!</div>';
                    break;
                case ($successMsg === 'Entry updated'):
                    echo '<div class="success-message">Entry updated successfully!</div>';
                    break;
                case ($deleteMsg === 'Entry deleted'):
                    echo '<div class="success-message">Entry deleted successfully!</div>';
                    break;
                case ($deleteMsg === 'Delete failed'):
                    echo '<div class="error-message">Failed to delete entry!</div>';
                    break;
            }
            ?>
        </div>

        <div class="entries-container">
            <?php DisplayEntries($page); ?>
        </div>

    <?php elseif ($view === 'add'): ?>
        <h1>Add New Entry</h1>

        <?php if ($actionError): ?>
            <div class="error-message"><?= htmlspecialchars($actionError) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?view=add">
            <input type="hidden" name="create_entry" value="1">

            <label for="Name">Name:
                <input type="text" id="Name" name="Name" value="<?= getFormValue('Name') ?>">
            </label>

            <label for="Email">Email:
                <input type="text" id="Email" name="Email" value="<?= getFormValue('Email') ?>">
            </label>

            <label for="Message">Message:
                <textarea id="Message" name="Message" placeholder="Write your message here..."><?= getFormValue('Message') ?></textarea>
            </label>

            <div class="form-buttons">
                <button type="submit" class="Submit-btn">Submit</button>
                <a href="index.php" class="Back-btn">Back</a>
            </div>
        </form>

    <?php elseif ($view === 'edit'): ?>
        <h1>Edit Entry</h1>

        <?php if ($actionError): ?>
            <div class="error-message"><?= htmlspecialchars($actionError) ?></div>
        <?php endif; ?>

        <?php if (!$entry): ?>
            <div class="error-message">Entry not found or invalid ID.</div>
            <div class="form-buttons">
                <a href="index.php" class="Back-btn">Back to Main</a>
            </div>
        <?php else: ?>
            <form method="POST" action="index.php?view=edit&id=<?= (int)$entry['id'] ?>">
                <input type="hidden" name="update_entry" value="1">
                <input type="hidden" name="id" value="<?= getFormValue('id', $entry['id']) ?>">

                <label for="name">Name:
                    <input type="text" id="name" name="name" value="<?= getFormValue('name', $entry['name']) ?>">
                </label>

                <label for="email">Email:
                    <input type="email" id="email" name="email" value="<?= getFormValue('email', $entry['email']) ?>">
                </label>

                <label for="message">Message:
                    <textarea id="message" name="message"><?= getFormValue('message', $entry['message']) ?></textarea>
                </label>

                <div class="form-buttons">
                    <button type="submit" class="Submit-btn">Update Entry</button>
                    <a href="index.php" class="Back-btn">Back</a>
                </div>
            </form>
        <?php endif; ?>
    <?php endif; ?>

    </body>

</html>