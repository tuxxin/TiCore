<?php
// addons/schema-generator/config/schema-generator.php — read via config('schema-generator').
// Minimal: just the list of supported schema.org @types the form offers.
// This tool is STATELESS — no DB, no file writes, no logging of submitted values.
return [
    'types' => ['Organization', 'LocalBusiness', 'Product', 'Article', 'WebSite'],
];
