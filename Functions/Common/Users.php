<?php
include_once 'Database.php';
include_once 'SessionManager.php';

class Users {
    private $db;
    private $conn;

    // Constructor to initialize the Database connection
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Fetch all users (existing method)
    public function fetchAllUsers() {
        $query = "SELECT user_ID, First_name, Last_name, Email FROM Users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch user by email using stored procedure
    public function fetchUserByEmail($email) {
        $query = "CALL GetUserByEmail(:email)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Register a new user and add to passengers table
    public function registerUser($first_name, $last_name, $email, $password, $nic_no, $mobile, $address, $user_img) {
        try {
            $stmt = $this->conn->prepare("CALL RegisterUser(:first_name, :last_name, :email, :password, :nic_no, :address, :user_img)");
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':nic_no', $nic_no);
            $stmt->bindParam(':mobile_number', $mobile);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':user_img', $user_img);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Fetch user information by user_ID (New Method)
    public function fetchUserByID($userID) {
        // SQL query to get user details by user_ID
        $query = "SELECT user_ID, user_type, password, Email, First_name, Last_name, NIC_No, Address,mobile_number,  user_img FROM users WHERE user_ID = :userID";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return null; // User not found
        }
    }
}

// Add this section in Users.php to fetch user details by ID
if (isset($_GET['fetch_user_id'])) {
    $userID = $_GET['fetch_user_id'];
    $users = new Users(); // Assuming Users is the class name
    $userDetails = $users->fetchUserByID($userID);
    
    // Return the user details as JSON
    header('Content-Type: application/json');
    echo json_encode($userDetails);
    exit();
}
