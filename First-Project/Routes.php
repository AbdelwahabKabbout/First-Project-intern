<?php
session_start();
require 'Services.php';

$action = $_GET['action'] ?? '';

switch($action) {
   case 'Create':
    $result = CreateEntries(); // returns true or error string

    if ($result === true) {
        // Debug: check what's happening
        error_log("Create successful, redirecting to index.php?success=1");
        
        // Success → clear old input
        unset($_SESSION['old']);
        header("Location: index.php?success=Successfully-added");
        exit;
    } else {
        // Debug: check errors
        error_log("Create failed: " . $result);
        
        // Error → save old input and redirect back
        $_SESSION['old'] = $_POST; // store all submitted fields
        $error = urlencode($result);
        header("Location: gbook-add.php?error=$error");
        exit;
    }
    break;


    case 'Read':
        ReadEntries();
        break;

    case 'Update':
        UpdateEntries();
        break;

    case 'Delete':
        DeleteEntries();
        break;

    default:
       echo "Invalid route.";
       break;
}
?>