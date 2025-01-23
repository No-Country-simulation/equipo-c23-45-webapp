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
        $query = $this->db->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        $query->execute([':name' => $data['name'], ':email' => $data['email']]);
        return ['id' => $this->db->lastInsertId(), 'name' => $data['name'], 'email' => $data['email']];
    }


    public function saveToken($token, $expirationTime, $userId)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO `access_tokens` (`token`, `expires_at`, `revoked`, `created_at`, `updated_at`, `user_id`) 
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
