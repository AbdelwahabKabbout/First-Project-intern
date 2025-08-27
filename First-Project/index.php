<?php
// Include all the logic from index-Services.php
require_once 'index-Services.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guestbook</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($cssFile) ?>">
</head>

<body<?php echo $isDarkMode ? ' class="dark-mode"' : ''; ?>>

    <!-- Theme toggle -->
    <a href="Dark-Light.php?toggle_theme=1&redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"
        class="DarkLight<?php echo $isDarkMode ? ' dark' : ''; ?>">
        <?php echo $isDarkMode ? '☀️ Light' : '🌙 Dark'; ?>
    </a>

    <!-- ================= Main Page ================= -->
    <?php if ($view === 'main'): ?>
        <h1>Welcome to Gbook</h1>
        <a href="index.php?view=add" class="Add<?php echo $isDarkMode ? ' dark' : ''; ?>">Make a message!</a>

        <div class="MessageContainer">
            <?php if ($successMsg === 'Successfully-added'): ?>
                <div class="success-message">✅ Entry created successfully!</div>
            <?php endif; ?>
            <?php if ($successMsg === 'Successfully-updated'): ?>
                <div class="success-message">📝 Entry updated successfully!</div>
            <?php endif; ?>
            <?php if ($deleteMsg === 'Successfully-deleted'): ?>
                <div class="success-message">🗑️ Entry deleted successfully!</div>
            <?php endif; ?>
            <?php if ($deleteMsg === 'Delete-failed'): ?>
                <div class="error-message">❌ Failed to delete entry!</div>
            <?php endif; ?>
        </div>

        <div class="entries-container">
            <?php ReadEntriesForDisplay($page); ?>
        </div>
    <?php endif; ?>

    <!-- ================= Add Page ================= -->
    <?php if ($view === 'add'): ?>
        <h1>Add New Entry</h1>

        <?php if ($errorMsg): ?>
            <div class="error-message"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?view=add">
            <input type="hidden" name="create_entry" value="1">
            <label for="Name">Name:
                <input type="text" id="Name" name="Name" value="<?= old('Name') ?>">
            </label>
            <label for="Email">Email:
                <input type="text" id="Email" name="Email" value="<?= old('Email') ?>">
            </label>
            <label for="Message">Message:
                <textarea id="Message" name="Message" placeholder="Write your message here..."><?= old('Message') ?></textarea>
            </label>

            <div class="form-buttons">
                <button type="submit" class="Submit-btn">Submit</button>
                <a href="index.php?view=main" class="Back-btn">Back</a>
            </div>
        </form>
        <?php unset($_SESSION['old']); ?>
    <?php endif; ?>

    <!-- ================= Edit Page ================= -->
    <?php if ($view === 'edit'): ?>
        <h1>Edit Entry</h1>

        <?php if ($updateError): ?>
            <div class="error-message"><?= htmlspecialchars($updateError) ?></div>
        <?php endif; ?>

        <?php if (!$entry): ?>
            <div class="error-message">Entry not found or invalid ID.</div>
            <div class="form-buttons">
                <a href="index.php?view=main" class="Back-btn">Back to Main</a>
            </div>
        <?php else: ?>
            <form method="POST" action="index.php?view=edit&id=<?= $entry['id'] ?>">
                <input type="hidden" name="update_entry" value="1">
                <input type="hidden" name="id" value="<?= $entry['id'] ?>">

                <label for="name">Name:
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($entry['name']) ?>" required>
                </label>

                <label for="email">Email:
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($entry['email']) ?>" required>
                </label>

                <label for="message">Message:
                    <textarea id="message" name="message" required><?= htmlspecialchars($entry['message']) ?></textarea>
                </label>

                <div class="form-buttons">
                    <button type="submit" class="Submit-btn">Update Entry</button>
                    <a href="index.php?view=main" class="Back-btn">Back</a>
                </div>
            </form>
        <?php endif; ?>
    <?php endif; ?>

    </body>

</html>