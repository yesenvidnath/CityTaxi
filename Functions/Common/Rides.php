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

    // Method to add a new ride
    public function addRide($taxiID, $driverID, $passengerID, $type, $startLocation, $endLocation, $startTime, $endTime, $startDate, $endDate, $totalDistance, $amount, $status) {
        // Prepare and execute the stored procedure
        $query = "CALL AddRide(:taxiID, :driverID, :passengerID, :type, :startLocation, :endLocation, :startTime, :endTime, :startDate, :endDate, :totalDistance, :amount, :status)";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':taxiID', $taxiID, PDO::PARAM_INT);
        $stmt->bindParam(':driverID', $driverID, PDO::PARAM_INT);
        $stmt->bindParam(':passengerID', $passengerID, PDO::PARAM_INT);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':startLocation', $startLocation, PDO::PARAM_STR);
        $stmt->bindParam(':endLocation', $endLocation, PDO::PARAM_STR);
        $stmt->bindParam(':startTime', $startTime, PDO::PARAM_STR);
        $stmt->bindParam(':endTime', $endTime, PDO::PARAM_STR);
        $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $stmt->bindParam(':totalDistance', $totalDistance, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        // Execute the stored procedure
        return $stmt->execute();
    }

    // Finish Ride functionality
    public function finishRide($rideID, $driverID, $endDate, $endTime) {
        $query = "CALL FinishRide(:rideID, :driverID, :endDate, :endTime)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':rideID', $rideID, PDO::PARAM_INT);
        $stmt->bindParam(':driverID', $driverID, PDO::PARAM_INT);
        $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $stmt->bindParam(':endTime', $endTime, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    
}

// Check if the script is being accessed via an HTTP request
if (php_sapi_name() == "cli-server" || php_sapi_name() == "apache2handler") {
    // Check for AJAX requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['action']) && $data['action'] === 'finishRide') {
            $rideID = $data['rideID'];
            $driverID = $data['driverID'];
            $endDate = $data['endDate']; // Get the end date from the request
            $endTime = $data['endTime']; // Get the end time from the request

            // Create an instance of the Ride class
            $ride = new Ride();

            // Call the finishRide method with endDate and endTime
            if ($ride->finishRide($rideID, $driverID, $endDate, $endTime)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
            exit; // Terminate the script after handling the request
        }
    }
}



?>
