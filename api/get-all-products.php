<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

// Check if connection failed (returns array with error)
if (is_array($conn) && isset($conn['error'])) {
    echo json_encode(['success' => false, 'error' => $conn['error']]);
    exit;
}

try {
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.category_id 
              WHERE p.is_active = TRUE 
              ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'products' => $products]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>