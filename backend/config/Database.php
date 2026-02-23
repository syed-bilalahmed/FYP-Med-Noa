<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'fyp_mediqu'; // Make sure to create this DB
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // First attempt: Connect directly to the database
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // If connection fails, check if it's because DB doesn't exist (Code 1049)
            if ($exception->getCode() == 1049) {
                return $this->createDatabase();
            } else {
                echo "Connection error: " . $exception->getMessage();
            }
        }
        return $this->conn;
    }

    private function createDatabase() {
        try {
            // Connect to MySQL without DB
            $pdo = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Create DB
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "`");
            $pdo->exec("USE `" . $this->db_name . "`");

            // Import Schema
            if (file_exists(__DIR__ . '/../schema.sql')) {
                $sql = file_get_contents(__DIR__ . '/../schema.sql');
                $pdo->exec($sql);
            }

            // Re-connect to the new DB
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $this->conn;

        } catch (PDOException $e) {
            die("Database Auto-Creation Failed: " . $e->getMessage());
        }
    }
}
