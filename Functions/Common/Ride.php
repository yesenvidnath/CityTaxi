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


class Ride {

    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Function to get available drivers within a radius of the start location
    function getAvailableDriversByVehicleType($taxiType, $startLat, $startLng, $radius) {
        $db = new Database();
        $conn = $db->getConnection();
    
        try {
            // Call the stored procedure to get drivers within a radius
            $stmt = $conn->prepare("CALL GetAvailableDrivers()");
            $stmt->execute();
            
            // Fetch drivers
            $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $drivers;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        } finally {
            $db->close();
        }
    }
}


// Function to fetch available drivers
function getAvailableDrivers() {
    $db = new Database();
    $conn = $db->getConnection();
    $query = "CALL GetAvailableDrivers()";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

