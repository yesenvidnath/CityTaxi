<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/CityTaxi/'; 

include_once $rootPath . 'Functions/Common/Database.php';

class JsonHandler {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    public function readJsonFile($filePath) {
        if (file_exists($filePath)) {
            $jsonData = file_get_contents($filePath);
            return json_decode($jsonData, true) ?? [];
        }
        return [];
    }

    public function writeJsonFile($filePath, $data) {
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($filePath, $jsonData);
    }
    
}
?>
