<?php
require_once 'Dark-Light.php';   // handles theme
require_once 'index-Services.php'; // handles messages, database prep, etc.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guestbook</title>
    <link rel="stylesheet" href="index.css">
</head>
<body<?php echo $isDarkMode ? ' class="dark-mode"' : ''; ?>>

    <!-- Theme toggle -->
    <a href="Dark-Light.php?toggle_theme=1&redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"
        class="DarkLight<?php echo $isDarkMode ? ' dark' : ''; ?>">
        <?php echo $isDarkMode ? '☀️ Light' : '🌙 Dark'; ?>
    </a>


    <h1<?php echo $isDarkMode ? ' class="dark"' : ''; ?>>Welcome To Gbook</h1>
        <a href="gbook-add.php" class="Add<?php echo $isDarkMode ? ' dark' : ''; ?>">Make a message!</a>

        <div class="container" id="guestbookContainer">
            <div class="MessageContainer">
                <?php if ($successMsg == 'Successfully-added'): ?>
                    <div class="success-message">✅ Entry created successfully!</div>
                <?php endif; ?>

                <?php if ($successMsg == 'Successfully-updated'): ?>
                    <div class="success-message">📝 Entry updated successfully!</div>
                <?php endif; ?>

                <?php if ($deleteMsg == 'Successfully-deleted'): ?>
                    <div class="success-message">🗑️ Entry deleted successfully!</div>
                <?php endif; ?>

                <?php if ($deleteMsg == 'Delete-failed'): ?>
                    <div class="error-message">❌ Failed to delete entry!</div>
                <?php endif; ?>
            </div>

            <div class="entries-container">
                <?php ReadEntriesForDisplay(); ?>
            </div>
        </div>

        </body>

</html>