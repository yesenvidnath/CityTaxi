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

    // Add a rating
    public function addRating($rideId, $driverId, $rate, $comment) {
        try {
            $query = "CALL AddRating(:rideId, :driverId, :rate, :comment)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':rideId', $rideId);
            $stmt->bindParam(':driverId', $driverId);
            $stmt->bindParam(':rate', $rate);
            $stmt->bindParam(':comment', $comment);
            
            if ($stmt->execute()) {
                return true;
            } else {
                // Get and return error info from SQL execution
                $errorInfo = $stmt->errorInfo();
                return "SQL Error: " . $errorInfo[2];
            }
        } catch (Exception $e) {
            // Catch and return any exceptions
            return "Exception: " . $e->getMessage();
        }
    }

    // Check if a rating exists for a specific ride ID
    public function ratingExists($rideId) {
        try {
            $query = "CALL CheckRatingExists(:rideId)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':rideId', $rideId);
            $stmt->execute();
            
            // Fetch the result, and use 'rating_exists' instead of 'exists'
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['rating_exists'] > 0;
        } catch (Exception $e) {
            error_log("Error checking rating existence: " . $e->getMessage());
            return false;
        }
    }
    

        
}

// Handle AJAX request for adding a rating
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'addRating') {
    $rideId = $_POST['rideId'];
    $driverId = $_POST['driverId'];
    $rate = $_POST['rate'];
    $comment = $_POST['comment'];

    $ratings = new Ratings();
    $result = $ratings->addRating($rideId, $driverId, $rate, $comment);

    if ($result === true) {
        echo "success";
    } else {
        echo $result;  // Return the detailed error message from the function
    }
}

?>
