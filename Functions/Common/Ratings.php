<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/Functions/Common/Database.php';

class Ratings {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Fetch all ratings
    public function fetchAllRatings() {
        $query = "SELECT r.Rating_ID, r.Rate, r.Comment, r.Driver_ID, r.Passenger_ID, u.First_name AS PassengerFirstName, u.Last_name AS PassengerLastName, d.First_name AS DriverFirstName, d.Last_name AS DriverLastName
                  FROM Ratings r
                  JOIN Users u ON r.Passenger_ID = u.user_ID
                  JOIN Drivers dr ON r.Driver_ID = dr.Driver_ID
                  JOIN Users d ON dr.User_ID = d.user_ID";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
