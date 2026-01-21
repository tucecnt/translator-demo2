<?php
include 'header.php';

$langs = getAvailableLanguages($pdo);

// Hakkımda sayfası için çeviri anahtarları
$aboutKeys = [
    'about_title' => 'Sayfa Başlığı',
    'about_tagline' => 'Unvan / Slogan',
    'about_p1' => 'Paragraf 1',
    'about_p2' => 'Paragraf 2',
    'countries_list' => 'Ülkeler Listesi',
];

// Handle Translation Save
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_translations'])) {
    foreach ($_POST['trans'] as $lang_id => $keys) {
        foreach ($keys as $key => $value) {
            $stmt = $pdo->prepare("REPLACE INTO translations (lang_id, `key`, `value`) VALUES (?, ?, ?)");
            $stmt->execute([$lang_id, $key, $value]);
        }
    }
    $success = "Tüm metin değişiklikleri başarıyla kaydedildi!";
}

// Handle Specialty Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_specialty'])) {
        $sid = $_POST['sid'];
        $sort_order = $_POST['sort_order'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if (!empty($sid)) {
            // Update
            $stmt = $pdo->prepare("UPDATE specialties SET sort_order = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$sort_order, $is_active, $sid]);
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO specialties (sort_order, is_active) VALUES (?, ?)");
            $stmt->execute([$sort_order, $is_active]);
            $sid = $pdo->lastInsertId();
        }

        // Save translations
        foreach ($_POST['spec_trans'] as $lang_id => $title) {
            $stmt = $pdo->prepare("SELECT id FROM specialty_translations WHERE specialty_id = ? AND lang_id = ?");
            $stmt->execute([$sid, $lang_id]);
            $exists = $stmt->fetch();

            if ($exists) {
                $stmt = $pdo->prepare("UPDATE specialty_translations SET title = ? WHERE id = ?");
                $stmt->execute([$title, $exists['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO specialty_translations (specialty_id, lang_id, title) VALUES (?, ?, ?)");
                $stmt->execute([$sid, $lang_id, $title]);
            }
        }
        $success = "Uzmanlık başarıyla kaydedildi!";
    }

    if (isset($_POST['delete_specialty_id'])) {
        $pdo->prepare("DELETE FROM specialties WHERE id = ?")->execute([$_POST['delete_specialty_id']]);
        $success = "Uzmanlık başarıyla silindi!";
    }
}

// Fetch Specialties
$stmt = $pdo->query("SELECT * FROM specialties ORDER BY sort_order ASC, id DESC");
$specialties = $stmt->fetchAll();

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $target_dir = "../assets/img/";
    $file_ext = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
    $new_filename = "profile_" . uniqid() . "." . $file_ext;
    $target_file = $target_dir . $new_filename;
    $uploadOk = 1;
    
    // Check if image file is a actual image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $error = "Dosya bir resim değil.";
            $uploadOk = 0;
        }
    }
    
    // Allow certain file formats
    if($file_ext != "jpg" && $file_ext != "png"&& $file_ext != "jpeg"&& $file_ext != "webp") {
        $error = "Sadece JPG, JPEG, PNG & WEBP dosyalarına izin verilir.";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            // Save to DB
            $db_path = "assets/img/" . $new_filename;
            
            // Check if setting exists
            $stmt = $pdo->prepare("SELECT id FROM settings WHERE setting_key = 'about_image'");
            $stmt->execute();
            if ($stmt->fetch()) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'about_image'");
                $stmt->execute([$db_path]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('about_image', ?)");
                $stmt->execute([$db_path]);
            }
            
            $success = "Resim başarıyla güncellendi.";
        } else {
            $error = "Dosya yüklenirken bir hata oluştu.";
        }
    }
}

// Fetch current image
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'about_image'");
$stmt->execute();
$current_img = $stmt->fetchColumn();
if (!$current_img) {
    $current_img = "assets/img/profile.jpg"; // Default
}
?>

