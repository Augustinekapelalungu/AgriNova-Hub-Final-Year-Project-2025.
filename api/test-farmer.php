<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

echo json_encode([
    "status" => "success",
    "message" => "Farmer API is working!",
    "timestamp" => date('Y-m-d H:i:s'),
    "data" => [
        "test_products" => [
            ["id" => 1, "title" => "Test Tomatoes", "price" => 50.00],
            ["id" => 2, "title" => "Test Potatoes", "price" => 30.00]
        ]
    ]
]);
?>