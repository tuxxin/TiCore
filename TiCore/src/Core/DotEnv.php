<?php
namespace TiCore\Core;

class DotEnv {
    public static function load($path) {
        if (!file_exists($path)) {
            throw new \Exception("The .env file is missing.");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue; // Skip comments
            if (!str_contains($line, '=')) continue;      // Skip lines without a value

            [$name, $value] = explode('=', $line, 2);
            $name  = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
