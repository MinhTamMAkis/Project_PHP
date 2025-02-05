<?php
class Database {
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    private function __construct() {
        $host = _HOST;
        $db   = _DB;
        $user = _USER;
        $pass = _PASS;
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT         => true,  // Kết nối Persistent giúp tái sử dụng connection
        ];

        try {
            $this->connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            die("Kết nối database thất bại: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): ?PDO {
        return $this->connection;
    }

    private function __clone() {}  // Ngăn chặn clone object
    public function __wakeup() {} // Ngăn chặn unserialize object
}
?>
