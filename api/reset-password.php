<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: *");

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

// Get posted data
$input = file_get_contents("php://input");
$data = json_decode($input);

if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "No data received"]);
    exit();
}

if (empty($data->newPassword) || empty($data->confirmPassword)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Both password fields are required"]);
    exit();
}

if ($data->newPassword !== $data->confirmPassword) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit();
}

if (strlen($data->newPassword) < 6) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Password must be at least 6 characters long"]);
    exit();
}

try {
    // In a real application, you would:
    // 1. Verify the reset token
    // 2. Check if it's not expired
    // 3. Update the password
    
    // For demo, we'll update the password directly if email is provided
    if (!empty($data->email)) {
        $hashed_password = password_hash($data->newPassword, PASSWORD_DEFAULT);
        
        $query = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $db->prepare($query);
        $success = $stmt->execute([$hashed_password, $data->email]);
        
        if ($success) {
            echo json_encode([
                "status" => "success",
                "message" => "Password reset successfully! You can now login with your new password."
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to reset password"]);
        }
    } else {
        echo json_encode([
            "status" => "success",
            "message" => "Password reset successful! (Demo mode)",
            "debug" => "In a real application, the password would be updated in the database."
        ]);
    }
    
} catch (PDOException $exception) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error: " . $exception->getMessage()]);
}
?>