<style>
    .about-container {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 30px;
        margin-bottom: 30px;
    }
    
    @media (max-width: 1200px) {
        .about-container {
            grid-template-columns: 1fr;
        }
    }
    
    .card-section {
        background: #fff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
    }
    
    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f3f4f6;
    }
    
    .card-header i {
        font-size: 24px;
        color: var(--admin-accent);
    }
    
    .card-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
    }
    
    .image-preview {
        text-align: center;
        margin-bottom: 25px;
    }
    
    .image-preview img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .form-group-modern {
        margin-bottom: 20px;
    }
    
    .form-group-modern label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }
    
    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }
    
    .file-input-wrapper input[type=file] {
        font-size: 14px;
        padding: 12px;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .file-input-wrapper input[type=file]:hover {
        border-color: var(--admin-accent);
        background: #f9fafb;
    }
    
    .hint-text {
        display: block;
        margin-top: 8px;
        font-size: 12px;
        color: #6b7280;
    }
    
    .translation-grid {
        display: grid;
        gap: 25px;
    }
    
    .translation-item {
        background: #f9fafb;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s;
    }
    
    .translation-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-color: #d1d5db;
    }
    
    .translation-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .translation-label {
        font-weight: 700;
        color: #1f2937;
        font-size: 15px;
    }
    
    .translation-key {
        font-size: 11px;
        color: #9ca3af;
        font-family: 'Courier New', monospace;
        background: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid #e5e7eb;
    }
    
    .language-inputs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    @media (max-width: 900px) {
        .language-inputs {
            grid-template-columns: 1fr;
        }
    }
    
    .language-field {
        display: flex;
        flex-direction: column;
    }
    
    .language-field label {
        font-size: 12px;
        font-weight: 700;
        color: var(--admin-accent);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .language-field textarea {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 12px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.3s;
        min-height: 100px;
        resize: vertical;
        background: #fff;
    }
    
    .language-field textarea:focus {
        border-color: var(--admin-accent);
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 43, 91, 0.1);
    }
    
    .btn-save {
        background: var(--admin-accent);
        color: white;
        border: none;
        padding: 14px 32px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 8px rgba(0, 43, 91, 0.2);
    }
    
    .btn-save:hover {
        background: #001f3f;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 43, 91, 0.3);
    }
    
    .btn-save i {
        font-size: 16px;
    }
    
    .action-bar {
        display: flex;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #e5e7eb;
    }

    /* Tab Styles */
    .admin-tabs {
        display: flex;
        gap: 5px;
        margin-bottom: 25px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 1px;
    }

    .tab-btn {
        padding: 12px 25px;
        border: none;
        background: none;
        font-weight: 600;
        font-size: 15px;
        color: #64748b;
        cursor: pointer;
        position: relative;
        transition: all 0.3s;
        border-radius: 8px 8px 0 0;
    }

    .tab-btn:hover {
        color: var(--admin-accent);
        background: #f1f5f9;
    }

    .tab-btn.active {
        color: var(--admin-accent);
        background: #fff;
    }

    .tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--admin-accent);
        border-radius: 3px 3px 0 0;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="dashboard-header about-header" style="background: linear-gradient(135deg, var(--admin-primary) 0%, #001f3f 100%); color: #fff; padding: 30px; border-radius: 15px; margin-bottom: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
    <div style="display: flex; align-items: center; gap: 20px;">
        <div class="about-icon-header" style="background: rgba(255,255,255,0.1); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px); flex-shrink: 0;">
            <i class="fas fa-user-shield" style="font-size: 30px; color: #fff;"></i>
        </div>
        <div>
            <h1 style="color: #fff; margin: 0; font-size: 24px;">Hakkımda & Uzmanlık</h1>
            <p style="color: rgba(255,255,255,0.7); margin-top: 5px; font-size: 14px;">Profil bilgilerinizi ve uzmanlıklarınızı yönetin.</p>
        </div>
    </div>
</div>

<?php if(isset($success)): ?>
<div style="background: #ecfdf5; color: #065f46; padding: 16px 20px; border-radius: 10px; margin-bottom: 25px; border-left: 4px solid #10b981; display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-check-circle" style="font-size: 20px;"></i>
    <span style="font-weight: 600;"><?php echo $success; ?></span>
</div>
<?php endif; ?>

<?php if(isset($error)): ?>
<div style="background: #fef2f2; color: #991b1b; padding: 16px 20px; border-radius: 10px; margin-bottom: 25px; border-left: 4px solid #ef4444; display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-exclamation-circle" style="font-size: 20px;"></i>
    <span style="font-weight: 600;"><?php echo $error; ?></span>
</div>
<?php endif; ?>

