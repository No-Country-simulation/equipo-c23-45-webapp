<?php

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];



// Routing
if ($requestUri === '/users' && $requestMethod === 'GET') {    
    require_once __DIR__ . '/../app/Controllers/UserController.php';
    (new UserController())->getAllUsers();
} elseif ($requestUri === '/login' && $requestMethod === 'POST') {
    require_once __DIR__ . '/../app/Controllers/UserController.php';
    (new UserController())->login();
} elseif (preg_match('/\/users\/(\d+)/', $requestUri, $matches) && $requestMethod === 'GET') {
     require_once __DIR__ . '/../app/Controllers/UserController.php';
    (new UserController())->getUser($matches[1]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}
