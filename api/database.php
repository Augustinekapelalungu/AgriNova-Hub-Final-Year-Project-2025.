<?php
class Database {
    private $host = "localhost";
    private $db_name = "agrinova_hub";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
            echo "✅ Database connected successfully<br>";
            return $this->conn;
            
        } catch(PDOException $exception) {
            echo "❌ Database connection error: " . $exception->getMessage() . "<br>";
            error_log("Database connection failed: " . $exception->getMessage());
            return null;
        }
    }
}

// Test the connection
$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "🎉 Database is working perfectly!";
} else {
    echo "💥 Database connection failed!";
}
?>