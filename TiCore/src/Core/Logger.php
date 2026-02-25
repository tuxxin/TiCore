<?php
namespace TiCore\Core;

/**
 * TiCore Logger
 *
 * Log levels (set via LOG_LEVEL constant or LOG_LEVEL in .env):
 *   0 = CRITICAL  — fatal errors only
 *   1 = ERROR     — runtime errors (+ critical)
 *   2 = WARNING   — warnings and notices (+ error + critical)
 *   3 = INFO      — informational messages (+ above)
 *   4 = DEPRECATED — deprecation notices (+ above)
 *   5 = DEBUG     — everything, including strict-mode and debug traces
 */
class Logger {

    const LEVEL_CRITICAL   = 0;
    const LEVEL_ERROR      = 1;
    const LEVEL_WARNING    = 2;
    const LEVEL_INFO       = 3;
    const LEVEL_DEPRECATED = 4;
    const LEVEL_DEBUG      = 5;

    private static array $levelNames = [
        0 => 'CRITICAL',
        1 => 'ERROR',
        2 => 'WARNING',
        3 => 'INFO',
        4 => 'DEPRECATED',
        5 => 'DEBUG',
    ];

    /**
     * Write a message to the daily log file if the given level is within
     * the configured LOG_LEVEL threshold.
     *
     * @param mixed  $message   String, array, or object to log.
     * @param int    $levelNum  One of the LEVEL_* constants (default INFO).
     */
    public static function log($message, int $levelNum = self::LEVEL_INFO): void {
        $configuredLevel = defined('LOG_LEVEL') ? (int)LOG_LEVEL : self::LEVEL_ERROR;

        // Skip messages above the configured threshold
        if ($levelNum > $configuredLevel) {
            return;
        }

        $levelName = self::$levelNames[$levelNum] ?? 'UNKNOWN';
        $date      = date('Y-m-d');
        $logFile   = CORE_PATH . "/logs/app-{$date}.log";
        $time      = date('H:i:s');

        if (is_array($message) || is_object($message)) {
            $message = json_encode($message);
        }

        $entry = "[$time] [$levelName] $message" . PHP_EOL;

        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents($logFile, $entry, FILE_APPEND);
    }

    // ── Convenience methods ─────────────────────────────────────────────────

    /** Level 0 — fatal failures that make the application inoperable. */
    public static function critical(string $message): void {
        self::log($message, self::LEVEL_CRITICAL);
    }

    /** Level 1 — runtime errors that do not require immediate shutdown. */
    public static function error(string $message): void {
        self::log($message, self::LEVEL_ERROR);
    }

    /** Level 2 — non-critical problems or unexpected conditions. */
    public static function warning(string $message): void {
        self::log($message, self::LEVEL_WARNING);
    }

    /** Level 3 — general operational events (requests, DB queries, etc.). */
    public static function info(string $message): void {
        self::log($message, self::LEVEL_INFO);
    }

    /** Level 4 — use of deprecated features or code paths. */
    public static function deprecated(string $message): void {
        self::log($message, self::LEVEL_DEPRECATED);
    }

    /** Level 5 — verbose debug output, stack traces, variable dumps. */
    public static function debug(string $message): void {
        self::log($message, self::LEVEL_DEBUG);
    }
}
