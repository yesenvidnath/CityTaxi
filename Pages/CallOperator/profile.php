<?php

// Define the root path for the includes
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/'; 

include $rootPath . 'TemplateParts/Header/header.php'; 
include_once $rootPath . 'Functions/Driver/Driver.php';
include_once $rootPath . 'Functions/Common/Rides.php'; 
include_once $rootPath . 'Functions/Common/Users.php'; 
include_once $rootPath . 'Functions/Common/Reservation.php'; 

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
// Check if $driverInfo is null or empty before accessing its keys
if ($driverInfo && is_array($driverInfo)) {
    $Driver_ID = $driverInfo['Driver_ID'] ?? 'N/A';
    $driverFirstName = $driverInfo['First_name'] ?? 'N/A';
    $driverLastName = $driverInfo['Last_name'] ?? 'N/A';
    $driverMobile = $driverInfo['mobile_number'] ?? 'N/A';
    $driverEmail = $driverInfo['Email'] ?? 'N/A';
    $driverLocation = $driverInfo['Current_Location'] ?? 'N/A';
    $userImage = $driverInfo['user_img'] ?? 'default.jpg';
} else {
    // Handle the case where $driverInfo is not available
    $Driver_ID = 'N/A';
    $driverFirstName = 'Unknown';
    $driverLastName = 'Unknown';
    $driverMobile = 'N/A';
    $driverEmail = 'N/A';
    $driverLocation = 'N/A';
    $userImage = 'default.jpg';
}
// Construct the image path
$imagePath = "/CityTaxi/Assets/Img/Driver/" . $userImage;

// Debug: Check session contents
error_log("Session Contents: " . print_r($_SESSION, true));

// Create an instance of the Ride class
$ride = new Ride(); // Ensure the Ride class is instantiated

$driver = new Driver();
$availableDrivers = $driver->getAvailableDrivers(); 

// Get driver's availability
$availabilityInfo = $ride->getDriverAvailability($driverInfo['Driver_ID']); // Use the Driver_ID


$users = new Users();
$userInfo = $users->fetchUserByID($userID);

// Assign user info to variables for easy use
$firstName = $userInfo['First_name'] ?? 'N/A';
$lastName = $userInfo['Last_name'] ?? 'N/A';
$email = $userInfo['Email'] ?? 'N/A';
$mobile = $userInfo['mobile_number'] ?? 'N/A'; 
$memberSince = $userInfo['Created_At'] ?? 'N/A'; 
$nicNo = $userInfo['NIC_No'] ?? 'N/A';
$address = $userInfo['Address'] ?? 'N/A';
$userImage = $userInfo['user_img'] ?? 'default.jpg'; 

$imagePath = "/CityTaxi/Assets/Img/Passenger/" . $userImage;


