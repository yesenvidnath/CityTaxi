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
    public function getAvailableDriversByVehicleType($taxiType, $startLat, $startLng, $radius) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("CALL GetAvailableDriversByVehicleType(:taxiType, :startLat, :startLng, :radius)");
        $stmt->bindParam(':taxiType', $taxiType, PDO::PARAM_STR);
        $stmt->bindParam(':startLat', $startLat);
        $stmt->bindParam(':startLng', $startLng);
        $stmt->bindParam(':radius', $radius);

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }
}

?>

