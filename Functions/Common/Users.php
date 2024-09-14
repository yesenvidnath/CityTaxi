<?php
include_once 'Database.php';

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
}
