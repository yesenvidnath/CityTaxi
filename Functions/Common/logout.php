<?php
include 'SessionManager.php';

// Start the session
SessionManager::startSession();

// Destroy the session and redirect to the login page with a success message
SessionManager::logout();

header("Location: /CityTaxi/index.php?status=success&message=You have been logged out successfully!");
exit();
?>
