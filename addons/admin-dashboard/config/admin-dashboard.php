<?php
// addons/admin-dashboard/config/admin-dashboard.php — read via config('admin-dashboard').
return [
    'brand'    => 'TiCore Admin',
    // Nav links shown in the dashboard shell (label => path). Reused by views.
    'nav'      => [
        'Dashboard' => '/admin',
        'Account'   => '/account',
        'Blog'      => '/admin/blog',
        'API Keys'  => '/admin/api-keys',
    ],
    'logout_url' => '/logout',
];
