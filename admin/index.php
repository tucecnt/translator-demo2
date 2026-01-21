<?php 
include 'header.php'; 

// Fetch Stats
$langCount = $pdo->query("SELECT COUNT(*) FROM languages")->fetchColumn();
$serviceCount = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
$refCount = $pdo->query("SELECT COUNT(*) FROM `references`")->fetchColumn();
$specCount = $pdo->query("SELECT COUNT(*) FROM specialties WHERE is_active = 1")->fetchColumn();
$transCount = $pdo->query("SELECT COUNT(*) FROM translations WHERE lang_id = 1")->fetchColumn(); 
?>

<div class="dashboard-header">
    <div>
        <h1>Masaüstü</h1>
        <p>Sefa Kaya Profesyonel Tercüme Hizmetleri Yönetim Paneli</p>
    </div>
    <div class="current-date">
        <i class="far fa-calendar-alt"></i> <?php echo date('d.m.Y'); ?>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-language"></i></div>
        <div class="stat-info">
            <h3><?php echo $langCount; ?></h3>
            <p>Aktif Dil</p>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="fas fa-concierge-bell"></i></div>
        <div class="stat-info">
            <h3><?php echo $serviceCount; ?></h3>
            <p>Toplam Hizmet</p>
        </div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon"><i class="fas fa-star"></i></div>
        <div class="stat-info">
            <h3><?php echo $specCount; ?></h3>
            <p>Aktif Uzmanlık</p>
        </div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
        <div class="stat-info">
            <h3><?php echo $refCount; ?></h3>
            <p>Referans</p>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card-admin">
        <h3><i class="fas fa-bolt" style="color: #ffc107; margin-right: 10px;"></i> Hızlı İşlemler</h3>
        <div class="quick-actions">
            <a href="translations.php" class="action-item">
                <i class="fas fa-edit"></i>
                <span>Metinleri Düzenle</span>
            </a>
            <a href="services.php" class="action-item">
                <i class="fas fa-plus"></i>
                <span>Yeni Hizmet Ekle</span>
            </a>
            <a href="specialties.php" class="action-item">
                <i class="fas fa-star"></i>
                <span>Uzmanlık Yönetimi</span>
            </a>
            <a href="references.php" class="action-item">
                <i class="fas fa-images"></i>
                <span>Referans Yönetimi</span>
            </a>
            <a href="../index.php" target="_blank" class="action-item">
                <i class="fas fa-external-link-alt"></i>
                <span>Siteyi Görüntüle</span>
            </a>
        </div>
    </div>

    <div class="card-admin">
        <h3><i class="fas fa-info-circle" style="color: #17a2b8; margin-right: 10px;"></i> Sistem Bilgisi</h3>
        <table class="info-table">
            <tr>
                <td><strong>PHP Versiyonu:</strong></td>
                <td><?php echo phpversion(); ?></td>
            </tr>
            <tr>
                <td><strong>Veritabanı:</strong></td>
                <td>MySQL (PDO)</td>
            </tr>
            <tr>
                <td><strong>Sunucu:</strong></td>
                <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
            </tr>
            <tr>
                <td><strong>Dil Dosyaları:</strong></td>
                <td>Veritabanı Tabanlı</td>
            </tr>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
