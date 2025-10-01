<?php
/**
 * Header Template
 * Topmate Clone - Core PHP Application
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Topmate Clone</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- Meta Tags -->
    <meta name="description" content="Connect with industry experts and book 1-on-1 mentoring sessions">
    <meta name="keywords" content="mentoring, experts, booking, professional development">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">Topmate</a>
                
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    
                    <?php if (Auth::isLoggedIn()): ?>
                        <?php if (Auth::isAdmin()): ?>
                            <li><a href="admin.php">Admin Panel</a></li>
                        <?php endif; ?>
                        <li><a href="profile.php">My Profile</a></li>
                        <li><a href="my-bookings.php">My Bookings</a></li>
                        <li>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <span style="color: var(--text-secondary);">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                                <a href="logout.php" class="btn btn-secondary btn-sm">Logout</a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li><a href="register.php" class="btn btn-primary btn-sm">Sign Up</a></li>
                        <li><a href="login.php" class="btn btn-secondary btn-sm">Sign In</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>

