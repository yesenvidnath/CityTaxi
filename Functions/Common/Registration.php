<?php
// Include your database connection file
include_once 'Database.php'; 

$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
$nicNo = $_POST['nic_no'];
$contactNo = $_POST['contact_no'];
$address = $_POST['address'];
$userType = $_POST['user_type']; // Should be 'Passenger'

try {
    // Connect to the database
    $database = new Database();
    $db = $database->getConnection();

    // Insert into Users table
    $stmt = $db->prepare("INSERT INTO Users (First_name, Last_name, NIC_No, mobile_number, Address, user_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bindParam(1, $firstName);
    $stmt->bindParam(2, $lastName);
    $stmt->bindParam(3, $nicNo);
    $stmt->bindParam(4, $contactNo);
    $stmt->bindParam(5, $address);
    $stmt->bindParam(6, $userType); // 'Passenger' in this case

    if ($stmt->execute()) {
        // Get the last inserted user ID
        $userId = $db->lastInsertId();

        // Insert into Passengers table
        $stmt = $db->prepare("INSERT INTO Passengers (User_ID) VALUES (?)");
        $stmt->bindParam(1, $userId);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
} catch (Exception $e) {
    echo 'error: ' . $e->getMessage();
}
?>
