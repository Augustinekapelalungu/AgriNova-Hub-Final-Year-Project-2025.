<?php
require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

// Check if connection is successful (it returns PDO object, not array)
if ($conn instanceof PDO) {
    echo json_encode(['success' => true, 'message' => 'Database connected successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
}
?>