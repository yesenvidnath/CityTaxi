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






<!-- Include the footer -->
<?php 
include $rootPath . 'TemplateParts/Passenger/PanelParts/menu.php'; 
include $rootPath . 'TemplateParts/Footer/footer.php'; 
?>
