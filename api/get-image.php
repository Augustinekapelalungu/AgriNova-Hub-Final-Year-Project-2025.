<?php
require_once 'database.php';

$product_id = $_GET['product_id'] ?? '';

if (empty($product_id)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

$database = new Database();
$conn = $database->getConnection();

try {
    $query = "SELECT image_data, image_type FROM product_images WHERE product_id = :product_id AND is_primary = TRUE LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        header("Content-Type: " . $row['image_type']);
        echo $row['image_data'];
    } else {
        // Return default image if no image found
        header("Content-Type: image/jpeg");
        readfile('../images/organic-vegetables.jpg');
    }
    
} catch (PDOException $e) {
    header("Content-Type: image/jpeg");
    readfile('../images/organic-vegetables.jpg');
}
?>