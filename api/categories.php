<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../db_config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM products p WHERE p.category_id = c.category_id AND p.is_active = 1) as product_count 
          FROM categories c 
          ORDER BY c.name";
$stmt = $db->prepare($query);
$stmt->execute();

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

http_response_code(200);
echo json_encode($categories);
?>