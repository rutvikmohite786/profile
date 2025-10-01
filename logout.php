<?php
/**
 * Logout Page
 * Topmate Clone - Core PHP Application
 */

require_once 'includes/auth.php';

// Logout user
Auth::logout();

// Redirect to home page
header('Location: index.php');
exit();
?>

