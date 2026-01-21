<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../includes/db.php';

// Initialize Admin Session
$current_page = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['admin_logged_in']) && $current_page != 'login.php') {
    header('Location: login.php');
    exit;
}

// Common Admin Functions
function getAvailableLanguages($pdo) {
    $stmt = $pdo->query("SELECT * FROM languages");
    return $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sefa Kaya | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">
<?php if (isset($_SESSION['admin_logged_in'])): ?>
    <div class="mobile-toggle">
        <i class="fas fa-bars"></i>
    </div>
    <div class="admin-sidebar">
        <div class="admin-logo">Admin Panel</div>
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Masaüstü</a>
            <a href="translations.php"><i class="fas fa-language"></i> Çeviri Yönetimi</a>
            <a href="services.php"><i class="fas fa-concierge-bell"></i> Hizmetler</a>
            <a href="document-categories.php"><i class="fas fa-file-alt"></i> Belge Kategorileri</a>
            <a href="about-us.php"><i class="fas fa-user-tie"></i> Hakkımda & Uzmanlıklar</a>
            <a href="references.php"><i class="fas fa-briefcase"></i> Referanslar</a>
            <a href="contact-info.php"><i class="fas fa-address-book"></i> İletişim Bilgileri</a>
            <a href="faqs.php"><i class="fas fa-question-circle"></i> SSS Yönetimi</a>
            <a href="messages.php"><i class="fas fa-envelope-open-text"></i> Gelen Mesajlar</a>
            <a href="theme-settings.php"><i class="fas fa-palette"></i> Tema Ayarları</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
        </nav>
    </div>
    <div class="admin-content">
<?php endif; ?>
