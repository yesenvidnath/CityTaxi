<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/Functions/Common/Database.php';

class Financial {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function fetchAllTransactions() {
        $query = "SELECT 
                    p.Payment_ID, 
                    p.Payment_date, 
                    p.Payment_time, 
                    p.Amount, 
                    p.Driver_ID, 
                    p.Taxi_ID, 
                    p.Ride_ID, 
                    u.First_name, 
                    u.Last_name 
                  FROM Payments p
                  JOIN Drivers d ON p.Driver_ID = d.Driver_ID
                  JOIN Users u ON d.User_ID = u.user_ID";  // Joining with Users to get first name and last name
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    

    // Fetch invoice by payment ID
    public function fetchInvoiceByPayment($paymentId) {
        $query = "SELECT * FROM Invoices WHERE Payment_ID = :paymentId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Generate a new invoice
    public function generateInvoice($paymentId) {
        $query = "INSERT INTO Invoices (Payment_ID) VALUES (:paymentId)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);
        $stmt->execute();
        return $this->conn->lastInsertId();  // Return the ID of the new invoice
    }
}

?>
