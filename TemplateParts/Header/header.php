<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Taxi</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Font Awesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    
    <link rel="stylesheet" href="<?php echo dirname($_SERVER['PHP_SELF']); ?>/Assets/Css/style.css">

    <?php 
        // Include the SessionManager
        include 'Functions/Common/SessionManager.php';
        // Start the session to check if the user is logged in
        SessionManager::startSession();
    ?>
    
</head>

<body>
    
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
        <!-- Brand Name -->
        <div class="d-flex align-items-center">
            <h1 class="navbar-brand mb-0">City Taxi</h1>
        </div>

        <!-- User Info and Actions -->
        <div class="d-flex align-items-center">
            <?php if (SessionManager::isLoggedIn()): ?>
                <!-- Welcome Message -->
                <div class="mr-3">
                    <h5 class="mb-0">Welcome, <?php echo SessionManager::get('first_name') . ' ' . SessionManager::get('last_name'); ?>!</h5>
                </div>
                <!-- Logout Button -->
                <a href="javascript:void(0);" onclick="confirmLogout();" class="btn btn-secondary btn-sm ml-3">Logout</a>
            <?php else: ?>
                <!-- Login Button -->
                <a href="login.php" class="btn btn-outline-secondary btn-sm ml-3">Login</a>
            <?php endif; ?>

            <!-- Ride With Call Button -->
            <a href="#" class="btn btn-warning btn-sm ml-3">Ride With Call</a>
        </div>
    </header>