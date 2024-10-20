<?php

include_once __DIR__ . '/Database.php';
include_once __DIR__ . '/Texts.php'; // Include Texts class

class Reservation {
    private $db;
    private $conn;

    // Constructor to initialize the Database connection
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Method to add a new reservation
    public function addReservation($firstName, $lastName, $email, $phoneNumber, $startLocation, $endLocation, $driverID) {
        try {
            // Prepare the stored procedure call
            $query = "CALL AddReservationAndUpdateDriver(:firstName, :lastName, :email, :phoneNumber, :startLocation, :endLocation, :driverID)";
            $stmt = $this->conn->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phoneNumber', $phoneNumber);
            $stmt->bindParam(':startLocation', $startLocation);
            $stmt->bindParam(':endLocation', $endLocation);
            $stmt->bindParam(':driverID', $driverID);

            // Execute the stored procedure
            if ($stmt->execute()) {
                // Fetch the driver's phone number from the users table
                $driverQuery = "SELECT mobile_number, First_name, Last_name FROM users WHERE user_ID = (SELECT User_ID FROM drivers WHERE Driver_ID = :driverID)";
                $driverStmt = $this->conn->prepare($driverQuery);
                $driverStmt->bindParam(':driverID', $driverID);
                $driverStmt->execute();
                $driverInfo = $driverStmt->fetch(PDO::FETCH_ASSOC);

                // Check if driver info is found
                if ($driverInfo) {
                    // Prepare the SMS details for the passenger and the driver
                    $passengerSms = "Dear $firstName $lastName, your ride from $startLocation to $endLocation has been confirmed.";
                    $driverSms = "Dear {$driverInfo['First_name']} {$driverInfo['Last_name']}, you have been assigned a ride from $startLocation to $endLocation. Please contact the passenger at $phoneNumber.";

                    // Create an instance of the Texts class
                    $texts = new Texts();
                    
                    // Send SMS to both the driver and passenger
                    $texts->sendSmsForReservations($phoneNumber, $passengerSms); // Send SMS to passenger
                    $texts->sendSmsForReservations($driverInfo['mobile_number'], $driverSms); // Send SMS to driver
                }

                return true;
            }
            return false;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Method to handle AJAX requests
    public function handleAjaxRequest($data) {
        if (isset($data['first_name'], $data['last_name'], $data['email'], $data['phone_number'], $data['start_location'], $data['end_location'], $data['driver_id'])) {
            return $this->addReservation($data['first_name'], $data['last_name'], $data['email'], $data['phone_number'], $data['start_location'], $data['end_location'], $data['driver_id']);
        }
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $reservation = new Reservation();
    $response = $reservation->handleAjaxRequest($data);

    if ($response) {
        // Return a valid JSON response to avoid the error
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
