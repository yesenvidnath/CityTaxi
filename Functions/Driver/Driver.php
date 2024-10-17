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
}

?>