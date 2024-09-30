<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/Functions/Common/Database.php';

// ride.php

function getTaxiTypes() {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "CALL GetTaxiTypes()";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();
    return $result;
}

function getTaxiRatesByType() {
    // Initialize Database connection
    $db = new Database();
    $conn = $db->getConnection();
    
    try {
        // Prepare and execute the stored procedure
        $stmt = $conn->prepare("CALL GetTaxiRatesByType()");
        $stmt->execute();
        
        // Fetch all results
        $taxiRates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $taxiRates; // Return the rates
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null; // Return null on failure
    } finally {
        $db->close(); // Close the connection
    }
}

// Function to fetch available drivers
function getAvailableDrivers() {
    $db = new Database();
    $conn = $db->getConnection();
    try {
        $query = "CALL GetAvailableDrivers()";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching available drivers: " . $e->getMessage());
        return []; // Return an empty array on error
    }
}

// Check for the fetch_drivers parameter to return available drivers as JSON
if (isset($_GET['fetch_drivers']) && $_GET['fetch_drivers'] === 'true') {
    header('Content-Type: application/json');
    $availableDrivers = getAvailableDrivers();
    echo json_encode($availableDrivers);
    exit;
}

?>