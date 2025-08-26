<?php
session_start();
require 'config.php';

if (isset($_GET['toggle_theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] ?? 'light') === 'light' ? 'dark' : 'light';
    $redirect_url = 'gbook-edit.php';
    $params = $_GET;
    unset($params['toggle_theme']);
    if (!empty($params)) {
        $redirect_url .= '?' . http_build_query($params);
    }
    header("Location: $redirect_url");
    exit;
}

$currentTheme = $_SESSION['theme'] ?? 'light';
$isDarkMode = $currentTheme === 'dark';

$entryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$entry = null;
$error = '';
$success = '';
$updateError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_entry'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    if ($id <= 0) {
        $updateError = "Invalid entry ID.";
    } elseif (empty($name) || empty($email) || empty($message)) {
        $updateError = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $updateError = "Please enter a valid email address.";
    } else {
        $stmt = $conn->prepare("UPDATE guestbook SET name=?, email=?, message=? WHERE id=? AND Active=1");
        $stmt->bind_param("sssi", $name, $email, $message, $id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                $conn->close();
                header("Location: index.php?success=Successfully-updated");
                exit;
            } else {
                $updateError = "No entry found with that ID or entry is inactive.";
            }
        } else {
            $updateError = "Error updating entry: " . $conn->error;
        }
        $stmt->close();
    }
}

if ($entryId > 0) {
    $stmt = $conn->prepare("SELECT id, name, email, message, createdAt FROM guestbook WHERE id=? AND Active=1");
    $stmt->bind_param("i", $entryId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $entry = $result->fetch_assoc();
    } else {
        $error = 'Entry not found or has been deleted.';
    }
    $stmt->close();
} else {
    $error = 'Invalid entry ID.';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Guestbook Entry</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Cormorant+Garamond:wght@400;600&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="gbook-edit.css">
</head>
<body<?php echo $isDarkMode ? ' class="dark-mode"' : ''; ?>>
    <a href="?toggle_theme=1<?php echo !empty($_GET) && !isset($_GET['toggle_theme']) ? '&' . http_build_query($_GET) : ''; ?>" 
       class="DarkLight<?php echo $isDarkMode ? ' dark' : ''; ?>">
        <?php echo $isDarkMode ? '☀️ Light' : '🌙 Dark'; ?>
    </a>
    <h1>Edit Guestbook Entry</h1>
    <?php if ($updateError): ?>
        <div class="error"><?php echo htmlspecialchars($updateError); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($entry): ?>
        <div class="meta-info">
            <strong>Entry ID:</strong> <?php echo $entry['id']; ?> | 
            <strong>Created:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($entry['createdAt'])); ?>
        </div>
        <div class="entry">
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                <input type="hidden" name="update_entry" value="1">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($entry['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($entry['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" required><?php echo htmlspecialchars($entry['message']); ?></textarea>
                </div>
                <div class="entry-buttons">
                    <button type="submit" class="Add">Update Entry</button>
                    <a href="index.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="entry">
            <p style="text-align: center; color: #d9534f;">Unable to load entry for editing.</p>
        </div>
    <?php endif; ?>
    <div class="back-link">
        <a href="index.php">← Back to Guestbook</a>
    </div>
</body>
</html>
