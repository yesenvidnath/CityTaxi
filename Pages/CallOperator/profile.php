<?php

// Define the root path for the includes
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/'; 

include $rootPath . 'TemplateParts/Header/header.php'; 
include_once $rootPath . 'Functions/Driver/Driver.php';
include_once $rootPath . 'Functions/Common/Rides.php'; 

// Retrieve user ID from session
$userID = SessionManager::get('user_ID'); 

if (!SessionManager::isLoggedIn() || !$userID) {
    header("Location: /CityTaxi/login.php?status=error&message=Please log in first!");
    exit();
}

// Fetch API keys from the .env file
$dotenv = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/env/.env');
$openRouteServiceApiKey = $dotenv['OpenRouteService_API_Key'];
$openCageApiKey = $dotenv['OpenCage_API_Key'];
$tomTomApiKey = $dotenv['TomTom_API_Key']; // Fetch TomTom API Key

// Initialize Driver class and get driver details and assigned rides
$driver = new Driver();
$driverInfo = $driver->getDriverDetailsByUserID($userID);
$assignedRides = $driver->getAssignedRides($driverInfo['Driver_ID']); // Use the Driver_ID from driverInfo

// Assign driver info to variables for easy use
$Driver_ID = $driverInfo['Driver_ID'] ?? 'N/A';
$driverFirstName = $driverInfo['First_name'] ?? 'N/A';
$driverLastName = $driverInfo['Last_name'] ?? 'N/A';
$driverMobile = $driverInfo['mobile_number'] ?? 'N/A';
$driverEmail = $driverInfo['Email'] ?? 'N/A';
$driverLocation = $driverInfo['Current_Location'] ?? '0,0'; 
$userImage = $driverInfo['user_img'] ?? 'default.jpg'; 

// Construct the image path
$imagePath = "/CityTaxi/Assets/Img/Driver/" . $userImage;

// Debug: Check session contents
error_log("Session Contents: " . print_r($_SESSION, true));

// Create an instance of the Ride class
$ride = new Ride(); // Ensure the Ride class is instantiated

// Get driver's availability
$availabilityInfo = $ride->getDriverAvailability($driverInfo['Driver_ID']); // Use the Driver_ID

if ($availabilityInfo) {
    $availabilityText = $availabilityInfo['Availability'] == 1 ? "Available" : "Unavailable";
} else {
    $availabilityText = "Status Unknown";
}

?>
<div class="wrapper-container">
    <div class="card profile-card mb-4">
        <div class="profile-cover"></div>
        <div class="profile-header">
            <div class="profile-avatar-wrapper">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                    <div class="status-indicator"></div>
                </div>
            </div>
            <h2 class="profile-name mb-2"><?php echo $driverFirstName . ' ' . $driverLastName; ?></h2>
            <div class="driver-rating mb-2">
                <i class="fas fa-star"></i>
                <span>4.95</span>
            </div>
            <div class="status-pill mb-3" id="driverAvailabilityStatus">
                <?php echo $availabilityText; ?>
            </div>
            <div class="contact-info">
                <div class="info-item">
                    <i class="fas fa-phone-alt"></i>
                    <span><?php echo $driverMobile; ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <span><?php echo $driverEmail; ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span id="driverLocation"><?php echo $driverLocation; ?></span>
                </div>
            </div>
            <button class="btn btn-apple mt-4" id="changeAvailabilityBtn" data-driver-id="<?php echo $driverInfo['Driver_ID']; ?>">
                <i class="fas fa-power-off me-2"></i>Toggle Availability
            </button>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-route"></i>
                </div>
                <!-- <div class="stat-value">1,234</div> -->
                <div class="stat-label">Total Trips</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <!-- <div class="stat-value">2y 3m</div> -->
                <div class="stat-label">Experience</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-medal"></i>
                </div>
                <!-- <div class="stat-value">92%</div> -->
                <div class="stat-label">Acceptance Rate</div>
            </div>
        </div>
    </div>

    <!-- Active Rides -->
    <div class="card rides-card mb-4">
        <div class="card-header">
            <h4>Current Rides</h4>
            <span class="badge bg-primary rides-counter">
                <?php echo count($assignedRides); ?> Active
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <?php if (!empty($assignedRides)): ?>
                    <?php foreach ($assignedRides as $ride): ?>
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="ride-card">
                                <div class="ride-header">
                                    <div class="ride-id">Ride #<?php echo $ride['Ride_ID']; ?></div>
                                    <span class="ride-status"><?php echo $ride['Status']; ?></span>
                                </div>
                                <div class="ride-route">
                                    <div class="route-point">
                                        <div class="point-marker pickup"></div>
                                        <div class="point-details">
                                            <label>Pickup</label>
                                            <p><?php echo $ride['Start_Location']; ?></p>
                                        </div>
                                    </div>
                                    <div class="route-line"></div>
                                    <div class="route-point">
                                        <div class="point-marker dropoff"></div>
                                        <div class="point-details">
                                            <label>Dropoff</label>
                                            <p><?php echo $ride['End_Location']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="ride-footer">
                                    <div class="passenger-info">
                                        <i class="fas fa-user-circle"></i>
                                        <span><?php echo $ride['Passenger_ID']; ?></span>
                                    </div>
                                    <div class="ride-amount">
                                        <i class="fas fa-dollar-sign"></i>
                                        <span><?php echo $ride['Amount']; ?></span>
                                    </div>
                                </div>
                                <?php if ($ride['Status'] === 'Accepted'): ?>
                                    <button class="btn btn-apple w-100 mt-3 finish-ride" 
                                        data-driver-id="<?php echo $driverInfo['Driver_ID']; ?>"
                                        data-ride-id="<?php echo $ride['Ride_ID']; ?>"
                                        data-passenger-id="<?php echo $ride['Passenger_ID']; ?>"
                                        data-amount="<?php echo $ride['Amount']; ?>"
                                        data-taxi-id="<?php echo $ride['Taxi_ID']; ?>">
                                        <i class="fas fa-check-circle me-2"></i>Complete Ride
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="no-rides">
                            <i class="fas fa-car-side"></i>
                            <p>No active rides at the moment</p>
                            <span>New rides will appear here</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Location Update Section -->
    <div class="card location-card mb-4">
        <div class="card-header">
            <h4>Update Location</h4>
        </div>
        <div class="card-body">
            <div class="location-input-group">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" id="startLocation" placeholder="Enter your location">
                    <ul id="startLocationList" class="autocomplete-list"></ul>
                </div>
                <div class="location-actions">
                    <button class="btn btn-apple" id="getCurrentLocationBtn">
                        <i class="fas fa-location-arrow"></i>
                        <span class="d-none d-sm-inline">Current Location</span>
                    </button>
                    <button class="btn btn-apple" id="updateLocationBtn">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="d-none d-sm-inline">Update</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

 
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide elements with the class 'hide-this' on page load
        hideElementsBySelector('.hide-this');

        // Hide elements with the ID 'specific-element'
        hideElementsBySelector('#myprofile-hide-btn');
    });

    // Pass PHP variables to JavaScript
    const openCageApiKey = "<?php echo $openCageApiKey; ?>";
    const tomTomApiKey = "<?php echo $tomTomApiKey; ?>";

 
    // document.addEventListener('DOMContentLoaded', function() {
    //     const driverID = '<?php //echo $Driver_ID; ?>';  
    // });

</script>


<?php 
    include $rootPath . 'TemplateParts/Passenger/PanelParts/menu.php';
    include $rootPath . 'TemplateParts/Footer/footer.php'; 
?>