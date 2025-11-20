<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// Test database connection
include_once '../db_config/database.php';

$database = new Database();
$db = $database->getConnection();

if (is_array($db) && isset($db['error'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed",
        "error" => $db['error']
    ]);
} else {
    echo json_encode([
        "status" => "success",
        "message" => "API and database are working correctly!",
        "timestamp" => date('Y-m-d H:i:s'),
        "server" => $_SERVER['SERVER_SOFTWARE'],
        "php_version" => PHP_VERSION
    ]);
}
?>