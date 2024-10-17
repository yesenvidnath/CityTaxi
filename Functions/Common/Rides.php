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

    // Method to finish the ride
    public function finishRide($rideID, $driverID, $endDate, $endTime, $amount, $taxiID, $driverName, $passengerID) {
        try {
            // Step 1: Check if a payment already exists for the given Ride_ID
            $checkPayment = $this->conn->prepare("SELECT COUNT(*) FROM payments WHERE Ride_ID = :rideID");
            $checkPayment->bindParam(':rideID', $rideID, PDO::PARAM_INT);
            $checkPayment->execute();
            $paymentExists = $checkPayment->fetchColumn();

            if ($paymentExists > 0) {
                // If a payment already exists, return an error message
                return ['success' => false, 'message' => 'Payment already exists for this ride'];
            }

            // Step 2: If no payment exists, proceed with the stored procedure to finish the ride and add the payment
            $stmt = $this->conn->prepare("CALL FinishRide(:rideID, :driverID, :endDate, :endTime, :amount, :taxiID, :driverName, :passengerID)");

            // Bind the parameters
            $stmt->bindParam(':rideID', $rideID, PDO::PARAM_INT);
            $stmt->bindParam(':driverID', $driverID, PDO::PARAM_INT);
            $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
            $stmt->bindParam(':endTime', $endTime, PDO::PARAM_STR);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt->bindParam(':taxiID', $taxiID, PDO::PARAM_INT);
            $stmt->bindParam(':driverName', $driverName, PDO::PARAM_STR);
            $stmt->bindParam(':passengerID', $passengerID, PDO::PARAM_INT);

            // Execute the statement
            $stmt->execute();
            
            // Return a success response
            return ['success' => true, 'message' => 'Ride completed successfully'];
        } catch (PDOException $e) {
            // Return an error response
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function getDriverDetailsByDriverID($driverID) {
        try {
            // Prepare the statement to call the stored procedure
            $stmt = $this->conn->prepare("CALL GetDriverDetails(:driverID)");

            // Bind the driverID parameter
            $stmt->bindParam(':driverID', $driverID, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Fetch the driver details
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Close the cursor to free up the connection
            $stmt->closeCursor();

            // Check if we got a result and return it
            if ($result) {
                return $result;
            } else {
                return null;  // Return null if no driver details are found
            }

        } catch (PDOException $e) {
            // Handle any errors and return an empty array or false
            return ['error' => $e->getMessage()];
        }
    }

    // Method to get passenger details using the stored procedure
    public function getPassengerDetailsByID($passengerID) {
        try {
            // Prepare the statement to call the stored procedure
            $stmt = $this->conn->prepare("CALL GetPassengerDetailsByID(:passengerID)");
            $stmt->bindParam(':passengerID', $passengerID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if ($result) {
                return $result;
            } else {
                return null;  // Return null if no passenger details are found
            }

        } catch (PDOException $e) {
            // Handle any errors and return an empty array or false
            return ['error' => $e->getMessage()];
        }
    }

    // Method to get start and end locations using the stored procedure
    public function getRideLocationsByID($rideID) {
        try {
            // Prepare the statement to call the stored procedure
            $stmt = $this->conn->prepare("CALL GetRideLocationsByRideID(:rideID)");
            $stmt->bindParam(':rideID', $rideID, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if ($result) {
                return $result;
            } else {
                return null;  // Return null if no locations are found
            }
 
        } catch (PDOException $e) {
            // Handle any errors and return an empty array or false
            return ['error' => $e->getMessage()];
        }
    }

    // Method to get driver details by Ride ID
    public function getDriverDetailsByRideID($rideID) {
        try {
            // Prepare the stored procedure call
            $stmt = $this->conn->prepare("CALL GetDriverDetailsByRideID(:rideID)");
            $stmt->bindParam(':rideID', $rideID, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch the driver details
            $driverDetails = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($driverDetails) {
                return $driverDetails; // Return the result as an associative array
            } else {
                return false; // Return false if no details found
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

}


// Check if the script is being accessed via an HTTP request
if (php_sapi_name() == "cli-server" || php_sapi_name() == "apache2handler") {
    // Check for AJAX requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Decode the JSON input
        $data = json_decode(file_get_contents('php://input'), true);

        // Check if 'action' exists in the decoded data
        if (isset($data['action']) && $data['action'] == 'finishRide') {
            $rideID = $data['rideID'];
            $driverID = $data['driverID'];
            $endDate = $data['endDate'];
            $endTime = $data['endTime'];
            $amount = $data['amount'];  // Fetch the amount from the request
            $taxiID = $data['taxiID'];  // Fetch the taxi ID from the request
            $driverName = $data['driverName'];  // Fetch the driver name from the request
            $passengerID = $data['passengerID'];  // Fetch the passenger ID from the request

            // Create an instance of the Ride class and call the finishRide method
            $ride = new Ride();
            $response = $ride->finishRide($rideID, $driverID, $endDate, $endTime, $amount, $taxiID, $driverName, $passengerID);

            // Return the response as JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // Return error if action is missing
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    }
}

?>
