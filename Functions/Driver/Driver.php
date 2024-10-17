<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/'; 

include_once $rootPath . 'Functions/Common/Database.php';

class Driver {
    private $db;
    private $conn;

    // Constructor to initialize the Database connection
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Get Driver Details using the stored procedure
    public function getDriverDetails($driverID) {
        $query = "CALL GetDriverDetails(:driverID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':driverID', $driverID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get Driver Details using the stored procedure
    public function getDriverDetailsByUserID($userID) {
        // Call the stored procedure to get driver details
        $query = "CALL GetDriverDetailsByUserID(:userID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the result set
        $driverDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        return $driverDetails;
    }

    // Get Assigned Rides for the driver
    public function getAssignedRides($driverID) {
        // Fetch rides assigned to the driver
        $query = "SELECT Passenger_ID, Ride_ID, Taxi_ID, Start_Location, End_Location, Amount, Status FROM rides WHERE Driver_ID = :driverID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':driverID', $driverID, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update driver's availability by Driver_ID
    public function updateDriverAvailability($driverId) {
        try {
            // Fetch the current availability
            $query = "SELECT Availability FROM drivers WHERE Driver_ID = :driverId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':driverId', $driverId);
            $stmt->execute();
            $currentAvailability = $stmt->fetch(PDO::FETCH_ASSOC)['Availability'];
    
            // Toggle the availability
            $newAvailability = $currentAvailability == 1 ? 0 : 1;
    
            // Call the stored procedure to update the availability
            $updateQuery = "CALL UpdateDriverAvailabilityByRideStatus(:driverId, :newAvailability)";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':driverId', $driverId);
            $updateStmt->bindParam(':newAvailability', $newAvailability);
            $updateStmt->execute();
    
            return ['status' => 'success', 'newAvailability' => $newAvailability];
        } catch (PDOException $e) {
            // Check for the specific SQLSTATE code '45000' for custom error and handle it gracefully
            if ($e->getCode() == '45000') {
                return ['status' => 'error', 'message' => 'Cannot update availability. You have active rides in progress.'];
            } else {
                // Catch any other SQL or unexpected errors and display a generic error message
                error_log("Failed to update driver availability: " . $e->getMessage());
                return ['status' => 'error', 'message' => 'An unexpected error occurred. Please try again later.'];
            }
        }
    }
}

// Handle AJAX request for changing availability
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'changeAvailability') {
    $driverId = $_POST['driverId'];
    $driver = new Driver();
    $result = $driver->updateDriverAvailability($driverId);

    echo json_encode($result);
}

?>