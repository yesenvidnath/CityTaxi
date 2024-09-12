<?php

class Database {
    private $conn;

    // Open connection to the MySQL database
    public function open() {
        $dotenv = parse_ini_file(__DIR__ . '/../../env/.env');
        $host = $dotenv['DB_HOST'];
        $dbname = $dotenv['DB_NAME'];
        $user = $dotenv['DB_USER'];
        $password = $dotenv['DB_PASSWORD'];

        try {
            // Establish MySQL connection using PDO without specifying the port
            $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        return $this->conn;
    }

    // Close the connection
    public function close() {
        $this->conn = null;
    }

    // Get the active connection
    public function getConnection() {
        if ($this->conn === null) {
            $this->open();
        }
        return $this->conn;
    }
}
