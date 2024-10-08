<?php

include_once __DIR__ . '/Database.php';

//include_once $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/Functions/Common/Database.php';

class Ride {
    private $db;
    private $conn;

    // Constructor to initialize the Database connection
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Fetch all ride details
    public function fetchAllRides() {
        $query = "SELECT * FROM Rides";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch specific ride details by ID
    public function getRideDetails($rideId) {
        $query = "SELECT * FROM Rides WHERE Ride_ID = :rideId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':rideId', $rideId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Delete a ride
    public function deleteRide($rideId) {
        $query = "DELETE FROM Rides WHERE Ride_ID = :rideId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':rideId', $rideId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount(); // Returns the number of rows affected
    }

    // Method to get driver vehicle by driver ID
    public function getDriverVehicleById($driverId) {
        $query = "CALL GetDriverVehicleInfo(:driverId)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':driverId', $driverId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Fetch a single result
        return $stmt->fetch(PDO::FETCH_ASSOC); // Change this line to fetch a single row
    }

    public function setDriverAvailability($driverID, $availability) {
        $query = "UPDATE drivers SET Availability = :availability WHERE Driver_ID = :driverID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':availability', $availability, PDO::PARAM_INT);
        $stmt->bindParam(':driverID', $driverID, PDO::PARAM_INT);
        
        return $stmt->execute();
    }  
    
    public function updateDriverAvailability($driverID, $availability) {
        $query = "CALL UpdateDriverAvailability(:driverID, :availability)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':driverID', $driverID, PDO::PARAM_INT);
        $stmt->bindParam(':availability', $availability, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getDriverAvailability($driverID) {
        $query = "CALL GetDriverAvailability(:driverID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':driverID', $driverID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}

?>
