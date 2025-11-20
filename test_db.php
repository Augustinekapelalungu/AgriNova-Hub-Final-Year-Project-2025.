<?php
header("Content-Type: application/json; charset=UTF-8");

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
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $exception) {
            return ["error" => "Connection failed: " . $exception->getMessage()];
        }
    }
}

$database = new Database();
$connection = $database->getConnection();

if (is_array($connection) && isset($connection['error'])) {
    echo json_encode(["status" => "error", "message" => $connection['error']]);
} else {
    echo json_encode(["status" => "success", "message" => "Database connected successfully!"]);
}
?>