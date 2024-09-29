<?php
include_once 'Database.php'; // Make sure your Database connection file is correctly included

$db = new Database();
$conn = $db->getConnection();

$query = "SELECT DATE_FORMAT(Payment_date, '%Y-%m') as Month, COUNT(*) as TotalRides FROM Payments GROUP BY DATE_FORMAT(Payment_date, '%Y-%m')";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
