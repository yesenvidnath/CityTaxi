<?php
include_once 'Database.php'; // Make sure your Database connection file is correctly included

$db = new Database();
$conn = $db->getConnection();

$query = "SELECT d.First_name, AVG(r.Rate) as AvgRating FROM Ratings r JOIN Drivers d ON r.Driver_ID = d.Driver_ID GROUP BY r.Driver_ID";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
