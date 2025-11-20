<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

include_once '../db_config/database.php';

$database = new Database();
$db = $database->getConnection();

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

if (empty($data->farmer_id) || empty($data->title) || empty($data->price)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Farmer ID, title, and price are required"]);
    exit();
}

try {
    $query = "INSERT INTO farmer_products (farmer_id, title, description, category, price, quantity, location, contact_phone) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $success = $stmt->execute([
        $data->farmer_id,
        $data->title,
        $data->description ?? '',
        $data->category ?? 'General',
        $data->price,
        $data->quantity ?? 1,
        $data->location ?? '',
        $data->contact_phone ?? ''
    ]);

    if ($success) {
        $product_id = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Product uploaded successfully!",
            "product_id" => $product_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to upload product"]);
    }
    
} catch (PDOException $exception) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $exception->getMessage()]);
}
?>