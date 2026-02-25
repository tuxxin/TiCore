<?php
// TiCore/src/Controllers/FeaturesController.php
namespace TiCore\Controllers;
use TiCore\Core\Database;

class FeaturesController {
    public function index(): void {
        // ── Database status ────────────────────────────────────────────────
        $db        = new Database();
        $dbStatus  = $db->pdo ? 'Connected' : 'Disabled';
        $dbVersion = 'N/A';

        if ($db->pdo) {
            try {
                $dbVersion = $db->pdo->query('SELECT VERSION()')->fetchColumn();
            } catch (\Exception $e) {
                $dbVersion = 'Unknown';
            }
        }

        // ── PHP extensions to probe ────────────────────────────────────────
        $extensionList = [
            'pdo'        => 'PDO',
            'pdo_mysql'  => 'PDO MySQL',
            'mysqli'     => 'MySQLi',
            'gd'         => 'GD (Images)',
            'curl'       => 'cURL',
            'mbstring'   => 'Multibyte String',
            'json'       => 'JSON',
            'zip'        => 'ZIP',
            'openssl'    => 'OpenSSL',
            'intl'       => 'Intl',
            'xml'        => 'XML',
            'dom'        => 'DOM',
            'fileinfo'   => 'FileInfo',
            'bcmath'     => 'BCMath',
            'exif'       => 'EXIF',
            'soap'       => 'SOAP',
            'opcache'    => 'OPcache',
            'xdebug'     => 'Xdebug',
            'redis'      => 'Redis',
            'imagick'    => 'Imagick',
        ];

        $extensions = [];
        foreach ($extensionList as $ext => $label) {
            $extensions[] = [
                'name'   => $label,
                'loaded' => extension_loaded($ext),
            ];
        }

        // ── Server software ────────────────────────────────────────────────
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';

        // ── Log level label ────────────────────────────────────────────────
        $logLevelLabels = [
            0 => '0 — CRITICAL',
            1 => '1 — ERROR',
            2 => '2 — WARNING',
            3 => '3 — INFO',
            4 => '4 — DEPRECATED',
            5 => '5 — DEBUG',
        ];
        $currentLogLevel = defined('LOG_LEVEL') ? (int)LOG_LEVEL : 1;
        $logLevelLabel   = $logLevelLabels[$currentLogLevel] ?? (string)$currentLogLevel;

        view('features', [
            'title'            => 'Features Supported',
            'meta_description' => 'Live server capabilities for TiCore on ' . $serverSoftware . ' — PHP version, loaded extensions, database status, and active log level.',
            'og_type'          => 'website',
            'server_software'  => $serverSoftware,
            'php_version'      => phpversion(),
            'db_status'        => $dbStatus,
            'db_version'       => $dbVersion,
            'log_level_label'  => $logLevelLabel,
            'extensions'       => $extensions,
        ]);
    }
}
