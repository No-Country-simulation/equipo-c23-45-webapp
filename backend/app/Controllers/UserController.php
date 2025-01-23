<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Database.php';
date_default_timezone_set('America/Bogota');

use Firebase\JWT\JWT;

class UserController
{
    private $userModel;
    private $secretKey = 'DE2A42DA12465A49EE1576C44754D';
    private $db;




    public function __construct()
    {
        $this->userModel = new User();
        $this->db = (new Database())->getConnection();
    }

    public function getAllUsers()
    {
        $users = $this->userModel->getAll();
        echo json_encode($users);
    }

    public function getUser($id)
    {
        $user = $this->userModel->get($id);
        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'User not found']);
        }
    }

    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['name'], $data['email'], $data['password'])) {
            //verificar si el email ya existe
            if($this->validateOnBD($data['email'])){
                http_response_code(400);
                echo json_encode(['message' => 'Email already exists']);
                return;
            }
            $user = $this->userModel->create($data);
            http_response_code(201);
            echo json_encode($user);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Los parámetros no están completos y son requeridos.']);
        }
    }
    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Username and password required']);
            return;
        }
        $user = $this->validateUser($data['email'], $data['password']);

        if ($user) {
            $validateStatus = $this->validateStatus($data['email']);

            if (!$validateStatus) {
                http_response_code(401);
                echo json_encode(['message' => 'User Inactive']);
                return;
            }


            $issuedAt = time();
            $expirationTime = $issuedAt + 7200;
            $expirationDate = date('Y-m-d H:i:s', $expirationTime);

            $payload = array(
                "iat" => $issuedAt,
                "exp" => $expirationTime,
                "uiid" => $user['uiid'],
                "email" => $user['email'],
                "name" => $user['name']
            );
            // Generar el token
            $jwt = JWT::encode($payload, $this->secretKey, 'HS256');

            //guadar token en tabla de token access
            $this->userModel->saveToken($jwt, $expirationDate, $user['id']);


            // Devolver el token
            http_response_code(200);
            echo json_encode([
                'message' => 'Login successful',
                "uiid" => $user['uiid'],
                "email" => $user['email'],
                "name" => $user['name'],
                'token' => $jwt
            ]);
        } else {
            // Si las credenciales son incorrectas
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }

    private function validateUser($username, $password)
    {
        $query = "SELECT id, uiid, email, name, password,status FROM users WHERE email = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }
        if (password_verify($password, $user['password'])) {
            return [
                'id' => $user['id'],
                'uiid' => $user['uiid'],
                'email' => $user['email'],
                'name' => $user['name']
            ];
        } else {
            return false;
        }
    }
    private function validateStatus($username)
    {
        $query = "SELECT id, uiid, email, name, password,status FROM users WHERE email = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }
        if ($user['status']) {
            return true;
        } else {
            return false;
        }
    }
    private function validateOnBD($username)
    {
        $query = "SELECT COUNT(*) AS total FROM users WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $username, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['total'] > 0) {
           return true;
        } else {
            return false;
        }
    }
}
