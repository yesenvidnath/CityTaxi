<?php
include_once 'Database.php'; 

class Registration {
    private $db;

    // Constructor to establish DB connection
    public function __construct($db) {
        $this->db = $db;
    }

    // Method to catch data from ride.js via POST
    public function handleRegistrationData() {
        // Get the raw POST data (JSON)
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            // Return error if no data was received
            echo json_encode(['status' => 'error', 'message' => 'No data received']);
            return;
        }

        $firstName = $data['firstName'] ?? '';
        $lastName = $data['lastName'] ?? '';
        $nicNo = $data['nicNo'] ?? '';
        $contactNo = $data['contactNo'] ?? '';
        $address = $data['address'] ?? '';
        $email = $data['email'] ?? '';
        $password = password_hash($data['password'], PASSWORD_DEFAULT);  // Hash the password
        $userType = $data['userType'] ?? '';

        try {
            // Start transaction
            $this->db->beginTransaction();

            // Insert into the Users table
            $sql = "INSERT INTO Users (user_type, password, Email, First_name, Last_name, NIC_No, mobile_number, Address)
                    VALUES (:user_type, :password, :email, :first_name, :last_name, :nic_no, :contact_no, :address)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_type', $userType);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':nic_no', $nicNo);
            $stmt->bindParam(':contact_no', $contactNo);
            $stmt->bindParam(':address', $address);
            $stmt->execute();

            // Get the last inserted user ID
            $userId = $this->db->lastInsertId();

            // Insert into specific table based on user type
            if ($userType === 'Passenger') {
                $this->registerPassenger($userId);
            } elseif ($userType === 'Driver') {
                $this->registerDriver($userId, $data['nicFront'], $data['nicBack'], $data['driverLicense']);
            } elseif ($userType === 'Vehicle Owner') {
                $this->registerVehicleOwner($userId);
            }

            // Commit the transaction
            $this->db->commit();

            // Return success response
            echo json_encode(['status' => 'success', 'message' => 'Registration completed successfully!']);
        } catch (PDOException $e) {
            // Rollback transaction in case of error
            $this->db->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    // Method to register Passenger
    private function registerPassenger($userId) {
        $sql = "INSERT INTO Passengers (User_ID) VALUES (:user_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }

    // Method to register Driver
    private function registerDriver($userId, $nicFront, $nicBack, $driverLicense) {
        // Save the NIC images, for example, in /uploads/
        $nicFrontPath = '/uploads/' . basename($nicFront['name']);
        $nicBackPath = '/uploads/' . basename($nicBack['name']);
        move_uploaded_file($nicFront['tmp_name'], $nicFrontPath);
        move_uploaded_file($nicBack['tmp_name'], $nicBackPath);

        $sql = "INSERT INTO Drivers (User_ID, Licence_ID, Current_Location, Availability) 
                VALUES (:user_id, :licence_id, 'N/A', 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindValue(':licence_id', null);  // Assuming Licence_ID will be updated later
        $stmt->execute();
    }

    // Method to register Vehicle Owner
    private function registerVehicleOwner($userId) {
        $sql = "INSERT INTO Vehicle_Owner (User_ID) VALUES (:user_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }
}
?>
