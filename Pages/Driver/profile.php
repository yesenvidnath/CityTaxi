<?php

    // Define the root path for the includes
    $rootPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/'; 

    include $rootPath . 'TemplateParts/Header/header.php'; 
    include_once $rootPath . 'Functions/Driver/Driver.php';

    // Attempt to include Alerts.php and check for existence
    if (file_exists($rootPath . 'Functions/Driver/Alerts.php')) {
        include_once $rootPath . 'Functions/Driver/Alerts.php';
    } else {
        die("Error: Alerts.php file not found.");
    }

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

?>


<div class="container profile-page">
    <div class="row">
        <!-- Driver Information Column -->
        <div class="col-lg-6 profile-card text-center">
            <h2><?php echo $driverFirstName . ' ' . $driverLastName; ?></h2>
            <div class="contact-info">
                <p>Mobile Number: <?php echo $driverMobile; ?></p>
                <p>Email Address: <?php echo $driverEmail; ?></p>
                <p>Current Location: <?php echo $driverLocation; ?></p>
            </div>
        </div>

        <!-- Rides Information Column -->
        <div class="col-lg-6 rides-card">
            <h3>Assigned Rides</h3>
            <ul class="list-group">
                <?php foreach ($assignedRides as $ride): ?>
                    <li class="list-group-item">
                        <strong>Ride ID:</strong> <?php echo $ride['Ride_ID']; ?><br>
                        <strong>Start Location:</strong> <?php echo $ride['Start_Location']; ?><br>
                        <strong>End Location:</strong> <?php echo $ride['End_Location']; ?><br>
                        <strong>Status:</strong> <?php echo $ride['Status']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    
    <!-- Alerts Section -->
    <div class="row">
        <div class="col-lg-12">
            <h3>Alerts</h3>
            <ul class="list-group">
                <?php if (!empty($alerts)): ?>
                    <?php foreach ($alerts as $alert): ?>
                        <li class="list-group-item">
                            <?php echo $alert; ?>
                        </li>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['alerts']); // Clear alerts after displaying ?>
                <?php else: ?>
                    <li class="list-group-item">No new alerts.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>


    <!-- Map Section -->
    <div class="map-info">
        <div id="map" style="width: 100%; height: 400px;"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide elements with the class 'hide-this' on page load
        hideElementsBySelector('.hide-this');

        // Hide elements with the ID 'specific-element'
        hideElementsBySelector('#myprofile-hide-btn');
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the map using TomTom's base layer
        var driverLocationArray = "<?php echo $driverLocation; ?>".split(',');
        var map = L.map('map').setView([parseFloat(driverLocationArray[0]), parseFloat(driverLocationArray[1])], 15); // Centered on driver's location

        // Set up the TomTom layer with the language set to English (en-GB)
        L.tileLayer(`https://api.tomtom.com/map/1/tile/basic/main/{z}/{x}/{y}.png?key=<?php echo $tomTomApiKey; ?>&language=en-GB`, {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.tomtom.com/copyright">TomTom</a>'
        }).addTo(map);

        // Add a marker for the driver's current location
        L.marker([parseFloat(driverLocationArray[0]), parseFloat(driverLocationArray[1])]).addTo(map)
            .bindPopup('Driver Location: <?php echo $driverFirstName . " " . $driverLastName; ?>')
            .openPopup();
    });


    // WebSocket connection code
    const driverID = '<?php echo $Driver_ID; ?>';

    const socket = new WebSocket(`ws://localhost:8080/ws?driverID=${driverID}`); // Pass Driver ID as a query parameter

    socket.onopen = function() {
        console.log('Connected to WebSocket server as Driver ID: ' + driverID);
    };

    socket.onmessage = function(event) {
        const response = JSON.parse(event.data);
        console.log('Received response:', response); // Debugging

        if (response.status === 'rideOffer') {
            // Display SweetAlert with ride details and custom buttons
            swal({
                title: "New Ride Available",
                text: response.message,
                icon: "info",
                buttons: {
                    accept: {
                        text: "Accept",
                        value: "accept",
                    },
                    reject: {
                        text: "Reject",
                        value: "reject",
                    }
                },
                dangerMode: true // Optional: Style it to indicate danger for reject
            }).then((value) => {
                if (value === "accept") {
                    // Send acceptance back to the server
                    socket.send(JSON.stringify({
                        action: 'acceptRide',
                        driverID: driverID,
                        rideDetails: response.rideDetails // Send the accepted ride details
                    }));
                } else {
                    // Send rejection back to the server
                    socket.send(JSON.stringify({
                        action: 'rejectRide',
                        driverID: driverID,
                        rideDetails: response.rideDetails
                    }));
                }
            });
        } else if (response.status === 'confirmed') {
            // Handle confirmation from the server
            swal("Booking Confirmed!", response.message, "success");
        } else if (response.status === 'rejected') {
            // Handle rejection message
            swal("All drivers are busy. Please select another vehicle type.", "", "error");
        }
    };

    socket.onerror = function(error) {
        console.error('WebSocket Error:', error);
    };

</script>

<?php 
    include $rootPath . 'TemplateParts/Passenger/PanelParts/menu.php';
    include $rootPath . 'TemplateParts/Footer/footer.php'; 
?>
