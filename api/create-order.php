<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Debug: Log the incoming request
error_log("Create Order API Called: " . file_get_contents('php://input'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $user_id = $input['user_id'] ?? '';
    $total_amount = $input['total_amount'] ?? 0;
    $payment_method = $input['payment_method'] ?? 'Cash on Delivery';
    $status = $input['status'] ?? 'pending';
    $items = $input['items'] ?? [];
    
    error_log("Order Data - User: $user_id, Total: $total_amount, Items: " . count($items));

    try {
        // Start transaction
        $db->beginTransaction();
        
        // Insert order
        $orderQuery = "INSERT INTO orders (user_id, total_amount, payment_method, status) 
                       VALUES (:user_id, :total_amount, :payment_method, :status)";
        $orderStmt = $db->prepare($orderQuery);
        $orderStmt->bindParam(':user_id', $user_id);
        $orderStmt->bindParam(':total_amount', $total_amount);
        $orderStmt->bindParam(':payment_method', $payment_method);
        $orderStmt->bindParam(':status', $status);
        
        if (!$orderStmt->execute()) {
            throw new Exception('Failed to insert order');
        }
        
        $order_id = $db->lastInsertId();
        error_log("Order created with ID: $order_id");
        
        // Insert order items
        $itemsCount = 0;
        foreach ($items as $item) {
            $itemQuery = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) 
                          VALUES (:order_id, :product_id, :product_name, :quantity, :price)";
            $itemStmt = $db->prepare($itemQuery);
            $itemStmt->bindParam(':order_id', $order_id);
            $itemStmt->bindParam(':product_id', $item['id']);
            $itemStmt->bindParam(':product_name', $item['name']);
            $itemStmt->bindParam(':quantity', $item['quantity']);
            $itemStmt->bindParam(':price', $item['price']);
            
            if ($itemStmt->execute()) {
                $itemsCount++;
            } else {
                error_log("Failed to insert order item: " . json_encode($item));
            }
        }
        
        // Commit transaction
        $db->commit();
        
        error_log("Order completed: $itemsCount items added");
        
        echo json_encode([
            'success' => true,
            'order_id' => $order_id,
            'items_count' => $itemsCount,
            'message' => 'Order created successfully'
        ]);
        
    } catch(PDOException $e) {
        // Rollback transaction on error
        $db->rollBack();
        error_log("Database Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    } catch(Exception $e) {
        $db->rollBack();
        error_log("General Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method'
    ]);
}
?>