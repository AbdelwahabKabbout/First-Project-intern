<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'Services.php';
require_once 'Dark-Light.php';

// Theme state (comes from Dark-Light.php)
$currentTheme = $_SESSION['theme'] ?? 'light';
$isDarkMode   = $currentTheme === 'dark';

// === Messages ===
$successMsg = $_GET['success'] ?? '';
$deleteMsg  = $_GET['deleted'] ?? '';
