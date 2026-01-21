<?php
include 'header.php';
require_once __DIR__ . '/../includes/themes.php';

// Handle theme change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_theme'])) {
    $newTheme = $_POST['theme_name'];
    $themes = getAvailableThemes();
    
    if (isset($themes[$newTheme])) {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'active_theme'");
        $stmt->execute([$newTheme]);
        header('Location: theme-settings.php?success=1');
        exit;
    }
}

$activeTheme = getActiveTheme($pdo);
$themes = getAvailableThemes();
?>

<div class="dashboard-header">
    <div>
        <h1>Tema Ayarları</h1>
        <p>Sitenizin görünümünü değiştirmek için bir tema seçin.</p>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
<div style="background: #ecfdf5; color: #065f46; padding: 15px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #10b981;">
    <i class="fas fa-check-circle"></i> Tema başarıyla değiştirildi!
</div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px;">
    <?php foreach($themes as $themeKey => $theme): ?>
    <div class="card-admin" style="position: relative; padding: 25px; <?php echo $activeTheme === $themeKey ? 'border: 3px solid var(--admin-accent); box-shadow: 0 10px 30px rgba(175, 139, 88, 0.2);' : ''; ?>">
        <?php if($activeTheme === $themeKey): ?>
        <div style="position: absolute; top: 15px; right: 15px; background: var(--admin-accent); color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
            <i class="fas fa-check"></i> Aktif Tema
        </div>
        <?php endif; ?>
        
        <h3 style="margin-bottom: 8px; color: var(--admin-primary);">
            <i class="fas fa-palette" style="color: var(--admin-accent); margin-right: 8px;"></i>
            <?php echo $theme['name']; ?>
        </h3>
        <p style="font-size: 13px; color: var(--admin-text-muted); margin-bottom: 20px;">
            <?php echo $theme['description']; ?>
        </p>
        
        <!-- Color Palette Preview -->
        <div style="margin-bottom: 20px;">
            <div style="font-size: 12px; font-weight: 600; margin-bottom: 8px; color: var(--admin-text-muted);">Renk Paleti:</div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <?php foreach($theme['colors'] as $colorName => $colorValue): ?>
                <div style="position: relative; group;">
                    <div style="width: 45px; height: 45px; background: <?php echo $colorValue; ?>; border-radius: 8px; border: 1px solid #ddd; cursor: help;" title="<?php echo ucfirst($colorName); ?>: <?php echo $colorValue; ?>"></div>
                    <div style="font-size: 9px; text-align: center; margin-top: 3px; color: #999;"><?php echo strtoupper(substr($colorName, 0, 3)); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <form method="POST" style="margin-top: 20px;">
            <input type="hidden" name="theme_name" value="<?php echo $themeKey; ?>">
            <?php if($activeTheme === $themeKey): ?>
            <button type="button" class="btn-primary-admin" style="width: 100%; background: #64748B; cursor: default;" disabled>
                <i class="fas fa-check-circle"></i> Şu An Kullanılıyor
            </button>
            <?php else: ?>
            <button type="submit" name="change_theme" class="btn-primary-admin" style="width: 100%; background: var(--admin-accent);">
                <i class="fas fa-paint-brush"></i> Bu Temayı Uygula
            </button>
            <?php endif; ?>
        </form>
    </div>
    <?php endforeach; ?>
</div>

<div class="card-admin" style="margin-top: 40px; background: #F8FAFC; border-left: 4px solid var(--admin-accent);">
    <h3 style="margin-bottom: 15px; color: var(--admin-primary);">
        <i class="fas fa-info-circle" style="color: var(--admin-accent);"></i> Tema Hakkında
    </h3>
    <p style="color: var(--admin-text-muted); line-height: 1.6;">
        Tema değiştirdiğinizde, sitenizin tüm renkleri otomatik olarak güncellenir. 
        Her tema profesyonel olarak tasarlanmış ve farklı bir atmosfer sunar. 
        Değişiklikler anında yansır ve tüm sayfalarda geçerli olur.
    </p>
</div>

<?php include 'footer.php'; ?>
