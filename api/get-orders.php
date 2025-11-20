<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include database configuration
include '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if connection is valid
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    $query = "SELECT * FROM orders ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query');
    }
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'count' => count($orders)
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>