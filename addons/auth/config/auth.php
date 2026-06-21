<?php
// addons/auth/config/auth.php — safe defaults (read via config('auth')).
return [
    'password_min' => 8,
    'roles'        => ['user', 'admin'],
    'login_url'    => '/login',
    'home_url'     => '/account',
];
