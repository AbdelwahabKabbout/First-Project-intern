<?php
require_once 'Dark-Light.php';
require_once 'Services.php';
$old = $_SESSION['old'] ?? [];
$errorMsg = $_GET['error'] ?? '';
$successMsg = $_GET['success'] ?? '';

function old($key)
{
    global $old;
    return htmlspecialchars($old[$key] ?? '');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Guestbook Entry</title>
    <link rel="stylesheet" href="gbook-add.css">
</head>
<body<?php echo $isDarkMode ? ' class="dark-mode"' : ''; ?>>

    <!-- Theme toggle -->
    <a href="Dark-Light.php?toggle_theme=1&redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"
        class="DarkLight<?php echo $isDarkMode ? ' dark' : ''; ?>">
        <?php echo $isDarkMode ? '☀️ Light' : '🌙 Dark'; ?>
    </a>

    <h1>Welcome to Gbook</h1>

    <div id="message-container">
        <?php if ($errorMsg): ?>
            <div class="error-message"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>
        <?php if ($successMsg == 'Successfully-added'): ?>
            <div class="success-message">✅ Entry created successfully!</div>
        <?php endif; ?>
    </div>

    <form id="addForm" action="Routes.php?action=Create" method="POST">
        <label for="Name">
            Name:
            <input type="text" id="Name" name="Name" value="<?= old('Name') ?>">
        </label>

        <label for="Email">
            Email:
            <input type="text" id="Email" name="Email" value="<?= old('Email') ?>">
        </label>

        <label for="Message">
            Message:
            <textarea id="Message" name="Message" placeholder="Write your message here..."><?= old('Message') ?></textarea>
        </label>

        <div class="form-buttons">
            <button type="submit" class="Submit-btn">Submit</button>
            <a href="index.php" class="Back-btn">Back</a>
        </div>
    </form>

    <?php unset($_SESSION['old']); ?>
    </body>

</html>