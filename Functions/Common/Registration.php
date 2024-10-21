<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/Functions/Common/Database.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

class Registration {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        if ($this->conn->connect_error) {
            die('Connect Error (' . $this->conn->connect_errno . ') '
                . $this->conn->connect_error);
        }
    }

    // Method to hash password using SHA-256
    private function hashPassword($password) {
        return hash('sha256', $password); // Hash the password using SHA-256
    }

    public function registerPassenger($data) {
        // Start a transaction
        $this->conn->beginTransaction();
    
        try {
            // Hash the password before storing it
            $hashedPassword = $this->hashPassword($data['password']); 

            // Query to insert user into the Users table
            $query = "INSERT INTO Users (First_name, Last_name, NIC_No, mobile_number, Address, Email, password, user_img, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $this->conn->errorInfo()[2]);
            }
    
            // Binding parameters for PDO
            $filePath = $this->saveProfileImage($data['profile_pic']);  // Save the profile image and get the file name
            $stmt->bindParam(1, $data['first_name']);
            $stmt->bindParam(2, $data['last_name']);
            $stmt->bindParam(3, $data['nic_no']);
            $stmt->bindParam(4, $data['contact_no']);
            $stmt->bindParam(5, $data['address']);
            $stmt->bindParam(6, $data['email']);
            $stmt->bindParam(7, $hashedPassword); // Use the hashed password here
            $stmt->bindParam(8, $filePath);
            $stmt->bindParam(9, $data['user_type']);
    
            // Execute the user insertion
            $stmt->execute();
            $lastUserId = $this->conn->lastInsertId();  // Get the last inserted user ID
    
            if ($stmt->rowCount() > 0) {
                // Insert into Passengers table
                $queryPassenger = "INSERT INTO Passengers (User_ID) VALUES (?)";
                $stmtPassenger = $this->conn->prepare($queryPassenger);
                $stmtPassenger->bindParam(1, $lastUserId);
                $stmtPassenger->execute();
    
                if ($stmtPassenger->rowCount() > 0) {
                    // Commit the transaction
                    $this->conn->commit();
                    echo "Registration successful, new passenger created.";
                } else {
                    throw new Exception("Failed to create passenger record.");
                }
            } else {
                throw new Exception("No rows affected in Users table, but the query was executed.");
            }
        } catch (Exception $e) {
            // Rollback the transaction on error
            $this->conn->rollBack();
            echo "Registration failed: " . $e->getMessage();
        }
    
        $stmt->closeCursor();
    }

    // Method to save profile image
    private function saveProfileImage($file) {
        $targetDir = "../../Assets/img/Passenger/";
        $targetFile = $targetDir . basename($file['name']);
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            die('Failed to save file');
        }
        return basename($file['name']);
    }
}

$registration = new Registration();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_FILES['profile_pic'])) {
        die('Profile picture is required.');
    }
    $passengerData = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'nic_no' => $_POST['nic_no'],
        'contact_no' => $_POST['contact_no'],
        'address' => $_POST['address'],
        'email' => $_POST['email'],
        'password' => $_POST['password'], // Password to be hashed
        'profile_pic' => $_FILES['profile_pic'],
        'user_type' => 'Passenger'
    ];
    $registration->registerPassenger($passengerData);
}

?>
