<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $product_id = $input['product_id'] ?? '';
    $stock_quantity = $input['stock_quantity'] ?? 0;
    
    error_log("Updating product stock - ID: $product_id, Stock: $stock_quantity");

    try {
        $query = "UPDATE products SET stock_quantity = :stock_quantity WHERE product_id = :product_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':stock_quantity', $stock_quantity);
        
        if ($stmt->execute()) {
            $affectedRows = $stmt->rowCount();
            error_log("Stock updated - Affected rows: $affectedRows");
            echo json_encode([
                'success' => true,
                'affected_rows' => $affectedRows
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'error' => 'Failed to update stock'
            ]);
        }
        
    } catch(PDOException $e) {
        error_log("Stock update error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
?>