<div class="admin-tabs">
    <button class="tab-btn active" onclick="openTab(event, 'general-settings')">
        <i class="fas fa-cog"></i> Genel Ayarlar & Metinler
    </button>
    <button class="tab-btn" onclick="openTab(event, 'specialties-management')" style="border-right: 2px solid rgba(var(--admin-accent-rgb), 0.1);">
        <i class="fas fa-star" style="color: #f59e0b;"></i> Uzmanlık Alanları 
        <span style="background: #f59e0b; color: #fff; font-size: 10px; padding: 2px 7px; border-radius: 10px; margin-left: 8px; vertical-align: middle;">YENİ BÖLÜM</span>
    </button>
</div>

<div id="general-settings" class="tab-content active">
    <div class="about-container">
    <!-- Görsel Yönetimi -->
    <div class="card-section">
        <div class="card-header">
            <i class="fas fa-image"></i>
            <h3>Profil Görseli</h3>
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="image-preview">
                <img src="../<?php echo $current_img; ?>" alt="Profil Görseli">
            </div>
            
            <div class="form-group-modern">
                <label><i class="fas fa-upload"></i> Yeni Görsel Yükle</label>
                <div class="file-input-wrapper">
                    <input type="file" name="profile_image" accept="image/*" required>
                </div>
                <small class="hint-text">
                    <i class="fas fa-info-circle"></i> Önerilen boyut: 600x800px | Format: JPG, PNG, WEBP
                </small>
            </div>
            
            <div class="action-bar">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Görseli Güncelle
                </button>
            </div>
        </form>
    </div>
    
    <!-- Metin Yönetimi -->
    <div class="card-section" style="grid-column: span 1;">
        <div class="card-header">
            <i class="fas fa-language"></i>
            <h3>Sayfa Metinleri</h3>
        </div>
        
        <form method="POST">
            <div class="translation-grid">
                <?php foreach ($aboutKeys as $key => $label): ?>
                    <div class="translation-item">
                        <div class="translation-item-header">
                            <span class="translation-label"><?php echo $label; ?></span>
                            <span class="translation-key"><?php echo $key; ?></span>
                        </div>
                        
                        <div class="language-inputs">
                            <?php foreach ($langs as $lang): 
                                $stmt = $pdo->prepare("SELECT `value` FROM translations WHERE lang_id = ? AND `key` = ?");
                                $stmt->execute([$lang['id'], $key]);
                                $val = $stmt->fetchColumn();
                            ?>
                                <div class="language-field">
                                    <label><?php echo $lang['name']; ?></label>
                                    <textarea name="trans[<?php echo $lang['id']; ?>][<?php echo $key; ?>]" placeholder="<?php echo $label; ?> (<?php echo $lang['name']; ?>)"><?php echo htmlspecialchars($val); ?></textarea>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="action-bar">
                <button type="submit" name="save_translations" class="btn-save">
                    <i class="fas fa-save"></i> Tüm Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    </div>
</div> <!-- Close about-container -->
</div> <!-- Close general-settings -->

