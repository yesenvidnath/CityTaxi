<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/Functions/Common/Database.php';

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
}

?>
