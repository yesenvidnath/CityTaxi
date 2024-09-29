<?php
include_once 'Database.php';

class Taxi {
    private $db;
    private $conn;

    // Constructor to initialize the Database connection
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Fetch all taxis
    public function fetchAllTaxis() {
        $query = "SELECT Taxi_ID, Taxi_type, Plate_number, Registration_Date, RevenueLicence, Insurance_info FROM Taxis";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>