<?php
// Define the root path for the includes
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/'; 

include $rootPath . 'TemplateParts/Header/header.php'; 
include_once $rootPath . 'Functions/Passenger/Passenger.php';
include_once $rootPath . 'Functions/Common/Ratings.php';
include_once $rootPath . 'Functions/Driver/Driver.php';

// Start the session and get the user ID using SessionManager
SessionManager::startSession();
// Retrieve user ID from session
$userID = SessionManager::get('user_ID'); 

if (!SessionManager::isLoggedIn() || !$userID) {
    header("Location: /CityTaxi/login.php?status=error&message=Please log in first!");
    exit();
}

// Initialize Passenger class and get passenger details
$driver = new Driver();
$ratings = new Ratings(); 
$passenger = new Passenger();
$userInfo = $passenger->getPassengerUserInfo($userID);
$rideDetails = $passenger->getPassengerDetails($userID);

// Get the Passenger ID
$passengerID = $passenger->getPassengerIDByUserID($userID);

// Assign user info to variables for easy use
$firstName = $userInfo['First_name'] ?? 'N/A';
$lastName = $userInfo['Last_name'] ?? 'N/A';
$email = $userInfo['Email'] ?? 'N/A';
$mobile = $userInfo['mobile_number'] ?? 'N/A'; 
$memberSince = $userInfo['Created_At'] ?? 'N/A'; 
$nicNo = $userInfo['NIC_No'] ?? 'N/A';
$address = $userInfo['Address'] ?? 'N/A';
$userImage = $userInfo['user_img'] ?? 'default.jpg'; 

// Construct the image path
$imagePath = "/CityTaxi/Assets/Img/Passenger/" . $userImage;
 
?>

<div class="wrapper-container">

    <div class="container profile-page center-content">
        <div class="profile-card text-center shadow-lg">
            <div class="profile-cover"></div>
            <div class="profile-header">
                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar">
                        <img src="<?php echo $imagePath; ?>" alt="Profile Image" class="profile-image img-fluid">
                        <div class="status-indicator"></div>
                    </div>
                </div>
                <h2 class="profile-name mb-2"><?php echo $firstName . ' ' . $lastName; ?></h2>
                <div class="driver-rating mb-2">
                    <i class="fas fa-star"></i>
                    <span>4.95</span> <!-- You can dynamically fetch rating here -->
                </div>
                <div class="status-pill mb-3">
                    Member Since <?php echo date('Y', strtotime($memberSince)); ?>
                </div>
                <div class="contact-info">
                    <div class="info-item">
                        <i class="fas fa-phone-alt"></i>
                        <span><?php echo $mobile; ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo $email; ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-id-card"></i>
                        <span><?php echo $nicNo; ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo $address; ?></span>
                    </div>
                </div>
                <button class="btn btn-apple mt-4 location-btn">Bambalapitiya</button>
            </div>
        </div>
    </div>

    <div class="container-fluid ride-container">
        <?php if (!empty($rideDetails) && isset($rideDetails[2]) && !empty($rideDetails[2])): ?>
            <div class="card rides-card shadow-lg mb-4">
                <div class="card-header">
                    <h4>Ride History</h4>
                    <span class="badge ride-status"><?php echo count($rideDetails[2]); ?> Rides</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($rideDetails[2] as $ride): ?>
                            <?php
                            $driverDetails = $driver->getDriverDetails($ride['Driver_ID']);
                            $driverName = $driverDetails ? $driverDetails['First_name'] . ' ' . $driverDetails['Last_name'] : "Driver Not Found";
                            $isRated = $ratings->ratingExists($ride['Ride_ID']);
                            ?>
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
                                        <p class="text-muted">This ride is in progress.</p>
                                        <?php elseif (!$isRated): ?>
                                        <button class="btn btn-success w-100 mt-3 rate-btn"
                                            onclick="openRatingPopup('<?php echo $ride['Ride_ID']; ?>', '<?php echo $ride['Driver_ID']; ?>')">
                                            Rate Ride
                                        </button>
                                    <?php else: ?>
                                        <p class="text-muted">This ride has already been rated.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="no-rides">
                <i class="fas fa-car-side"></i>
                <p>No rides found for this passenger.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        hideElementsBySelector('.hide-this');
        hideElementsBySelector('#myprofile-hide-btn');
    });

    function openRatingPopup(rideId, driverId) {
        // Custom HTML for the stars
        const customHtml = `
            <div class="custom-star-rating">
                <h4>Select your rating</h4>
                <div id="starRating">
                    <i class="fas fa-star" data-value="1"></i>
                    <i class="fas fa-star" data-value="2"></i>
                    <i class="fas fa-star" data-value="3"></i>
                    <i class="fas fa-star" data-value="4"></i>
                    <i class="fas fa-star" data-value="5"></i>
                </div>
                <p id="ratingValue" style="margin-top: 10px;">Rating: 0</p>
            </div>
        `;

        // First SweetAlert to handle the star rating
        swal({
            title: "Rate Your Ride",
            text: customHtml,  // Custom star HTML
            html: true,
            showCancelButton: true,
            confirmButtonText: "Next",
            cancelButtonText: "Cancel",
            closeOnConfirm: false
        }, function() {
            const rating = document.getElementById('ratingValue').dataset.rating;

            if (!rating || rating < 1) {
                swal("Error", "Please select a rating before proceeding.", "error");
                return;
            }

            // Move to the comment section
            swal({
                title: "Leave a Comment",
                text: "Please leave a comment about your ride:",
                type: "input",
                showCancelButton: true,
                confirmButtonText: "Submit",
                cancelButtonText: "Cancel",
                closeOnConfirm: false
            }, function(comment) {
                if (comment === false) return false; // If the user cancels the comment part

                if (comment === "") {
                    swal.showInputError("You need to write something!");
                    return false;
                }

                // Send the rating and comment via AJAX
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "/CityTaxi/Functions/Common/Ratings.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        if (xhr.responseText === "success") {
                            swal("Thank you!", "Your rating has been submitted.", "success");
                        } else {
                            swal("Error", xhr.responseText, "error");
                        }
                    }
                };
                xhr.send(`action=addRating&rideId=${rideId}&driverId=${driverId}&rate=${rating}&comment=${encodeURIComponent(comment)}`);
            });
        });

        // Event listener for star rating interaction
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('fa-star')) {
                const stars = document.querySelectorAll('#starRating .fa-star');
                const selectedValue = event.target.getAttribute('data-value');
                
                // Highlight selected stars
                stars.forEach((star) => {
                    const starValue = star.getAttribute('data-value');
                    if (starValue <= selectedValue) {
                        star.classList.add('selected');
                    } else {
                        star.classList.remove('selected');
                    }
                });

                // Set rating value
                const ratingValueElement = document.getElementById('ratingValue');
                ratingValueElement.innerHTML = `Rating: ${selectedValue}`;
                ratingValueElement.dataset.rating = selectedValue; // Store selected rating
            }
        });
    }

