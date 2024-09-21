<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Taxi</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="/CityTaxi/Assets/Css/style.css">

    <?php 
        $rootPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/'; 
        
        include $rootPath . 'Functions/Common/PathHandler.php'; 
        showLinkOnProfilePage('<link rel="stylesheet" href="/CityTaxi/Assets/Css/profile.css">');
        showLinkOnMyRides('<link rel="stylesheet" href="/CityTaxi/Assets/Css/myrides.css">');
        showLinkOnRide('<link rel="stylesheet" href="/CityTaxi/Assets/Css/ride.css">');

        // Include the SessionManager
        include $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/Functions/Common/SessionManager.php';
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
                <a href="Pages/Passenger/profile.php" class="btn btn-secondary btn-sm ml-3 " id="myprofile-hide-btn">Visit My Profile</a>
                <!-- Logout Button -->
                <a href="javascript:void(0);" onclick="confirmLogout();" class="btn btn-outline-secondary btn-sm ml-3">Logout</a>
            <?php else: ?>
                <!-- Login Button -->
                <a href="login.php" class="btn btn-outline-secondary btn-sm ml-3">Login</a>
            <?php endif; ?>

            <!-- Ride With Call Button -->
            <a href="#" class="btn btn-warning btn-sm ml-3">Ride With Call</a>
        </div>
    </header>