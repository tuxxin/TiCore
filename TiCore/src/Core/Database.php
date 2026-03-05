<?php
// TiCore/src/Core/Database.php
namespace TiCore\Core;
use PDO;
use PDOException;

class Database {
    public ?PDO $pdo = null;

    public function __construct() {
        if (!defined('DB_ENABLED') || DB_ENABLED === false) {
            return; // Database is disabled
        }

        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            Logger::critical('Database connection failed: ' . $e->getMessage());
            http_response_code(503);
            die('Service temporarily unavailable.');
        }
    }
}