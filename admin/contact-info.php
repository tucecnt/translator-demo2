<?php
include 'header.php';

// İletişim bilgileri anahtarları
$contactFields = [
    'contact_phone' => ['label' => 'Telefon Numarası', 'type' => 'text', 'icon' => 'fas fa-phone', 'placeholder' => '+90 XXX XXX XX XX'],
    'contact_email' => ['label' => 'E-posta Adresi', 'type' => 'email', 'icon' => 'fas fa-envelope', 'placeholder' => 'info@example.com'],
    'contact_address' => ['label' => 'Adres', 'type' => 'textarea', 'icon' => 'fas fa-map-marker-alt', 'placeholder' => 'Tam adres...'],
    'contact_whatsapp' => ['label' => 'WhatsApp Numarası', 'type' => 'text', 'icon' => 'fab fa-whatsapp', 'placeholder' => '+90 XXX XXX XX XX'],
    'contact_working_hours' => ['label' => 'Çalışma Saatleri', 'type' => 'text', 'icon' => 'fas fa-clock', 'placeholder' => 'Pzt-Cum: 09:00 - 18:00'],
    'contact_linkedin' => ['label' => 'LinkedIn URL', 'type' => 'url', 'icon' => 'fab fa-linkedin', 'placeholder' => 'https://linkedin.com/in/...'],
    'contact_twitter' => ['label' => 'Twitter URL', 'type' => 'url', 'icon' => 'fab fa-twitter', 'placeholder' => 'https://twitter.com/...'],
    'contact_facebook' => ['label' => 'Facebook URL', 'type' => 'url', 'icon' => 'fab fa-facebook', 'placeholder' => 'https://facebook.com/...'],
];

// Handle Save
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_contact'])) {
    foreach ($_POST['contact'] as $key => $value) {
        // Check if setting exists
        $stmt = $pdo->prepare("SELECT id FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $stmt->execute([$key, $value]);
        }
    }
    $success = "İletişim bilgileri başarıyla güncellendi!";
}

// Fetch current values
$currentValues = [];
foreach ($contactFields as $key => $field) {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $currentValues[$key] = $stmt->fetchColumn() ?: '';
}
?>

<style>
    .contact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .contact-field-card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        border: 2px solid #e5e7eb;
        transition: all 0.3s;
    }
    
    .contact-field-card:hover {
        border-color: var(--admin-accent);
        box-shadow: 0 4px 12px rgba(0,43,91,0.1);
    }
    
    .field-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }
    
    .field-icon {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, var(--admin-accent) 0%, #003d7a 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }
    
    .field-label {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
    }
    
    .field-input, .field-textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
        font-family: inherit;
    }
    
    .field-textarea {
        min-height: 100px;
        resize: vertical;
    }
    
    .field-input:focus, .field-textarea:focus {
        border-color: var(--admin-accent);
        outline: none;
        box-shadow: 0 0 0 3px rgba(0,43,91,0.1);
    }
    
    .section-divider {
        margin: 40px 0;
        border: none;
        border-top: 2px solid #e5e7eb;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<div class="dashboard-header">
    <div>
        <h1><i class="fas fa-address-book"></i> İletişim Bilgileri Yönetimi</h1>
        <p>Web sitenizdeki iletişim bilgilerini buradan güncelleyebilirsiniz.</p>
    </div>
</div>

<?php if(isset($success)): ?>
<div style="background: #ecfdf5; color: #065f46; padding: 16px 20px; border-radius: 10px; margin-bottom: 25px; border-left: 4px solid #10b981; display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-check-circle" style="font-size: 20px;"></i>
    <span style="font-weight: 600;"><?php echo $success; ?></span>
</div>
<?php endif; ?>

<form method="POST">
    <div class="card-section" style="background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid #e5e7eb;">
        
        <!-- Temel İletişim Bilgileri -->
        <div class="section-title">
            <i class="fas fa-info-circle" style="color: var(--admin-accent);"></i>
            Temel İletişim Bilgileri
        </div>
        
        <div class="contact-grid">
            <?php 
            $basicFields = ['contact_phone', 'contact_email', 'contact_address', 'contact_whatsapp', 'contact_working_hours'];
            foreach ($basicFields as $key): 
                $field = $contactFields[$key];
            ?>
            <div class="contact-field-card">
                <div class="field-header">
                    <div class="field-icon">
                        <i class="<?php echo $field['icon']; ?>"></i>
                    </div>
                    <div class="field-label"><?php echo $field['label']; ?></div>
                </div>
                
                <?php if ($field['type'] === 'textarea'): ?>
                    <textarea 
                        name="contact[<?php echo $key; ?>]" 
                        class="field-textarea" 
                        placeholder="<?php echo $field['placeholder']; ?>"><?php echo htmlspecialchars($currentValues[$key]); ?></textarea>
                <?php else: ?>
                    <input 
                        type="<?php echo $field['type']; ?>" 
                        name="contact[<?php echo $key; ?>]" 
                        class="field-input" 
                        value="<?php echo htmlspecialchars($currentValues[$key]); ?>" 
                        placeholder="<?php echo $field['placeholder']; ?>">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        
        <hr class="section-divider">
        
        <!-- Sosyal Medya -->
        <div class="section-title">
            <i class="fas fa-share-alt" style="color: var(--admin-accent);"></i>
            Sosyal Medya Linkleri <span style="font-size: 12px; font-weight: 400; color: #6b7280;">(Opsiyonel)</span>
        </div>
        
        <div class="contact-grid">
            <?php 
            $socialFields = ['contact_linkedin', 'contact_twitter', 'contact_facebook'];
            foreach ($socialFields as $key): 
                $field = $contactFields[$key];
            ?>
            <div class="contact-field-card">
                <div class="field-header">
                    <div class="field-icon">
                        <i class="<?php echo $field['icon']; ?>"></i>
                    </div>
                    <div class="field-label"><?php echo $field['label']; ?></div>
                </div>
                
                <input 
                    type="<?php echo $field['type']; ?>" 
                    name="contact[<?php echo $key; ?>]" 
                    class="field-input" 
                    value="<?php echo htmlspecialchars($currentValues[$key]); ?>" 
                    placeholder="<?php echo $field['placeholder']; ?>">
            </div>
            <?php endforeach; ?>
        </div>
        
        <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #e5e7eb; text-align: right;">
            <button type="submit" name="save_contact" class="btn-primary-admin" style="background: var(--admin-accent); padding: 14px 40px; font-weight: 700; font-size: 15px;">
                <i class="fas fa-save"></i> Değişiklikleri Kaydet
            </button>
        </div>
    </div>
</form>

<?php include 'footer.php'; ?>
