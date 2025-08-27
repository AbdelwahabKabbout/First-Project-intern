<?php
// gbook-edit.php

require_once 'Dark-Light.php';      // handles session_start(), $isDarkMode, theme toggle
require_once 'config.php';          // DB connection
require_once 'Services.php';        // DB functions like GetEntry()

// === Handle update if form submitted ===
$updateError = '';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_entry'])) {
    $result = UpdateEntries(); // call function from Services.php
    if ($result === true) {
        header("Location: index.php?success=Successfully-updated");
        exit;
    } else {
        $updateError = $result; // will show error on this page
    }
}

// === Get entry to edit ===
$entryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$entry = null;
$error = '';

if ($entryId > 0) {
    $entry = GetEntry($entryId); // from Services.php
    if (!$entry) {
        $error = "Entry not found or has been deleted.";
    }
} else {
    $error = "Invalid entry ID.";
}
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

    <!-- Theme toggle -->
    <a href="Dark-Light.php?toggle_theme=1&redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"
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