</script>



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
        const passengerID = '<?php echo $passengerID; ?>'; // Ensure this is correctly set in PHP
        const firstName = '<?php echo $firstName; ?>';
        const lastName = '<?php echo $lastName; ?>';
        const email = '<?php echo $email; ?>';
        const mobile = '<?php echo $mobile; ?>';
        const address = '<?php echo $address; ?>';

        const socket = new WebSocket(`ws://localhost:8080/ws?passengerID=${passengerID}`);

        let rideID = null;
        let driverID = null; // Driver ID is stored here when received in WebSocket
        let totalAmount = null;
        let taxiID = null;

        socket.onopen = function() {
            console.log('Connected to WebSocket server as Passenger ID: ' + passengerID);
        };

        socket.onmessage = function(event) {
            const response = JSON.parse(event.data);
            console.log('Received message:', response);

            if (response.status === 'rideEndRequest') {
                rideID = response.rideID;
                driverID = response.driverID; // Set driverID from WebSocket message
                totalAmount = response.totalAmount;
                taxiID = response.taxiID;

                console.log("Ride ID received from WebSocket:", rideID);
                console.log("Driver ID received from WebSocket:", driverID);

                swal({
                    title: "Ride End Request",
                    text: "The driver has ended the ride. Do you confirm?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, end the ride",
                    cancelButtonText: "No, continue the ride",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function(isConfirm) {
                    if (isConfirm) {
                        handlePaymentMethodSelection(rideID, driverID, totalAmount); // driverID is now set and passed here
                    } else {
                        swal("Declined", "You have declined to end the ride.", "info");
                    }
                });
            } 
            // Handle cash payment confirmation
            else if (response.status === 'driverCashConfirmation') {
                handleDriverCashConfirmation(response);
            } 
            // Handle cash payment success
            else if (response.status === 'cashPaymentSuccess') {
                swal("Success", "Cash payment has been successfully confirmed by the driver.", "success");
            }
        };


        function handlePaymentMethodSelection(rideID, driverID, totalAmount) {
            if (!driverID) {
                console.error("driverID is missing before payment method selection.");
                return; // Exit the function if driverID is missing
            }

            swal({
                title: "Thank you for confirming!",
                text: "What's your payment method?",
                type: "info",
                showCancelButton: true,
                confirmButtonText: "Cash",
                cancelButtonText: "Online Banking",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function(isCash) {
                if (isCash) {
                    sendPaymentMethod('cash', rideID, driverID); // Pass driverID here
                    swal("Waiting for Driver", "Waiting for driver to confirm the correct cash amount.", "info");
                } else {
                    sendPaymentMethod('online', rideID, driverID); // Pass driverID here
                    swal({
                        title: "Proceed to Payment",
                        text: "You will be redirected to the payment gateway.",
                        type: "success",
                        closeOnConfirm: false
                    }, function() {
                        sendPaymentDataToServer(rideID, driverID, totalAmount); // Pass driverID here
                    });
                }
            });
        }


        function sendPaymentMethod(paymentMethod, rideID, driverID) {
            if (driverID && passengerID) {
                socket.send(JSON.stringify({
                    action: 'passengerPaymentMethod',
                    paymentMethod: paymentMethod,
                    rideID: rideID,
                    passengerID: passengerID,
                    driverID: driverID, // Send the driverID stored from WebSocket
                    totalAmount: totalAmount
                }));
            } else {
                console.error('Missing driverID or passengerID.');
            }
        }

        function sendPaymentDataToServer(rideID, driverID, totalAmount) {
            console.log("Sending totalAmount to server:", totalAmount);

            if (!driverID) {
                console.error("driverID is missing when sending payment data to server.");
                return;
            }

            fetch('/CityTaxi/Functions/Common/Payments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    rideID: rideID,  // Ensure rideID is passed here
                    driverID: driverID,
                    amount: parseFloat(totalAmount),
                    firstName: firstName,
                    lastName: lastName,
                    email: email,
                    phone: mobile,
                    address: address,
                    passengerID: passengerID
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Payment data received:', data.paymentData);

                    // Ensure driverID and rideID are included in the paymentData object
                    data.paymentData.driverID = driverID;
                    data.paymentData.rideID = rideID;  // Add rideID here

                    // Pass payment data and success callback to paymentGateway
                    paymentGateway(data.paymentData, processPaymentStatus);
                } else {
                    console.error('Error preparing payment:', data.message);
                    swal("Error", "There was an error processing your payment. Please try again.", "error");
                }
            })
            .catch(error => {
                console.error('Error sending payment data:', error);
                swal("Error", "There was an error processing your payment. Please try again.", "error");
            });
        }


        
        function processPaymentStatus(paymentStatus, orderId, rideID, driverID, totalAmount) {
            if (paymentStatus === "completed") {
                swal("Payment Completed", "Your payment was successful. Order ID: " + orderId, "success");

                // Debug log for driverID and rideID to confirm they're being passed
                console.log("driverID being sent: ", driverID);
                console.log("rideID being sent: ", rideID);

                // Notify the driver of the successful online payment via WebSocket
                socket.send(JSON.stringify({
                    action: 'passengerOnlinePaymentSuccess',
                    rideID: rideID,         // Ensure rideID is passed here
                    driverID: driverID,     // Ensure driverID is passed here
                    passengerID: passengerID,
                    totalAmount: totalAmount
                }));

            } else {
                swal("Error", "The payment failed to process. Please try again.", "error");
            }
        }



        function handleDriverCashConfirmation(response) {
            if (response.confirmed) {
                swal("Success", "Cash payment has been successfully confirmed by the driver.", "success");
            } else {
                swal({
                    title: "Incorrect Amount",
                    text: "The cash amount is incorrect. Please recheck.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Re-checked and Correct",
                    cancelButtonText: "No, Pay Online",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function(rechecked) {
                    if (rechecked) {
                        sendRecheckedCash(response.rideID, driverID); // Use the stored driverID
                        swal("Waiting for Driver", "Waiting for driver to confirm the corrected cash amount.", "info");
                    } else {
                        sendPaymentDataToServer(rideID, driverID, totalAmount); // Use the stored driverID
                        swal("Switching to Online Payment", "Please proceed with online payment.", "success");
                    }
                });
            }
        }


        function sendRecheckedCash(rideID, driverID) {
            if (driverID && passengerID) {
                socket.send(JSON.stringify({
                    action: 'passengerRecheckedCash',
                    rideID: rideID,
                    driverID: driverID, // Ensure driverID is sent
                    passengerID: passengerID
                }));
            } else {
                console.error("Missing driverID or passengerID.");
            }
        }

        function switchToOnlinePayment(rideID, driverID) {
            if (driverID && passengerID) {
                socket.send(JSON.stringify({
                    action: 'passengerSwitchToOnline',
                    rideID: rideID,
                    driverID: driverID, // Ensure driverID is sent
                    passengerID: passengerID
                }));
            } else {
                console.error("Missing driverID or passengerID.");
            }
        }
    });
</script>



<script src="https://www.payhere.lk/lib/payhere.js"></script>
<script src="/CityTaxi/Assets/Js/payments.js"></script>

<script src="//code.tidio.co/3zbfxeuvta3jkyr1we8h8cr9yjg060c0.js" async></script>

<?php // tmp add please remove immediatly looks
// include $rootPath . 'TemplateParts/Passenger/PanelParts/menu.php';
include $rootPath . 'TemplateParts/Footer/footer.php'; 
?>
