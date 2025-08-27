<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_GET['toggle_theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] ?? 'light') === 'light' ? 'dark' : 'light';


    $redirect_url = $_GET['redirect'] ?? 'index.php';
    header("Location: $redirect_url");
    exit;
}


$currentTheme = $_SESSION['theme'] ?? 'light';
$isDarkMode   = $currentTheme === 'dark';
