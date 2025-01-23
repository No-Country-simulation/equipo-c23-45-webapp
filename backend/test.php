<?php
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;

$key = "example_key";
$payload = [
    "iss" => "http://example.org",
    "aud" => "http://example.com",
    "iat" => time(),
    "nbf" => time() + 10,
];

try {
    $jwt = JWT::encode($payload, $key, 'HS256');
    echo "Generated Token: " . $jwt . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}