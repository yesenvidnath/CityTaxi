<?php
// Define the root path for the includes
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/'; 

include $rootPath . 'TemplateParts/Header/header.php'; 
include_once $rootPath . 'Functions/Passenger/Passenger.php';
include_once $rootPath . 'Functions/Driver/Driver.php';

// Start the session and get the user ID using SessionManager
SessionManager::startSession();
$userID = SessionManager::get('user_ID');

if (!SessionManager::isLoggedIn() || !$userID) {
    header("Location: /CityTaxi/login.php?status=error&message=Please log in first!");
    exit();
}

// Initialize Passenger class and get passenger details
$passenger = new Passenger();
$rideDetails = $passenger->getPassengerDetails($userID);

// Include the custom CSS for this page
echo '<link rel="stylesheet" href="/CityTaxi/Assets/Css/myrides.css">';
?>

<div class="container-fluid ride-container">
    <?php
    // Initialize the Driver class
    $driver = new Driver();

    if (!empty($rideDetails) && isset($rideDetails[2]) && !empty($rideDetails[2])): ?>
        <?php foreach ($rideDetails[2] as $ride): ?>
            <?php
            // Fetch the driver's details using the Driver ID
            $driverDetails = $driver->getDriverDetails($ride['Driver_ID']);
            $driverName = $driverDetails ? $driverDetails['First_name'] . ' ' . $driverDetails['Last_name'] : "Driver Not Found";
            ?>
            <div class="ride-card">
                <h3><?php echo "Driver: " . $driverName; ?></h3>
                <span class="distance-label">Distance: <?php echo $ride['Total_distance'] . "KM"; ?></span>
                <p>Pickup - <?php echo $ride['Start_Location']; ?></p>
                <p>Where To - <?php echo $ride['End_Location']; ?></p>
                <!-- <button class="accept-btn">Accept Ride</button> -->
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No rides found for this passenger.</p>
    <?php endif; ?>
    
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide elements with the class 'hide-this' on page load
        hideElementsBySelector('.hide-this');

        // Hide elements with the ID 'specific-element'
        hideElementsBySelector('#myprofile-hide-btn');
    });
</script>


<!-- Include the footer -->
<?php 
include $rootPath . 'TemplateParts/Passenger/PanelParts/menu.php'; 
include $rootPath . 'TemplateParts/Footer/footer.php'; 
?>
