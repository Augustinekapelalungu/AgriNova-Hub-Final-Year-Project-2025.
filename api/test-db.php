<?php
require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

if ($conn instanceof PDO) {
    echo json_encode([
        'success' => true,
        'message' => 'Database connected successfully!'
    ]);
} elseif (is_array($conn) && isset($conn['error'])) {
    // in case your Database class returns an array with an 'error' key
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn['error']
    ]);
} else {
    // fallback for null/false or unexpected return types
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed.'
    ]);
}
?>
