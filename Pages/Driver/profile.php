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

<div class="container py-4">
    <!-- Profile Header -->
    <div class="card mb-4">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h2 class="mb-2"><?php echo $driverFirstName . ' ' . $driverLastName; ?></h2>
            <div class="status-pill mb-3" id="driverAvailabilityStatus">
                <?php echo $availabilityText; ?>
            </div>
            <div class="contact-info text-start">
                <p><i class="fas fa-phone-alt"></i> <?php echo $driverMobile; ?></p>
                <p><i class="fas fa-envelope"></i> <?php echo $driverEmail; ?></p>
                <p><i class="fas fa-map-marker-alt"></i> <span id="driverLocation"><?php echo $driverLocation; ?></span></p>
            </div>
            <button class="btn btn-apple mt-3" id="changeAvailabilityBtn" data-driver-id="<?php echo $driverInfo['Driver_ID']; ?>">
                Toggle Availability
            </button>
        </div>
        
        <!-- Stats Section
        <div class="stats-container px-4 pb-4">
            <div class="stat-card">
                <div class="stat-value">4.9</div>
                <div class="stat-label">Rating</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">1,234</div>
                <div class="stat-label">Trips</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">2y 3m</div>
                <div class="stat-label">Experience</div>
            </div>
        </div> -->
    </div>

    <!-- Active Rides -->
    <div class="card mb-4 p-4">
        <h4 class="mb-3">Current Rides</h4>
        <div class="row">
            <?php if (!empty($assignedRides)): ?>
                <?php foreach ($assignedRides as $ride): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card ride-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Ride #<?php echo $ride['Ride_ID']; ?></h5>
                                    <span class="badge bg-primary rounded-pill"><?php echo $ride['Status']; ?></span>
                                </div>
                                <div class="ride-details">
                                    <p class="mb-2"><i class="fas fa-user text-primary"></i> <?php echo $ride['Passenger_ID']; ?></p>
                                    <p class="mb-2"><i class="fas fa-map-marker-alt text-success"></i> <?php echo $ride['Start_Location']; ?></p>
                                    <p class="mb-2"><i class="fas fa-location-arrow text-danger"></i> <?php echo $ride['End_Location']; ?></p>
                                    <p class="mb-2"><i class="fas fa-dollar-sign text-primary"></i> <?php echo $ride['Amount']; ?></p>
                                </div>
                                <?php if ($ride['Status'] === 'Accepted'): ?>
                                    <button class="btn btn-apple w-100 mt-3 finish-ride" 
                                        data-driver-id="<?php echo $driverInfo['Driver_ID']; ?>"
                                        data-ride-id="<?php echo $ride['Ride_ID']; ?>"
                                        data-passenger-id="<?php echo $ride['Passenger_ID']; ?>"
                                        data-amount="<?php echo $ride['Amount']; ?>"
                                        data-taxi-id="<?php echo $ride['Taxi_ID']; ?>">
                                        Complete Ride
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-car-side fa-3x mb-3"></i>
                        <p>No active rides at the moment</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <!-- Location Update Section -->
    <div class="card mb-4 p-4">
        <h4 class="mb-3">Update Location</h4>
        <div class="input-group">
            <input type="text" class="form-control rounded-pill" id="startLocation" placeholder="Enter location">
            <ul id="startLocationList" class="autocomplete-list"></ul>
            <button class="btn btn-apple ms-2" id="getCurrentLocationBtn">
                <i class="fas fa-location-arrow"></i> Current Location
            </button>
            <button class="btn btn-apple ms-2" id="updateLocationBtn">
                <i class="fas fa-map-marker-alt"></i> Update Location
            </button>
        </div>
    </div>


    <!-- Map Section -->
    <div class="card">
        <div id="map" style="width: 100%; height: 400px; "></div>    
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

    const driverID = '<?php echo $Driver_ID; ?>';
    const driverName = '<?php echo $driverFirstName . ' ' . $driverLastName; ?>';
    const driverLocation = '<?php echo $driverLocation; ?>';
    const driverMobile = '<?php echo $driverMobile; ?>';

    const socket = new WebSocket(`ws://localhost:8080/ws?driverID=${driverID}`);

    socket.onopen = function() {
        console.log('Connected to WebSocket server as Driver ID: ' + driverID);
    };

 
    // document.addEventListener('DOMContentLoaded', function() {
    //     const driverID = '<?php //echo $Driver_ID; ?>';  
    // });

</script>


<?php 
    include $rootPath . 'TemplateParts/Passenger/PanelParts/menu.php';
    include $rootPath . 'TemplateParts/Footer/footer.php'; 
?>