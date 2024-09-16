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
        // Call the stored procedure to get driver details
        $query = "CALL GetDriverDetails(:driverID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':driverID', $driverID, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the result set
        $driverDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        return $driverDetails;
    }
}
?>
