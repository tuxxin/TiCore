<?php
namespace TiCore\Core;

class DotEnv {
    public static function load($path) {
        if (!file_exists($path)) {
            // .env is optional — fall back to real environment variables + config defaults.
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue; // Skip comment lines
            if (!str_contains($line, '=')) continue;      // Skip lines without a value

            [$name, $value] = explode('=', $line, 2);
            $name  = trim($name);
            $value = trim($value);

            // Strip surrounding quotes ("value" or 'value')
            if (strlen($value) >= 2
                && (($value[0] === '"'  && $value[-1] === '"')
                 || ($value[0] === "'"  && $value[-1] === "'"))
            ) {
                $value = substr($value, 1, -1);
            } else {
                // Strip inline comments (e.g. VALUE=foo # comment) on unquoted values
                if (($pos = strpos($value, ' #')) !== false) {
                    $value = rtrim(substr($value, 0, $pos));
                }
            }

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name]    = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
