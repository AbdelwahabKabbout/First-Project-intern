<?php
session_start();

// Handle theme toggle
if (isset($_GET['toggle_theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] ?? 'light') === 'light' ? 'dark' : 'light';
    
    $redirect_url = 'index.php';
    
    $params = $_GET;
    unset($params['toggle_theme']);
    if (!empty($params)) {
        $redirect_url .= '?' . http_build_query($params);
    }
    
    header("Location: $redirect_url");
    exit;
}

// Get current theme
$currentTheme = $_SESSION['theme'] ?? 'light';
$isDarkMode = $currentTheme === 'dark';

// Handle deletion if delete_id is present
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    require_once 'config.php';
    
    $stmt = $conn->prepare("UPDATE guestbook SET Active=0 WHERE id=?");
    $stmt->bind_param("i", $delete_id);
    
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
}


error_log("GET parameters: " . print_r($_GET, true));
$successMsg = $_GET['success'] ?? '';
$deleteMsg = $_GET['deleted'] ?? '';


echo "<!-- DEBUG: successMsg = '" . htmlspecialchars($successMsg) . "' -->";
echo "<!-- DEBUG: deleteMsg = '" . htmlspecialchars($deleteMsg) . "' -->";
echo "<!-- DEBUG: GET = " . htmlspecialchars(print_r($_GET, true)) . " -->";
echo "<!-- DEBUG: Theme = '" . htmlspecialchars($currentTheme) . "' -->";


require_once 'Services.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="index.css">
</head>
<body<?php echo $isDarkMode ? ' class="dark-mode"' : ''; ?>>
    
    
    <a href="?toggle_theme=1<?php echo !empty($_GET) && !isset($_GET['toggle_theme']) ? '&' . http_build_query($_GET) : ''; ?>" 
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
            <?php 
            
            ReadEntriesForDisplay();
            ?>
        </div>

    </div>

</body>
</html>