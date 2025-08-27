<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle theme toggle
if (isset($_GET['toggle_theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] ?? 'light') === 'light' ? 'dark' : 'light';

    // Redirect back to page specified in 'redirect' parameter, or index.php by default
    $redirect_url = $_GET['redirect'] ?? 'index.php';
    header("Location: $redirect_url");
    exit;
}

// Current theme
$currentTheme = $_SESSION['theme'] ?? 'light';
$isDarkMode   = $currentTheme === 'dark';
