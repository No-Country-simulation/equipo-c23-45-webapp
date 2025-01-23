<?php

class User
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function getAll()
    {
        $query = $this->db->prepare("SELECT * FROM users");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get($id)
    {
        $query = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $query->execute([':id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {   
        $uiid = uniqid(); // Generar un identificador único para el usuario
        $name = $data['name'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $query = "INSERT INTO users (uiid, name, email, password, status, created_at, update_at) 
                  VALUES (:uiid, :name, :email, :password, 1, NOW(), NOW())";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':uiid', $uiid);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        return ['id' => $this->db->lastInsertId(),'uiid' => $uiid, 'name' => $name, 'email' => $email];
    }


    public function saveToken($token, $expirationTime, $userId)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO `access_tokens` (`token`, `name`, `revoked`, `created_at`, `updated_at`, `user_id`) 
                VALUES (:token, :expires_at, :revoked, NOW(), NOW(), :user_id)
            ");
            $revoked = 0;
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expires_at', $expirationTime);
            $stmt->bindParam(':revoked', $revoked);
            $stmt->bindParam(':user_id', $userId);

            if ($stmt->execute()) {
                return true;
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to save token']);
                return false;
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            return false;
        }
    }
}
