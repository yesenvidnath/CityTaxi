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
    }

    public function registerDriverVehicleOwner($data) {
        try {
            $this->conn->beginTransaction();

            // Insert into Users table
            $query = "INSERT INTO Users (user_type, password, Email, First_name, Last_name, NIC_No, mobile_number, Address, user_img) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $profilePath = $this->saveImage($data['profile_pic'], 'profile');
            $stmt->execute([
                $data['user_type'], 
                $data['password'], 
                $data['email'], 
                $data['first_name'], 
                $data['last_name'], 
                $data['nic_no'], 
                $data['contact_no'], 
                $data['address'], 
                $profilePath
            ]);

            $userId = $this->conn->lastInsertId();
            if ($stmt->rowCount() == 0) {
                throw new Exception("Failed to insert user data.");
            }

            // Insert into License table
            $queryLicense = "INSERT INTO License (User_ID, NIC_Img_Front, NIC_Img_Back, Drivers_license_Front, Drivers_license_Back, Drivers_license_No) 
                             VALUES (?, ?, ?, ?, ?, ?)";
            $stmtLicense = $this->conn->prepare($queryLicense);
            $nicFrontPath = $this->saveImage($data['nic_front'], 'nic_front');
            $nicBackPath = $this->saveImage($data['nic_back'], 'nic_back');
            $licenseFrontPath = $this->saveImage($data['license_front'], 'license_front');
            $licenseBackPath = $this->saveImage($data['license_back'], 'license_back');
            $stmtLicense->execute([
                $userId,
                $nicFrontPath,
                $nicBackPath,
                $licenseFrontPath,
                $licenseBackPath,
                $data['license_no']
            ]);

            $licenseId = $this->conn->lastInsertId();
            if ($stmtLicense->rowCount() == 0) {
                throw new Exception("Failed to insert license data.");
            }

            // Insert into Drivers or Vehicle Owner table
            if ($data['user_type'] == 'Driver') {
                $queryDriver = "INSERT INTO Drivers (User_ID, Licence_ID, Current_Location, Availability) VALUES (?, ?, ?, ?)";
                $stmtDriver = $this->conn->prepare($queryDriver);
                $stmtDriver->execute([$userId, $licenseId, 'Default Location', 1]); // Assuming default location and availability
            } elseif ($data['user_type'] == 'Vehicle Owner') {
                $queryOwner = "INSERT INTO Vehicle_Owner (User_ID) VALUES (?)";
                $stmtOwner = $this->conn->prepare($queryOwner);
                $stmtOwner->execute([$userId]);
            }

            $this->conn->commit();
            echo "Registration successful.";
        } catch (Exception $e) {
            $this->conn->rollBack();
            echo "Registration failed: " . $e->getMessage();
        }
    }

    private function saveImage($file, $type) {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error uploading file for $type.");
        }
        $targetDir = "../../Assets/img/Driver/" . ($type == 'profile' ? "" : $type . "/");
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0775, true);
        }
        $targetFile = $targetDir . basename($file['name']);
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            throw new Exception("Failed to save $type image.");
        }
        return basename($file['name']);
    }
}

$registration = new Registration();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $driverData = [
        'user_type' => $_POST['user_type'],
        'password' => $_POST['password'],
        'email' => $_POST['email'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'nic_no' => $_POST['nic_no'],
        'contact_no' => $_POST['contact_no'],
        'address' => $_POST['address'],
        'profile_pic' => $_FILES['profile_pic'],
        'nic_front' => $_FILES['nic_front'],
        'nic_back' => $_FILES['nic_back'],
        'license_front' => $_FILES['license_front'],
        'license_back' => $_FILES['license_back'],
        'license_no' => $_POST['license_no']
    ];
    $registration->registerDriverVehicleOwner($driverData);
}
?>
