<?php
/**
 * Database settings — reads Railway MySQL variables when deployed.
 * On Railway, link the MySQL service so MYSQLHOST, MYSQLUSER, etc. are injected.
 */

function dallah_env(string $keys, $default = null)
{
    foreach (explode('|', $keys) as $key) {
        $key = trim($key);
        $val = getenv($key);
        if ($val !== false && $val !== '') {
            return $val;
        }
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return $_ENV[$key];
        }
    }
    return $default;
}

$dbHost = dallah_env('MYSQLHOST|MYSQL_HOST', 'mysql.railway.internal');
$dbUser = dallah_env('MYSQLUSER|MYSQL_USER', 'root');
$dbPass = dallah_env('MYSQLPASSWORD|MYSQL_PASSWORD|MYSQL_ROOT_PASSWORD', '');
$dbName = dallah_env('MYSQLDATABASE|MYSQL_DATABASE', 'railway');
$dbPort = dallah_env('MYSQLPORT|MYSQL_PORT', '3306');

// Prefer internal URL on Railway (mysql.railway.internal), not the public proxy.
$mysqlUrl = dallah_env('MYSQL_URL') ?: dallah_env('MYSQL_PUBLIC_URL');
if ($mysqlUrl) {
    $parsed = parse_url($mysqlUrl);
    if (!empty($parsed['host'])) {
        $dbHost = $parsed['host'];
    }
    if (!empty($parsed['port'])) {
        $dbPort = (string) $parsed['port'];
    }
    if (!empty($parsed['user'])) {
        $dbUser = $parsed['user'];
    }
    if (array_key_exists('pass', $parsed) && $parsed['pass'] !== null && $parsed['pass'] !== '') {
        $dbPass = $parsed['pass'];
    }
    if (!empty($parsed['path'])) {
        $dbName = ltrim($parsed['path'], '/');
    }
}

define('DB_HOST', $dbHost);
define('DB_USER', $dbUser);
define('DB_PASSWORD', $dbPass);
define('DB_NAME', $dbName);
define('DB_PORT', $dbPort);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('CAN_REGISTER', 'none');
define('DEFAULT_ROLE', 'member');

// For development only!!
define('SECURE', false);
define('DEBUG', true);
