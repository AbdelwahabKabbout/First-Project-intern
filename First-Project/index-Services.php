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

// ==================== Messages ====================
$successMsg = $_GET['success'] ?? '';
$errorMsg   = $_GET['error'] ?? '';
$deleteMsg  = $_GET['deleted'] ?? '';
$old        = $_SESSION['old'] ?? [];

function old($key)
{
    global $old;
    return htmlspecialchars($old[$key] ?? '');
}

// ==================== Pagination ====================
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

// ==================== View Selector ====================
$view = $_GET['view'] ?? 'main'; // 'main', 'add', 'edit'

// ==================== Handle Update ====================
$updateError = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    error_log("POST Request received: " . print_r($_POST, true));

    if (isset($_POST['update_entry'])) {
        error_log("Update entry request detected");

        // Make sure we have all required fields
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        error_log("Update data - ID: $id, Name: '$name', Email: '$email', Message: '$message'");

        $result = UpdateEntries();
        error_log("Update result: " . ($result === true ? "SUCCESS" : $result));

        if ($result === true) {
            header("Location: index.php?view=main&success=Successfully-updated");
            exit;
        } else {
            $updateError = $result;
            $view = 'edit';
        }
    } elseif (isset($_POST['create_entry'])) {

        $result = CreateEntries();
        if ($result === true) {
            unset($_SESSION['old']);
            header("Location: index.php?view=main&success=Successfully-added");
            exit;
        } else {
            $_SESSION['old'] = $_POST;
            header("Location: index.php?view=add&error=" . urlencode($result));
            exit;
        }
    }
}

// ==================== Edit Entry ====================
$entry = null;
if ($view === 'edit') {
    $entryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($entryId > 0) {
        $entry = GetEntry($entryId);
        if (!$entry) $updateError = "Entry not found or deleted.";
    } else {
        $updateError = "Invalid entry ID.";
    }
}

// ==================== Dynamic CSS Selection ====================
switch ($view) {
    case 'main':
        $cssFile = 'index.css';
        break;
    case 'add':
        $cssFile = 'gbook-add.css';
        break;
    case 'edit':
        $cssFile = 'gbook-edit.css';
        break;
    default:
        $cssFile = 'index.css';
}
