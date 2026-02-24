<?php
namespace TiCore\Core;

class Logger {
    public static function log($message, $level = 'INFO') {
        $date = date('Y-m-d');
        // Logs are stored in TiCore/logs/app-YYYY-MM-DD.log
        $logFile = CORE_PATH . "/logs/app-{$date}.log";
        
        $time = date('H:i:s');
        
        // Context: Is this an array or string?
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message);
        }

        $formattedMessage = "[$time] [$level] $message" . PHP_EOL;

        // Append to file
        file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    }

    public static function error($message) {
        self::log($message, 'ERROR');
    }

    public static function info($message) {
        self::log($message, 'INFO');
    }
}
