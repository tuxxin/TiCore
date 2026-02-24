<?php
// TiCore/src/Controllers/HomeController.php
namespace TiCore\Controllers;
use TiCore\Core\Database;

class HomeController {
    public function index() {
        $db = new Database();
        
        $status = "Database is " . ($db->pdo ? "CONNECTED" : "DISABLED");
        
        view('home', [
            'title' => 'Welcome Home',
            'db_status' => $status
        ]);
    }
}
