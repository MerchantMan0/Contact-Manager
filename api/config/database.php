<?php
class Database {

    $env = parse_ini_file('.env');

    private $host = "localhost";//replace with ip
    private $dbName = "database";//replace with actual name
    private $username = "admin";
    private $password = $env["PASSWORD"];//db pwd
    public $conn;//db connection

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbName,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>