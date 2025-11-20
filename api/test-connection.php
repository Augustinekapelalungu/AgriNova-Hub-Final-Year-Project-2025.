<?php
require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo json_encode(['success' => true, 'message' => 'Database connected successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
}
?>