if ($availabilityInfo) {
    $availabilityText = $availabilityInfo['Availability'] == 1 ? "Available" : "Unavailable";
} else {
    $availabilityText = "Status Unknown";
}

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
    </div>
    <!-- Available Drivers -->
    <div class="container-fluid ride-container">
        <?php if (!empty($availableDrivers)): ?>
            <div class="card rides-card shadow-lg mb-4">
                <div class="card-header">
                    <h4>Available Drivers</h4>
                    <span class="badge ride-status"><?php echo count($availableDrivers); ?> Drivers</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($availableDrivers as $driver): ?>
                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="ride-card">
                                    <div class="ride-header">
                                        <div class="ride-id">Driver #<?php echo $driver['Driver_ID']; ?></div>
                                        <span class="ride-status">Available</span>
                                    </div>
                                    <div class="ride-route">
                                        <div class="route-point">
                                            <div class="point-marker pickup"></div>
                                            <div class="point-details">
                                                <label>Name</label>
                                                <p><?php echo $driver['First_name'] . ' ' . $driver['Last_name']; ?></p>
                                            </div>
                                        </div>
                                        <div class="route-line"></div>
                                        <div class="route-point">
                                            <div class="point-marker dropoff"></div>
                                            <div class="point-details">
                                                <label>Location</label>
                                                <p><?php echo $driver['Current_Location']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ride-footer">
                                        <div class="passenger-info">
                                            <i class="fas fa-phone-alt"></i>
                                            <span><?php echo $driver['mobile_number']; ?></span>
                                        </div>
                                        <div class="ride-amount">
                                            <i class="fas fa-car"></i>
                                            <span>Vehicle Type: <?php echo $driver['Taxi_type']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="no-rides">
                <i class="fas fa-car-side"></i>
                <p>No available drivers at the moment.</p>
            </div>
        <?php endif; ?>
    </div>


    <button class="apple-trigger-button" onclick="openPopup()">
        <i class="fas fa-plus"></i> Add a Reservation
    </button>
    
    <!-- Popup Overlay -->
    <div class="popup-overlay" id="popupOverlay">
    <div class="popup-container" id="popupContainer">
        <button class="popup-close" onclick="closePopup()">
            <i class="fas fa-times"></i>
        </button>
        <div class="apple-form-card">
            <h4 class="apple-heading">
                <i class="fas fa-taxi apple-icon"></i>Add Reservation
            </h4>
            <form id="reservationForm">
                <!-- First Name -->
                <div class="apple-form-group">
                    <label class="apple-label" for="firstName">
                        <i class="fas fa-user apple-icon"></i>First Name
                    </label>
                    <input type="text" class="apple-input" id="firstName" name="first_name" placeholder="Enter first name" required>
                </div>

                <!-- Last Name -->
                <div class="apple-form-group">
                    <label class="apple-label" for="lastName">
                        <i class="fas fa-user apple-icon"></i>Last Name
                    </label>
                    <input type="text" class="apple-input" id="lastName" name="last_name" placeholder="Enter last name" required>
                </div>

                <!-- Email -->
                <div class="apple-form-group">
                    <label class="apple-label" for="email">
                        <i class="fas fa-envelope apple-icon"></i>Email
                    </label>
                    <input type="email" class="apple-input" id="email" name="email" placeholder="Enter email" required>
                </div>

                <!-- Phone Number -->
                <div class="apple-form-group">
                    <label class="apple-label" for="phoneNumber">
                        <i class="fas fa-phone apple-icon"></i>Phone Number
                    </label>
                    <input type="text" class="apple-input" id="phoneNumber" name="phone_number" placeholder="Enter phone number" required>
                </div>

                <!-- Driver ID -->
                <div class="apple-form-group">
                    <label class="apple-label" for="driverID">
                        <i class="fas fa-id-badge apple-icon"></i>Driver ID
                    </label>
                    <input type="text" class="apple-input" id="driverID" name="driver_id" placeholder="Enter Driver ID" required>
                </div>

                <!-- Start Location -->
                <div class="apple-form-group">
                    <label class="apple-label" for="startLocation">
                        <i class="fas fa-map-marker-alt apple-icon"></i>Start Location
                    </label>
                    <input type="text" class="apple-input" id="startLocation" name="start_location" placeholder="Enter start location" required>
                </div>

                <!-- End Location -->
                <div class="apple-form-group">
                    <label class="apple-label" for="endLocation">
                        <i class="fas fa-map-marker-alt apple-icon"></i>End Location
                    </label>
                    <input type="text" class="apple-input" id="endLocation" name="end_location" placeholder="Enter end location" required>
                </div>

                <!-- Submit Button -->
                <div class="apple-form-group">
                    <button type="submit" class="apple-button">
                        <i class="fas fa-save"></i> Submit Reservation
                    </button>
                </div>
            </form>
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


    document.getElementById('reservationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form data
        const formData = {
            first_name: document.getElementById('firstName').value,
            last_name: document.getElementById('lastName').value,
            email: document.getElementById('email').value,
            phone_number: document.getElementById('phoneNumber').value,
            driver_id: document.getElementById('driverID').value,
            start_location: document.getElementById('startLocation').value,
            end_location: document.getElementById('endLocation').value,
        };

        // Show SweetAlert confirmation dialog
        swal({
            title: "Confirm Reservation",
            text: "Do you want to submit the reservation?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, submit it!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false, // Keep this open until the operation finishes
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                // Show waiting message after confirming
                swal({
                    title: "Processing...",
                    text: "Please wait while we submit your reservation.",
                    type: "info",
                    showConfirmButton: false, // Hide the confirm button during processing
                    allowOutsideClick: false // Prevent closing by clicking outside
                });

                // Send AJAX request to submit the form
                fetch('/CityTaxi/Functions/Common/Reservation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        swal("Success", "Reservation has been added successfully.", "success");
                    } else {
                        swal("Error", "Failed to add reservation. Please try again.", "error");
                    }
                })
                .catch(error => {
                    swal("Error", "An error occurred: " + error, "error");
                });
            }
        });
    });



    // Wait for DOM to be loaded
    document.addEventListener('DOMContentLoaded', function() {
        const popupOverlay = document.getElementById('popupOverlay');
        
        if (popupOverlay) {
            popupOverlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    closePopup();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePopup();
            }
        });
    });

    function openPopup() {
        const popupOverlay = document.getElementById('popupOverlay');
        const popupContainer = document.getElementById('popupContainer');
        
        if (popupOverlay && popupContainer) {
            popupOverlay.style.display = 'block';
            setTimeout(() => {
                popupContainer.classList.add('active');
            }, 10);
            document.body.style.overflow = 'hidden';
        }
    }

    function closePopup() {
        const container = document.getElementById('popupContainer');
        const overlay = document.getElementById('popupOverlay');
        
        if (container && overlay) {
            container.classList.remove('active');
            setTimeout(() => {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        }
    }

</script>


<?php 
    // include $rootPath . 'TemplateParts/Passenger/PanelParts/menu.php';
    include $rootPath . 'TemplateParts/Footer/footer.php'; 
?>