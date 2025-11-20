<?php
header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html>
<html>
<head>
    <title>AgriNOVA Hub - Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { background: #f4f4f4; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .warning { color: orange; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🌱 AgriNOVA Hub Database Setup</h1>";

// Check if PDO is available
if (!extension_loaded('pdo_mysql')) {
    echo "<p class='error'>❌ PDO MySQL extension is not enabled.</p>";
    echo "<p>Please enable it in your php.ini file by adding/uncommenting: <code>extension=pdo_mysql</code></p>";
    echo "</body></html>";
    exit();
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'agrinova_hub';

try {
    // Connect to MySQL server without PDO attributes first
    $pdo = new PDO("mysql:host=$host", $username, $password);
    
    // Try to set attribute, but continue if it fails
    if (defined('PDO::ATTR_ERRMODE')) {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    echo "<p class='success'>✓ Connected to MySQL server successfully</p>";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database");
    $pdo->exec("USE $database");
    
    echo "<p class='success'>✓ Database '$database' created/selected successfully</p>";
    
    // Read and execute SQL file
    $sql_file = 'agrinova_hub.sql';
    if (file_exists($sql_file)) {
        $sql = file_get_contents($sql_file);
        
        // Split SQL statements and execute them one by one
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore duplicate key errors and continue
                    if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                        echo "<p class='warning'>⚠️ SQL statement skipped: " . $e->getMessage() . "</p>";
                    }
                }
            }
        }
        
        echo "<p class='success'>✓ Database tables created successfully</p>";
        echo "<p class='success'>✓ Sample data inserted successfully</p>";
    } else {
        throw new Exception("SQL file not found: $sql_file");
    }
    
    echo "<div class='info'>";
    echo "<h2>🎉 Setup Complete!</h2>";
    echo "<p>Your AgriNOVA Hub database has been successfully set up.</p>";
    echo "<p><strong>Test Accounts Created:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Farmer Account:</strong> farmer@test.com / password123</li>";
    echo "<li><strong>Customer Account:</strong> customer@test.com / password123</li>";
    echo "</ul>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li><a href='../api/test.php' target='_blank'>Test API Connection</a></li>";
    echo "<li><a href='../signup.html' target='_blank'>Test Signup Page</a></li>";
    echo "<li><a href='../login.html' target='_blank'>Test Login with pre-created accounts</a></li>";
    echo "</ol>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Database Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>Is XAMPP/WAMP running?</li>";
    echo "<li>Are Apache and MySQL started?</li>";
    echo "<li>Is your MySQL password correct? (default is empty)</li>";
    echo "</ul>";
    
    // Try alternative connection method
    echo "<p class='warning'>Trying alternative connection method...</p>";
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        echo "<p class='success'>✓ Alternative connection successful!</p>";
    } catch (PDOException $e2) {
        echo "<p class='error'>❌ Alternative connection also failed: " . $e2->getMessage() . "</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>