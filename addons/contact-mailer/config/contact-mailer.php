<?php
// addons/contact-mailer/config/contact-mailer.php — read via config('contact-mailer').
// Secrets come from the environment (never hardcode); fall back to safe defaults.
return [
    'mailersend_key' => getenv('MAILERSEND_API_KEY') ?: '',
    'from_email'     => getenv('MAILERSEND_FROM_EMAIL') ?: 'noreply@example.com',
    'from_name'      => 'TiCore Contact Form',
    'to_email'       => getenv('CONTACT_EMAIL') ?: 'you@example.com',
    'to_name'        => 'TiCore',
    'subject_prefix' => 'TiCore contact',
];
