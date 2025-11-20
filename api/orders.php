<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

include_once '../db_config/database.php';

$database = new Database();
$db = $database->getConnection();

// Check if connection failed
if (is_array($db) && isset($db['error'])) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $db['error']]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "No data received"]);
    exit();
}

if (empty($data->user_id) || empty($data->items) || empty($data->total_amount)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required order data"]);
    exit();
}

try {
    // Start transaction
    $db->beginTransaction();
    
    // Insert order
    $order_query = "INSERT INTO orders (user_id, total_amount, payment_method, status) 
                    VALUES (?, ?, ?, 'completed')";
    $order_stmt = $db->prepare($order_query);
    $order_stmt->execute([
        $data->user_id,
        $data->total_amount,
        $data->payment_method
    ]);
    
    $order_id = $db->lastInsertId();
    
    // Insert order items
    $item_query = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) 
                   VALUES (?, ?, ?, ?, ?)";
    $item_stmt = $db->prepare($item_query);
    
    foreach ($data->items as $item) {
        $item_stmt->execute([
            $order_id,
            $item->id,
            $item->name,
            $item->quantity,
            $item->price
        ]);
    }
    
    // Commit transaction
    $db->commit();
    
    echo json_encode([
        "status" => "success",
        "message" => "Order placed successfully!",
        "order_id" => $order_id
    ]);
    
    error_log("Order #$order_id placed successfully for user {$data->user_id}");
    
} catch (PDOException $exception) {
    // Rollback transaction on error
    $db->rollBack();
    
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Failed to place order: " . $exception->getMessage()
    ]);
    error_log("Order error: " . $exception->getMessage());
}
?>