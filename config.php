<?php
/**
 * Database Configuration
 */

// Railway varsa
$databaseUrl = getenv('DATABASE_URL');

if ($databaseUrl) {
    // RAILWAY
    $pdo = new PDO($databaseUrl);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} else {
    // LOCAL
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'translator');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');

    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

/**
 * App Configuration
 */
define('BASE_URL', getenv('RAILWAY_STATIC_URL') ?: 'http://localhost/Translator/');
define('DEFAULT_LANG', 'tr');

/**
 * Site Titles
 */
$site_titles = [
    'tr' => 'Sefa Kaya – Yeminli Tercüman',
    'de' => 'Sefa Kaya – Vereidigter Übersetzer',
    'en' => 'Sefa Kaya – Certified Interpreter'
];
