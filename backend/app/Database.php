<?php

class Database {
    private $host = '127.0.0.1';
    private $db_name = 'backend-agro';
    private $username = 'root';
    private $password = '';
    private $conn;

/**
 * Establishes a connection to the database using PDO.
 *
 * @return PDO|null Returns a PDO connection object on success, or null on failure.
 * @throws PDOException If there is an error establishing the database connection.
 */


    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo json_encode(['error' => $exception->getMessage()]);
            exit;
        }
        return $this->conn;
    }
}