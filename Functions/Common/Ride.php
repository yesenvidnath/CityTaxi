<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/Functions/Common/Database.php';

// ride.php

function getTaxiTypes() {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "CALL GetTaxiTypes()";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();
    return $result;
}

?>

