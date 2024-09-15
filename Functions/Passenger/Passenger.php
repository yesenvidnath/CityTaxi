<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/'; 

include_once $rootPath . 'Functions/Common/Database.php';
include_once $rootPath . 'Functions/Common/Users.php';

class Passenger {
    private $db;
    private $conn;

    // Constructor to initialize the Database connection
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Get Passenger ID based on User ID
    public function getPassengerIDByUserID($userID) {
        $query = "SELECT Passenger_ID FROM Passengers WHERE User_ID = :userID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['Passenger_ID'];
        }
        
        return null;
    }

    // Get Passenger Details using the stored procedure
    public function getPassengerDetails($userID) {
        // Get Passenger ID from User ID
        $passengerID = $this->getPassengerIDByUserID($userID);

        if (!$passengerID) {
            return null; // No passenger found
        }

        // Call the stored procedure to get passenger details
        $query = "CALL GetPassengerDetails(:passengerID)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':passengerID', $passengerID, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch multiple result sets
        $results = [];
        do {
            $results[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } while ($stmt->nextRowset());

        return $results;
    }

    // Get Passenger's User Information using the Users class
    public function getPassengerUserInfo($userID) {
        $users = new Users();
        return $users->fetchUserByID($userID); 
    }
}
?>
