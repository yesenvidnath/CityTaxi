<?php
// Define the root path for the includes
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/'; 

include $rootPath . 'TemplateParts/Header/header.php'; 
include_once $rootPath . 'Functions/Passenger/Passenger.php';
include_once $rootPath . 'Functions/Common/Ratings.php';
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
    $ratings = new Ratings(); // Initialize the Ratings class

    if (!empty($rideDetails) && isset($rideDetails[2]) && !empty($rideDetails[2])): ?>
        <?php foreach ($rideDetails[2] as $ride): ?>
            <?php
            // Fetch the driver's details using the Driver ID
            $driverDetails = $driver->getDriverDetails($ride['Driver_ID']);
            $driverName = $driverDetails ? $driverDetails['First_name'] . ' ' . $driverDetails['Last_name'] : "Driver Not Found";
    
            // Check if the ride has already been rated
            $isRated = $ratings->ratingExists($ride['Ride_ID']);
            ?>
            <div class="ride-card">
                <h3><?php echo "Driver: " . $driverName; ?></h3>
                <span class="distance-label">Distance: <?php echo $ride['Total_distance'] . "KM"; ?></span>
                <span class="">Ride ID <?php echo $ride['Ride_ID']; ?></span>
                <p>Pickup - <?php echo $ride['Start_Location']; ?></p>
                <p>Where To - <?php echo $ride['End_Location']; ?></p>
                
                <!-- Show the rate button only if the ride hasn't been rated yet -->
                <?php if (!$isRated): ?>
                    <button class="rate-btn btn btn-success" onclick="openRatingPopup('<?php echo $ride['Ride_ID']; ?>', '<?php echo $ride['Driver_ID']; ?>')">Rate Ride ⭐</button>
                <?php else: ?>
                    <p>This ride has already been rated.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No rides found for this passenger.</p>
    <?php endif; ?>
    
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


<!-- Include the footer -->
<?php 
include $rootPath . 'TemplateParts/Passenger/PanelParts/menu.php'; 
include $rootPath . 'TemplateParts/Footer/footer.php'; 
?>
