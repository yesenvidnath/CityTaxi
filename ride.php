<?php 
// Include the header part
include 'TemplateParts/Header/header.php'; 

// Fetch API keys from the .env file
$dotenv = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/env/.env');
$openRouteServiceApiKey = $dotenv['OpenRouteService_API_Key'];
$openCageApiKey = $dotenv['OpenCage_API_Key'];
$tomTomApiKey = $dotenv['TomTom_API_Key']; // Fetch TomTom API Key

// Include the ride functions
include_once 'Functions/Common/Ride.php';
include_once 'Functions/Passenger/Passenger.php';

// Fetch the taxi types and rates
$taxiTypes = getTaxiTypes();
$taxiRates = getTaxiRatesByType();

// Fetch available drivers
$availableDrivers = getAvailableDrivers();

// Map the rates to taxi types for easy access
$taxiRatesMap = [];
foreach ($taxiRates as $rate) {
    $taxiRatesMap[$rate['Taxi_type']] = $rate;
}

// Start the session and get the user ID using SessionManager
SessionManager::startSession();
// Retrieve user ID from session
$userID = SessionManager::get('user_ID'); 
if (!SessionManager::isLoggedIn() || !$userID) {
    header("Location: /CityTaxi/login.php?status=error&message=Please log in first!");
    exit();
}

// Fetch user information
$user = new Users(); // Assuming you have a Users class to handle user operations
$userInfo = $user->fetchUserByID($userID); // Fetch user information

// Store user information in variables
$PassengeruserID = $userInfo['user_ID'] ?? '';
$firstName = $userInfo['First_name'] ?? '';
$lastName = $userInfo['Last_name'] ?? '';
$mobileNumber = $userInfo['mobile_number'] ?? '';

// Check if the ride has been accepted
$rideAccepted = isset($_SESSION['ride_accepted']) ? $_SESSION['ride_accepted'] : false;

?>

<!-- Main content -->
<section class="ride-info-section">
    <div class="driver-info" id="step1">
        
        <h2>Buckle Up For The Ride</h2>
        <div class="form-group" id="manualLocationSection">
            <label for="startLocation"> Start Location</label>
            <input type="text" class="form-control" id="startLocation" placeholder="Enter Start Location">
            <ul id="startLocationList" class="autocomplete-list"></ul>
            <button class="btn btn-info" onclick="useTomTomLocation()"><i class="fas fa-location-arrow"></i> Use Current Location</button>
        </div>
        <div class="form-group">
            <label for="endLocation">End Location</label>
            <input type="text" class="form-control" id="endLocation" placeholder="Enter End Location">
            <ul id="endLocationList" class="autocomplete-list"></ul> 
        </div>
        <button class="btn btn-warning" onclick="showRoute()"><i class="fas fa-route"></i> Show Route</button>
        <button class="btn btn-success" id="confirmRouteBtn" style="display: none;" onclick="confirmRoute()"> <i class="fas fa-check"></i> Confirm Route</button>
    </div>
    

    <div class="map-info">
        <div id="map" ></div>
        <div id="details">
            <h4>Route Details</h4>
            <p id="routeDetails"></p>
        </div>
    </div>


    <div class="fixed-drag-handle"></div>

    <section class="bottom-content-section">
        <div class="drag-handle"></div>

        <div class="taxi-selection-section" id="step2" style="display: none;">
            
            <h2>Select Your Taxi Type</h2>
            <div class="row">
                <?php
                $displayedTypes = [];
                foreach ($taxiTypes as $taxi):
                    if (!in_array($taxi['Taxi_type'], $displayedTypes)):
                        $displayedTypes[] = $taxi['Taxi_type'];
                        $ratePerKM = isset($taxiRatesMap[$taxi['Taxi_type']]) ? $taxiRatesMap[$taxi['Taxi_type']]['Rate_per_Km'] : 'N/A';
                ?>
                    <div class="col-lg-4">
                        <div class="taxi-card">
                            <img src="Assets/img/taxis/<?php echo $taxi['Taxi_type'] == 'Car' ? 'car.png' : $taxi['Vehicle_Img']; ?>" alt="<?php echo $taxi['Taxi_type']; ?>" class="img-fluid">
                            <h3><?php echo $taxi['Taxi_type']; ?></h3>
                            <p>Rate per KM: <?php echo $ratePerKM; ?></p>
                            <p>Total Price: <span id="price-<?php echo $taxi['Taxi_type']; ?>"></span></p>
                            <button class="btn btn-warning" onclick="selectTaxiType('<?php echo $taxi['Taxi_type']; ?>')">Select</button>
                        </div>
                    </div>
                <?php
                    endif;
                endforeach;
                ?>
            </div>
            <button class="btn btn-danger" id="changeRouteBtn" onclick="confirmChangeRoute()">Change Route</button>
        </div>

        <div class="selection-summary-section" id="step3" style="display: none;">
            <!-- <h2 class="text-center">Your Selection</h2>
            <p id="selectionDetails" class="text-center"></p> -->
            
            <h3 class="text-center">Available Drivers</h3>
            <div id="driverList" class="driver-list">
                <?php foreach ($availableDrivers as $driver): ?>
                    <div class="driver-card card mb-4">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $driver['First_name'] . ' ' . $driver['Last_name']; ?></h4>
                            <p class="card-text">Location: <?php echo $driver['Current_Location']; ?></p>
                            <p class="card-text">Vehicle Type: <?php echo $driver['Taxi_type']; ?></p>
                            <!-- <button class="btn btn-success" onclick="confirmDriverSelection('<?php //echo $driver['Driver_ID']; ?>')">Select Driver</button> -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-4" id="bookingButtons" style="<?php echo $rideAccepted ? 'display:none;' : ''; ?>">
                <button class="btn btn-primary" onclick="confirmBooking()">Confirm Booking</button>
                <button class="btn btn-secondary" onclick="changeVehicleType()">Change Vehicle Type</button>
            </div>

            <!-- Display Visit Your Profile button when ride is accepted -->
            <div class="text-center mt-4" id="visitProfileButton" style="<?php echo $rideAccepted ? 'display:block;' : 'display:none;'; ?>">
                <a href="/CityTaxi/Pages/Driver/profile.php" class="btn btn-info">Visit Your Profile</a>
            </div>

        </div>

    </section>


