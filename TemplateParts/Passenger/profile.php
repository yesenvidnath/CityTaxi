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
?>

<?php 
include $rootPath . 'TemplateParts/Footer/footer.php'; 
?>