<div id="specialties-management" class="tab-content">
    <!-- Uzmanlıklar Bölümü -->
    <div class="card-section">
    <div class="card-header" style="justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-star"></i>
            <h3>Uzmanlık Alanları Yönetimi</h3>
        </div>
        <button class="btn-save" onclick="showAddForm()" style="padding: 10px 20px; font-size: 14px;">
            <i class="fas fa-plus"></i> Yeni Uzmanlık Ekle
        </button>
    </div>

    <!-- Add/Edit Specialty Form (Hidden by default) -->
    <div id="specForm" style="display:none; background: #f8fafc; border: 1px solid #e2e8f0; padding: 25px; border-radius: 12px; margin-bottom: 30px; border-left: 5px solid var(--admin-accent);">
        <h4 id="formTitle" style="margin-bottom: 20px; color: #1e293b;"><i class="fas fa-edit"></i> <span id="titleText">Uzmanlık Düzenle</span></h4>
        <form method="POST">
            <input type="hidden" name="sid" id="sid">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group-modern">
                    <label>Görüntüleme Sırası</label>
                    <input type="number" name="sort_order" id="ssort" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;" value="0">
                </div>
                <div class="form-group-modern" style="display: flex; align-items: flex-end; padding-bottom: 15px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; border: 1px solid #ddd; padding: 8px 15px; border-radius: 6px; background: #fff;">
                        <input type="checkbox" name="is_active" id="sactive" checked style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; font-size: 14px;">Aktif / Yayında</span>
                    </label>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
                <?php foreach($langs as $lang): ?>
                <div class="language-field">
                    <label style="font-size: 11px;"><?php echo $lang['name']; ?> Başlık</label>
                    <input type="text" name="spec_trans[<?php echo $lang['id']; ?>]" id="stitle_<?php echo $lang['id']; ?>" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;" placeholder="Örn: Hukuki Tercüme" required>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="hideAddForm()" class="btn-save" style="background:#dc2626; box-shadow: none;">İptal</button>
                <button type="submit" name="save_specialty" class="btn-save">
                    <i class="fas fa-save"></i> Uzmanlığı Kaydet
                </button>
            </div>
        </form>
    </div>

    <!-- Specialties Table -->
    <div class="table-responsive-container">
        <table class="table-admin" style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 15px; text-align: left; font-size: 13px; text-transform: uppercase; color: #64748b;">Sıra</th>
                    <th style="padding: 15px; text-align: left; font-size: 13px; text-transform: uppercase; color: #64748b;">Durum</th>
                    <th style="padding: 15px; text-align: left; font-size: 13px; text-transform: uppercase; color: #64748b;">Uzmanlık Alanı (TR)</th>
                    <th style="padding: 15px; text-align: right; font-size: 13px; text-transform: uppercase; color: #64748b;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($specialties as $s): 
                    $stmt = $pdo->prepare("SELECT title FROM specialty_translations WHERE specialty_id = ? AND lang_id = 1");
                    $stmt->execute([$s['id']]);
                    $title_tr = $stmt->fetchColumn() ?: 'Başlıksız';
                    
                    $stmt = $pdo->prepare("SELECT lang_id, title FROM specialty_translations WHERE specialty_id = ?");
                    $stmt->execute([$s['id']]);
                    $all_trans = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                ?>
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 15px;">
                        <span style="background: #e2e8f0; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700;"><?php echo $s['sort_order']; ?></span>
                    </td>
                    <td style="padding: 15px;">
                        <?php if($s['is_active']): ?>
                            <span style="color: #10b981; font-size: 13px; font-weight: 600;"><i class="fas fa-check-circle"></i> Aktif</span>
                        <?php else: ?>
                            <span style="color: #ef4444; font-size: 13px; font-weight: 600;"><i class="fas fa-times-circle"></i> Pasif</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px;">
                        <span style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($title_tr); ?></span>
                    </td>
                    <td style="padding: 15px; text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 8px;">
                            <button onclick='editSpec(<?php echo json_encode($s); ?>, <?php echo json_encode($all_trans); ?>)' style="background: #3b82f6; color: white; border: none; width: 34px; height: 34px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'" title="Düzenle">
                                <i class="fas fa-edit" style="font-size: 14px;"></i>
                            </button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Bu uzmanlığı silmek istediğinize emin misiniz?')">
                                <input type="hidden" name="delete_specialty_id" value="<?php echo $s['id']; ?>">
                                <button type="submit" style="background: #ef4444; color: white; border: none; width: 34px; height: 34px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'" title="Sil">
                                    <i class="fas fa-trash-alt" style="font-size: 14px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($specialties)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">
                        <i class="fas fa-info-circle"></i> Henüz bir uzmanlık eklenmemiş.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div> <!-- Close specialties-management -->

<script>
function showAddForm() {
    document.getElementById('specForm').style.display = 'block';
    document.getElementById('sid').value = '';
    document.getElementById('ssort').value = '0';
    document.getElementById('sactive').checked = true;
    document.getElementById('titleText').innerText = 'Yeni Uzmanlık Ekle';
    
    <?php foreach($langs as $lang): ?>
    document.getElementById('stitle_<?php echo $lang['id']; ?>').value = '';
    <?php endforeach; ?>
    
    document.getElementById('specForm').scrollIntoView({ behavior: 'smooth' });
}

function hideAddForm() {
    document.getElementById('specForm').style.display = 'none';
}

function editSpec(s, trans) {
    document.getElementById('specForm').style.display = 'block';
    document.getElementById('sid').value = s.id;
    document.getElementById('ssort').value = s.sort_order;
    document.getElementById('sactive').checked = s.is_active == 1;
    document.getElementById('titleText').innerText = 'Uzmanlığı Düzenle';
    
    for (const [langId, title] of Object.entries(trans)) {
        const titleEl = document.getElementById('stitle_' + langId);
        if (titleEl) titleEl.value = title;
    }
    
    document.getElementById('specForm').scrollIntoView({ behavior: 'smooth' });
}

function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }
    tablinks = document.getElementsByClassName("tab-btn");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}
</script>

<?php include 'footer.php'; ?>
