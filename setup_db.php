<?php
require_once 'config.php';

try {
    // Connect without DB name first to create it if it doesn't exist
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE " . DB_NAME);
    
    $sql = file_get_contents('database.sql');
    $pdo->exec($sql);
    
    echo "Database and tables initialized successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
