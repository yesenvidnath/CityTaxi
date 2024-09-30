<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/'; 

include_once $rootPath . 'Functions/Common/Database.php';

class Alert {
    private $db;
    private $conn;

    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
}

?>