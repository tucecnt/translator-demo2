<?php
include 'config.php';
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
    $cols = $pdo->query("DESCRIBE settings")->fetchAll(PDO::FETCH_ASSOC);
    echo "Settings Columns:\n";
    foreach($cols as $col) {
        print_r($col);
    }
    
    // Also check current content
    $data = $pdo->query("SELECT * FROM settings LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    echo "\nSample Data:\n";
    print_r($data);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
