<?php
/**
 * Environment validation script.
 * Run: php deploy/env-validate.php
 * Or from project root: php core/deploy/env-validate.php
 */

$required = [
    'APP_KEY'       => 'Generate with: php artisan key:generate',
    'APP_URL'       => 'Set to production URL',
    'DB_DATABASE'   => 'Database name',
    'DB_USERNAME'   => 'Database user',
    'DB_PASSWORD'   => 'Can be empty for local',
];

$recommended = [
    'ADMIN_PREFIX'       => 'Use non-predictable value in production',
    'ENABLE_ADMIN_CLEAR' => 'Set to false in production',
    'APP_DEBUG'          => 'Must be false in production',
];

$dir = dirname(__DIR__);
if (!file_exists($dir . '/.env')) {
    echo "ERROR: .env file not found in core/\n";
    exit(1);
}

$env = parse_ini_file($dir . '/.env', false, INI_SCANNER_RAW);
$errors = [];
$warnings = [];

foreach ($required as $key => $msg) {
    $v = $env[$key] ?? '';
    if (trim((string) $v) === '') {
        $errors[] = "$key is required. $msg";
    }
}

foreach ($recommended as $key => $msg) {
    $v = $env[$key] ?? '';
    if ($key === 'APP_DEBUG' && ($v === 'true' || $v === '1')) {
        $warnings[] = "APP_DEBUG should be false in production. $msg";
    }
    if ($key === 'ENABLE_ADMIN_CLEAR' && ($v === 'true' || $v === '1')) {
        $warnings[] = "ENABLE_ADMIN_CLEAR: $msg";
    }
}

if (!empty($errors)) {
    echo "FAILED – fix these:\n";
    foreach ($errors as $e) echo "  - $e\n";
    exit(1);
}

if (!empty($warnings)) {
    echo "WARNINGS:\n";
    foreach ($warnings as $w) echo "  - $w\n";
}

echo "Environment validation passed.\n";
exit(0);
