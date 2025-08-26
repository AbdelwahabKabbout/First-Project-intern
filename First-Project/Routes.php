<?php
require 'Services.php';

$action = $_GET['action'] ?? '';

switch($action) {
    case 'Create':
        CreateEntries();
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