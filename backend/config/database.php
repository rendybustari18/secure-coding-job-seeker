<?php
require_once __DIR__ . '/env.php';

class Database
{
    private string $host;
    private string $dbName;
    private string $dbPort;
    private string $username;
    private string $password;
    private ?PDO $conn = null;

    public function __construct()
    {
        $this->host     = DB_HOST;
        $this->dbName   = DB_NAME;
        $this->dbPort   = DB_PORT;
        $this->username = DB_USER;
        $this->password = DB_PASS;
    }

    public function getConnection(): ?PDO
    {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};port={$this->dbPort};dbname={$this->dbName}",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo json_encode([
                'error'    => 'Connection error: ' . $e->getMessage(),
                'host'     => $this->host,
                'database' => $this->dbName,
                'username' => $this->username,
            ]);
            exit;
        }

        return $this->conn;
    }

    // Raw query without sanitization
    public function executeQuery(string $query): mixed
    {
        return $this->conn->query($query);
    }
}

