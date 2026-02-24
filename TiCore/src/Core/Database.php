<?php
// TiCore/src/Core/Database.php
namespace TiCore\Core;
use PDO;
use PDOException;

class Database {
    public $pdo = null;

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
            // In production, log this instead of echoing
            die("Database Connection Failed: " . $e->getMessage());
        }
    }
}