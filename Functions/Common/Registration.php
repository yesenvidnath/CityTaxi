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

    public function registerPassenger($data) {
        $query = "INSERT INTO Users (First_name, Last_name, NIC_No, mobile_number, Address, Email, password, user_img, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die('Prepare failed: ' . $this->conn->errorInfo()[2]);
        }
    
        $filePath = $this->saveProfileImage($data['profile_pic']);
    
        // Binding parameters for PDO
        $stmt->bindParam(1, $data['first_name']);
        $stmt->bindParam(2, $data['last_name']);
        $stmt->bindParam(3, $data['nic_no']);
        $stmt->bindParam(4, $data['contact_no']);
        $stmt->bindParam(5, $data['address']);
        $stmt->bindParam(6, $data['email']);
        $stmt->bindParam(7, $data['password']);
        $stmt->bindParam(8, $filePath);
        $stmt->bindParam(9, $data['user_type']);
    
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                echo "Registration successful, rows affected: " . $stmt->rowCount();
            } else {
                echo "No rows affected, but the query was executed.";
            }
        } else {
            echo "Registration failed: " . implode(", ", $stmt->errorInfo());
        }
        $stmt->closeCursor();
    }    

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
        'password' => $_POST['password'],
        'profile_pic' => $_FILES['profile_pic'],
        'user_type' => 'Passenger'
    ];
    $registration->registerPassenger($passengerData);
}
?>
