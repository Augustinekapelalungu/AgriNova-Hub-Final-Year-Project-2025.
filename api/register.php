<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database configuration
include_once '../db_config/database.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Check if connection failed
if (is_array($db) && isset($db['error'])) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed",
        "debug" => $db['error']
    ]);
    exit();
}

// Get POST data
$input = file_get_contents("php://input");
$data = json_decode($input);

// If no JSON data, try form data
if (!$data && !empty($_POST)) {
    $data = (object)$_POST;
}

// Validate input
if (!$data) {
    http_response_code(400);
    echo json_encode([
        "status" => "error", 
        "message" => "No data received"
    ]);
    exit();
}

// Check required fields
if (empty($data->fullname) || empty($data->email) || empty($data->password) || empty($data->userType)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error", 
        "message" => "All fields are required: fullname, email, password, userType"
    ]);
    exit();
}

// Validate email
if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error", 
        "message" => "Invalid email format"
    ]);
    exit();
}

// Check password match
if (isset($data->confirmPassword) && $data->password !== $data->confirmPassword) {
    http_response_code(400);
    echo json_encode([
        "status" => "error", 
        "message" => "Passwords do not match"
    ]);
    exit();
}

// Check password length
if (strlen($data->password) < 6) {
    http_response_code(400);
    echo json_encode([
        "status" => "error", 
        "message" => "Password must be at least 6 characters"
    ]);
    exit();
}

try {
    // Check if email already exists
    $check_query = "SELECT user_id FROM users WHERE email = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$data->email]);
    
    if ($check_stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            "status" => "error", 
            "message" => "Email already registered"
        ]);
        exit();
    }

    // Hash password
    $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);
    
    // Insert new user
    $query = "INSERT INTO users (fullname, email, phone, password, user_type, newsletter) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $success = $stmt->execute([
        $data->fullname,
        $data->email,
        $data->phone ?? '',
        $hashed_password,
        $data->userType,
        isset($data->newsletter) ? 1 : 0
    ]);

    if ($success) {
        $user_id = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Registration successful!",
            "user_id" => $user_id,
            "fullname" => $data->fullname,
            "email" => $data->email,
            "user_type" => $data->userType
        ]);
        
    } else {
        http_response_code(500);
        echo json_encode([
            "status" => "error", 
            "message" => "Unable to register user"
        ]);
    }
    
} catch (PDOException $exception) {
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Database error: " . $exception->getMessage()
    ]);
}
?>