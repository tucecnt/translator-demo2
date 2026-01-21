<?php
session_start();
require_once 'includes/db.php';
require_once 'classes/Language.php';
require_once 'includes/themes.php';

$langObj = new Language($pdo);
$currentLang = $langObj->getCurrentLang();

// Load active theme
$activeTheme = getActiveTheme($pdo);
$themeCSS = generateThemeCSS($activeTheme);

// Fetch visibility for references
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'show_references'");
$stmt->execute();
$show_references = $stmt->fetchColumn();

function __($key, $default = null) {
    global $langObj;
    return $langObj->get($key, $default);
}

// Meta titles for pages
$site_title = $site_titles[$currentLang] ?? 'Sefa Kaya';
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $site_title; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Dynamic Theme CSS -->
    <style><?php echo $themeCSS; ?></style>
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>">
                    <span class="name">Sefa Kaya</span>
                    <span class="title"><?php echo __('nav_title', 'Yeminli Tercüman'); ?></span>
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php"><?php echo __('nav_home', 'Ana Sayfa'); ?></a></li>
                <li><a href="hakkimda.php"><?php echo __('nav_about', 'Hakkımda'); ?></a></li>
                <li><a href="hizmetler.php"><?php echo __('nav_services', 'Hizmetler'); ?></a></li>
                <li><a href="surec.php"><?php echo __('nav_process', 'Çeviri Süreci'); ?></a></li>
                <?php if ($show_references == '1'): ?>
                <li><a href="referanslar.php"><?php echo __('nav_references', 'Referanslar'); ?></a></li>
                <?php endif; ?>
                <li><a href="sss.php"><?php echo __('nav_faq', 'SSS'); ?></a></li>
                <li><a href="iletisim.php"><?php echo __('nav_contact', 'İletişim'); ?></a></li>
            </ul>
            <div class="lang-switcher">
                <a href="?lang=tr" class="<?php echo $currentLang == 'tr' ? 'active' : ''; ?>">TR</a>
                <a href="?lang=de" class="<?php echo $currentLang == 'de' ? 'active' : ''; ?>">DE</a>
                <a href="?lang=en" class="<?php echo $currentLang == 'en' ? 'active' : ''; ?>">EN</a>
            </div>
            <div class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </div>
        </nav>
    </header>
