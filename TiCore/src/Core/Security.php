<?php
namespace TiCore\Core;

class Security {
    
    // 1. Generate CSRF Token
    public static function generateCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // 2. Verify CSRF Token
    public static function verifyCsrfToken(string $token): bool {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            // Use only the first IP from X-Forwarded-For to avoid logging spoofed proxy chains
            $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
            $ip = $forwarded !== ''
                ? trim(explode(',', $forwarded)[0])
                : $_SERVER['REMOTE_ADDR'];
            Logger::error("CSRF Token Mismatch from IP: " . $ip);
            http_response_code(403);
            die("Invalid Request (CSRF match failed)");
        }
        return true;
    }

    // 3. XSS Protection (Output Sanitization)
    public static function e(string $string): string {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