</section>

<!-- Display SweetAlert alerts if status is passed -->
<?php 
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $message = htmlspecialchars($_GET['message']);
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showAlert('$status', '$message');
            });
        </script>
        ";
    }
?>



<script>

    const openRouteServiceApiKey = '<?php echo $openRouteServiceApiKey; ?>';
    const openCageApiKey = '<?php echo $openCageApiKey; ?>';
    const tomTomApiKey = '<?php echo $tomTomApiKey; ?>';
    var totalDistance = 0; // Global variable to store the total distance
    

    // Function to update the taxi prices based on the total distance
    function updateTaxiPrices() {
        <?php foreach ($taxiRates as $rate): ?>
            var ratePerKM = <?php echo $rate['Rate_per_Km']; ?>;
            var totalPrice = (totalDistance * ratePerKM).toFixed(2);
            document.getElementById('price-<?php echo $rate['Taxi_type']; ?>').textContent = totalPrice + ' LKR';
        <?php endforeach; ?>
    }

    function confirmBooking() {
        // Store user information and trip details in JavaScript variables
        const startLocation = startLocationValue; // Use the global variable for start location
        const endLocation = document.getElementById('endLocation').value; // Get the end location from the input field
        const tripPrice = document.getElementById(`price-${selectedVehicleType}`).textContent; // Get the price displayed
        const rideID = generateUniqueRideID(); // You may want to implement a function to create a unique ride ID

        const userInfo = {
            PassengerUserID: '<?php echo $PassengeruserID; ?>',
            firstName: '<?php echo $firstName; ?>',
            lastName: '<?php echo $lastName; ?>',
            mobileNumber: '<?php echo $mobileNumber; ?>',
            startLocation: startLocation,
            endLocation: endLocation,
            tripPrice: tripPrice,
            rideID: rideID, // Include rideID
            driverIDs: window.driverIDs, // Add the driver IDs to the user info
            totalDistance: totalDistance // Include the total distance
        };

        // Display the user information and trip details in the console
        console.log('User Information and Trip Details:', userInfo);
        console.log('Available Driver IDs:', userInfo.driverIDs); // Log the driver IDs

        // Change alert to a waiting message
        swal("Waiting for Driver Response...", "", "info");

        // Send ride details to drivers via WebSocket
        const socket = new WebSocket('ws://localhost:8080/ws');
        socket.onopen = function() {
            userInfo.driverIDs.forEach(driverID => {
                const message = "A new ride has been booked. From " + startLocation + " to " + endLocation + " for a price of " + tripPrice + ". Total distance: " + totalDistance.toFixed(2) + " km. Do you accept this ride?";
                socket.send(JSON.stringify({
                    action: 'sendMessage',
                    driverID: driverID,
                    rideDetails: userInfo // Send complete ride details including rideID and totalDistance
                }));
            });
        };

        socket.onmessage = function(event) {
            const response = JSON.parse(event.data);
            if (response.status === 'rideOffer') {
                // Display SweetAlert for driver confirmation
                swal({
                    title: "New Ride Request",
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
                    dangerMode: true // Optional: to style the reject button
                }).then((value) => {
                    if (value === "accept") {
                        socket.send(JSON.stringify({
                            action: 'acceptRide',
                            driverID: driverID,
                            rideDetails: response.rideDetails // Send accepted ride details
                        }));
                    } else {
                        socket.send(JSON.stringify({
                            action: 'rejectRide',
                            driverID: driverID,
                            rideDetails: response.rideDetails
                        }));
                    }
                });
            } else if (response.status === 'confirmed') {
                console.log(`Driver Response: ${response.message}`);
                swal("Booking Confirmed!", response.message, "success");
                // Hide the booking buttons and show the profile button
                document.getElementById('bookingButtons').style.display = 'none';
                document.getElementById('visitProfileButton').style.display = 'block';

            } else if (response.status === 'rejected') {
                swal("Driver has rejected the ride. Please try another vehicle type.", "", "error");
            }
        };
    }

    function generateUniqueRideID() {
        return 'ride-' + Date.now(); // Simple unique ID based on current timestamp
    }


    // Variables to track drag state
    let isDragging = false;
    let startY = 0;
    let currentY = 0;
    let bottomSection = document.querySelector('.bottom-content-section');
    let dragHandle = document.querySelector('.drag-handle');
    let fixedDragHandle = document.querySelector('.fixed-drag-handle');
    let threshold = 50; // The distance threshold for showing/hiding

    // Function to show the section
    function showSection() {
        bottomSection.classList.remove('hidden');
        bottomSection.classList.add('visible');
        fixedDragHandle.style.display = 'none'; // Hide fixed handle when section is visible
    }

    // Function to hide the section
    function hideSection() {
        bottomSection.classList.add('hidden');
        bottomSection.classList.remove('visible');
        fixedDragHandle.style.display = 'block'; // Show fixed handle when section is hidden
    }

    // Reset drag state after drag is complete
    function resetDragState() {
        isDragging = false;
        startY = 0;
        currentY = 0;
    }

    // Function to handle the drag start
    function startDrag(event) {
        isDragging = true;
        startY = event.touches ? event.touches[0].clientY : event.clientY; // Track starting Y position for both touch and mouse
    }

    // Function to handle the drag move
    function onDrag(event) {
        if (!isDragging) return;
        currentY = event.touches ? event.touches[0].clientY : event.clientY;

        // Detect if the user is dragging down or up
        if (currentY - startY > threshold) {
        // User drags down - hide section
        hideSection();
        } else if (startY - currentY > threshold) {
        // User drags up - show section
        showSection();
        }
    }

    // Attach event listeners for both touch and mouse events
    dragHandle.addEventListener('touchstart', startDrag);
    dragHandle.addEventListener('mousedown', startDrag);
    fixedDragHandle.addEventListener('touchstart', startDrag);
    fixedDragHandle.addEventListener('mousedown', startDrag);

    document.addEventListener('touchmove', onDrag);
    document.addEventListener('mousemove', onDrag);

    document.addEventListener('touchend', resetDragState);
    document.addEventListener('mouseup', resetDragState);
</script>


<?php 
    include 'TemplateParts/Shared/NavMenu.php'; 
    include 'TemplateParts/Footer/footer.php';   
?>