<?php
session_start();

$errorMsg = $_GET['error'] ?? '';
$successMsg = $_GET['success'] ?? '';
$old = $_SESSION['old'] ?? [];

function old($key) {
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

<body>
    <button class="DarkLight" onclick="toggleDarkLightMode()">🌙 Dark</button>

    <h1>Welcome to Gbook</h1>

    <div id="message-container">
        <?php if ($errorMsg): ?>
            <div class="error-message"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>
        <?php if ($successMsg): ?>
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
        <textarea id="Message" class="Message" name="Message" placeholder="Write your message here..."><?= old('Message') ?></textarea>
    </label>

    <button type="submit" class="Submit-btn">Submit</button>
    <button type="button" class="Back-btn" onclick="window.location.href='index.php'">Back</button>
</form>


    <div class="back-link">
        <a href="index.php">← Back to Guestbook</a>
    </div>

    <script src="gbook-add.js"></script>
</body>
<?php unset($_SESSION['old']); ?>

</html>
