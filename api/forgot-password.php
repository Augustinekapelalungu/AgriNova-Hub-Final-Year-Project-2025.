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

if (empty($data->name) || empty($data->email) || empty($data->message)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Name, email, and message are required"]);
    exit();
}

try {
    $query = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    
    $success = $stmt->execute([
        $data->name,
        $data->email,
        $data->subject ?? '',
        $data->message
    ]);

    if ($success) {
        echo json_encode([
            "status" => "success", 
            "message" => "Message sent successfully! We'll get back to you soon!"
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Unable to send message"]);
    }
} catch (PDOException $exception) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $exception->getMessage()]);
}
?>