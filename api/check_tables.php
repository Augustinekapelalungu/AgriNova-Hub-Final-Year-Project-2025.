<?php
require_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

if (is_array($conn) && isset($conn['error'])) {
    echo "❌ Database connection failed: " . $conn['error'];
    exit;
}

try {
    // Check products table
    $stmt = $conn->query("SHOW TABLES LIKE 'products'");
    $productsTableExists = $stmt->rowCount() > 0;
    
    // Check categories table
    $stmt = $conn->query("SHOW TABLES LIKE 'categories'");
    $categoriesTableExists = $stmt->rowCount() > 0;
    
    echo "Products table: " . ($productsTableExists ? "✅ EXISTS" : "❌ MISSING") . "<br>";
    echo "Categories table: " . ($categoriesTableExists ? "✅ EXISTS" : "❌ MISSING") . "<br>";
    
    if ($productsTableExists) {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM products");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Products in database: " . $count['count'] . "<br>";
    }
    
} catch (PDOException $e) {
    echo "Error checking tables: " . $e->getMessage();
}
?>