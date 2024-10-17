<?php
// Define the root path for the includes
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/'; 

include $rootPath . 'TemplateParts/Header/header.php'; 
include_once $rootPath . 'Functions/Passenger/Passenger.php';

// Start the session and get the user ID using SessionManager
SessionManager::startSession();
// Retrieve user ID from session
$userID = SessionManager::get('user_ID'); 

if (!SessionManager::isLoggedIn() || !$userID) {
    header("Location: /CityTaxi/login.php?status=error&message=Please log in first!");
    exit();
}

// Initialize Passenger class and get passenger details
$passenger = new Passenger();
$userInfo = $passenger->getPassengerUserInfo($userID);

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

<div class="container profile-page center-content">
    <div class="profile-card text-center">
        <img src="<?php echo $imagePath; ?>" alt="Profile Image" class="profile-image">
        <h2><?php echo $firstName . ' ' . $lastName; ?></h2>
        <div class="membership">
            <span class="member-since">Member Since</span>
            <span class="member-year"><?php echo date('Y', strtotime($memberSince)); ?></span>
        </div>
        <div class="contact-info">
            <p>Mobile Number - <?php echo $mobile; ?></p>
            <p>Email Address - <?php echo $email; ?></p>
            <p>NIC Number - <?php echo $nicNo; ?></p>
            <p>Address - <?php echo $address; ?></p>
        </div>
        <button class="btn btn-warning location-btn">Bambalapitiya</button>
    </div>
</div>


<div id="paymentPopup" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Complete Your Payment</h2>
        <div id="paymentContainer"></div>
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


<?php // tmp add please remove immediatly looks
include $rootPath . 'TemplateParts/Passenger/PanelParts/menu.php';
include $rootPath . 'TemplateParts/Footer/footer.php'; 
?>
