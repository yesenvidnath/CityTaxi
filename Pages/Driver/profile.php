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

        <!-- Driver Availability Section -->
        <div class="col-lg-12">
            <h3>Driver Availability</h3>
            <p id="driverAvailability" class="availability-status">
                <?php echo $availabilityText; ?>
            </p>
        </div>

        <!-- Rides Information Column -->
        <div class="col-lg-6 rides-card">
        <h3>Assigned Rides</h3>
        
        <div class="row">

            <?php foreach ($assignedRides as $ride): ?>
                <div class="col-lg-6">
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="card-title">Ride ID: <?php echo $ride['Ride_ID']; ?></h5>
                            <h5 class="card-title">Passenger ID: <?php echo $ride['Passenger_ID']; ?></h5>
                            <p class="card-text"><strong>Start Location:</strong> <?php echo $ride['Start_Location']; ?></p>
                            <p class="card-text"><strong>End Location:</strong> <?php echo $ride['End_Location']; ?></p>
                            <p class="card-text"><strong>Total amount :</strong> <?php echo $ride['Amount']; ?></p>
                            <p class="card-text"><strong>Status:</strong> <?php echo $ride['Status']; ?></p>
                            
                            <!-- Add the Taxi_ID as a data attribute to the button -->
                            <?php if ($ride['Status'] === 'Accepted'): ?>
                                <button class="btn btn-danger finish-ride" 
                                        data-driver-id="<?php echo $driverInfo['Driver_ID']; ?>"
                                        data-ride-id="<?php echo $ride['Ride_ID']; ?>"
                                        data-passenger-id="<?php echo $ride['Passenger_ID']; ?>"
                                        data-amount="<?php echo $ride['Amount']; ?>"
                                        data-taxi-id="<?php echo $ride['Taxi_ID']; ?>">Finish Ride</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>


        </div>

        
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

    const driverID = '<?php echo $Driver_ID; ?>';
    const driverName = '<?php echo $driverFirstName . ' ' . $driverLastName; ?>';
    const driverLocation = '<?php echo $driverLocation; ?>';
    const driverMobile = '<?php echo $driverMobile; ?>';

    const socket = new WebSocket(`ws://localhost:8080/ws?driverID=${driverID}`);

    socket.onopen = function() {
        console.log('Connected to WebSocket server as Driver ID: ' + driverID);
    };

    socket.onmessage = function(event) {
        const response = JSON.parse(event.data);
        console.log('Received response:', response);

        if (response.status === 'rideOffer') {
            sweetAlert({
                title: "New Ride Available",
                text: response.message,
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Accept",
                cancelButtonText: "Reject",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm){
                if (isConfirm) {
                    socket.send(JSON.stringify({
                        action: 'acceptRide',
                        driverID: driverID,
                        driverName: driverName,
                        driverLocation: driverLocation,
                        driverMobile: driverMobile,
                        rideDetails: response.rideDetails
                    }));
                    sweetAlert("Accepted!", "You have accepted the ride.", "success");

                    // Update availability status
                    document.getElementById('driverAvailability').innerText = "Unavailable"; // Update availability status
                } else {
                    socket.send(JSON.stringify({
                        action: 'rejectRide',
                        driverID: driverID,
                        rideDetails: response.rideDetails
                    }));
                    sweetAlert("Rejected", "You have rejected the ride.", "info");
                }
            });
        } else if (response.status === 'confirmed') {
            sweetAlert("Booking Confirmed!", response.message, "success");
        } else if (response.status === 'rejected') {
            sweetAlert("Ride Rejected", "The passenger has been notified.", "info");
        } else if (response.status === 'availabilityUpdate') {
            // Update the availability status based on the received message
            document.getElementById('driverAvailability').innerText = response.availability === 1 ? "Available" : "Unavailable";
        }
    };

    socket.onerror = function(error) {
        console.error('WebSocket Error:', error);
    };

    socket.onclose = function(event) {
        if (event.wasClean) {
            console.log(`WebSocket closed cleanly, code=${event.code}, reason=${event.reason}`);
        } else {
            console.error('WebSocket connection died');
        }
    };
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const driverID = '<?php echo $Driver_ID; ?>';  // Ensure this is set correctly

    const finishRideButtons = document.querySelectorAll('.finish-ride');
    finishRideButtons.forEach(button => {
        button.addEventListener('click', function() {
            const rideID = this.getAttribute('data-ride-id');
            const passengerID = this.getAttribute('data-passenger-id');
            const totalAmount = this.getAttribute('data-amount');  // Get the amount from the button
            const taxiID = this.getAttribute('data-taxi-id');
            const driverID = this.getAttribute('data-driver-id');

            console.log('Finish ride clicked. RideID:', rideID, 'PassengerID:', passengerID, 'Amount:', totalAmount, 'TaxiID:', taxiID, 'Driver ID:',driverID );


            if (!driverID || !passengerID || !rideID || !totalAmount || !taxiID) {
                console.error('Missing driverID, passengerID, rideID, totalAmount, or taxiID.');
                return;
            }

            swal({
                title: "Are you sure?",
                text: "Do you want to finish this ride?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    socket.send(JSON.stringify({
                        action: 'finishRide',
                        rideID: rideID,
                        driverID: driverID,
                        passengerID: passengerID,
                        totalAmount: totalAmount
                    }));

                    swal("Waiting", "Waiting for the passenger to proceed.", "info");

                   // Listen for the passenger's payment method response and other actions
                    socket.onmessage = function(event) {
                        const response = JSON.parse(event.data);
                        console.log('Driver received message:', response);

                        if (response.action === 'passengerPaymentMethod' && response.rideID === rideID) {
                            if (response.paymentMethod === 'cash') {
                                swal({
                                    title: "Cash Payment",
                                    text: "Please confirm the correct amount of cash has been received.",
                                    type: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Yes, correct",
                                    cancelButtonText: "No, incorrect",
                                    closeOnConfirm: false,
                                    closeOnCancel: false
                                }, function(confirmed) {
                                    if (confirmed) {
                                        // Call the new method to finish the ride and update the ride table
                                        finishRideAndUpdateTable(rideID, driverID, totalAmount, taxiID, driverName, passengerID);

                                        swal("Success", "Cash payment has been confirmed.", "success");
                                    } else {
                                        // Send a message to the passenger that the cash amount is incorrect
                                        socket.send(JSON.stringify({
                                            action: 'driverCashConfirmation',
                                            confirmed: false, // Set confirmed to false
                                            rideID: rideID,
                                            passengerID: passengerID,
                                            totalAmount: totalAmount
                                        }));

                                        swal("Waiting", "Notified passenger to recheck the cash amount.", "info");
                                    }
                                });
                            } else if (response.paymentMethod === 'online') {
                                swal("Online Payment", "The passenger is proceeding with online payment.", "info");
                            }
                        } else if (response.action === 'passengerRecheckedCash' && response.rideID === rideID) {
                            // Handle passenger rechecking the cash
                            console.log("Received 'passengerRecheckedCash' message.");
                            swal({
                                title: "Cash Re-checked",
                                text: "The passenger has rechecked the cash amount. Do you confirm?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Yes, correct",
                                cancelButtonText: "No, incorrect",
                                closeOnConfirm: false,
                                closeOnCancel: false
                            }, function(confirmed) {
                            if (confirmed) {
                                // Call the method to finish the ride and update the ride table
                                finishRideAndUpdateTable(rideID, driverID, totalAmount, taxiID, driverName, passengerID);

                                // Notify the WebSocket server of cash payment success
                                socket.send(JSON.stringify({
                                    action: 'cashPaymentSuccess',  // New action for cash payment success
                                    rideID: rideID,
                                    passengerID: passengerID,
                                    driverID: driverID,
                                    totalAmount: totalAmount
                                }));

                                swal("Success", "Cash payment has been confirmed.", "success");
                            } else {
                                // Send a message to the passenger that the cash amount is incorrect
                                socket.send(JSON.stringify({
                                    action: 'driverCashConfirmation',
                                    confirmed: false, // Set confirmed to false
                                    rideID: rideID,
                                    passengerID: passengerID,
                                    totalAmount: totalAmount
                                }));

                                swal("Please ask the passenger to pay online.", "info");
                            }
                        });
                        } else if (response.action === 'passengerOnlinePaymentSuccess' && response.rideID === rideID) {

                            finishRideAndUpdateTable(rideID, driverID, totalAmount, taxiID, driverName, passengerID);
                            swal({
                                title: "Payment Completed",
                                text: "The passenger has successfully completed the online payment.",
                                type: "success",
                                confirmButtonText: "OK"
                            });//, function() {
                            //     // Call the function to finish the ride and update the ride table
                            //     finishRideAndUpdateTable(rideID, driverID, totalAmount, taxiID, driverName, passengerID);
                            // }
                        }
                    };

                }
            });
        });
    });


    // Method to finish the ride, update the ride table, insert into payments and invoices table
    function finishRideAndUpdateTable(rideID, driverID, amount, taxiID, driverName, passengerID) {
        // Get the current date and time
        const now = new Date();
        const endDate = now.toISOString().split('T')[0]; // Format as YYYY-MM-DD
        const endTime = now.toTimeString().split(' ')[0]; // Format as HH:MM:SS

        // Check if all required data is present before proceeding
        if (!rideID || !driverID || !amount || !taxiID || !driverName || !passengerID) {
            console.error('Missing required data to finish the ride.');
            return;
        }

        // Send AJAX request to finish the ride, update the ride and insert into payments and invoices
        fetch('/CityTaxi/Functions/Common/Rides.php', {  // Updated path
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'finishRide',
                rideID: rideID,
                driverID: driverID,
                endDate: endDate,
                endTime: endTime,
                amount: amount,        // Send the amount for the payment
                taxiID: taxiID,        // Send the taxiID for the payment
                driverName: driverName, // Send the driver name
                passengerID: passengerID // Send the passenger ID
            })
        })
        .then(response => response.text())  // Get the full response as text for debugging
        .then(text => {
            try {
                const data = JSON.parse(text);  // Try to parse the text into JSON
                if (data.success) {
                    console.log('Ride, payment, and invoice processed successfully:', data);
                    swal("Success", "Ride has been completed and payment recorded.", "success");
                } else {
                    console.error('Error in processing ride, payment, and invoice:', data.message);
                    swal("Error", "There was an issue processing the ride. Please try again.", "error");
                }
            } catch (err) {
                console.error('Response is not valid JSON:', text);  // Log the entire response for debugging
                swal("Error", "An unexpected error occurred. Please try again.", "error");
            }
        })
        .catch(error => {
            console.error('Error finishing ride, payment, and invoice:', error);
            swal("Error", "An error occurred while processing the ride. Please try again.", "error");
        });
    }
});

</script>


<?php 
    include $rootPath . 'TemplateParts/Passenger/PanelParts/menu.php';
    include $rootPath . 'TemplateParts/Footer/footer.php'; 
?>