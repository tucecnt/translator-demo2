<?php
include 'header.php';

// Fetch visibility setting
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'show_references'");
$stmt->execute();
$show_references = $stmt->fetchColumn();
if ($show_references === false) {
    // If it doesn't exist, create it with default '1'
    $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('show_references', '1')")->execute();
    $show_references = '1';
}

$langs = getAvailableLanguages($pdo);

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle toggle
    if (isset($_POST['toggle_visibility'])) {
        $new_val = $_POST['show_references_val'] == '1' ? '1' : '0';
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'show_references'");
        $stmt->execute([$new_val]);
        header('Location: references.php?success=1');
        exit;
    }
    if (isset($_POST['save_reference'])) {
        $rid = $_POST['id'];
        $sort_order = $_POST['sort_order'];
        $image_path = $_POST['old_image'] ?? '';

        // Handle File Upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
            $filename = $_FILES['logo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid('ref_') . '.' . $ext;
                $upload_dir = __DIR__ . '/../assets/img/references/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $new_filename)) {
                    $image_path = 'assets/img/references/' . $new_filename;
                }
            }
        }

        if (!empty($rid)) {
            // Update
            $stmt = $pdo->prepare("UPDATE `references` SET sort_order = ?, image = ? WHERE id = ?");
            $stmt->execute([$sort_order, $image_path, $rid]);
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO `references` (sort_order, image) VALUES (?, ?)");
            $stmt->execute([$sort_order, $image_path]);
            $rid = $pdo->lastInsertId();
        }

        // Save translations
        foreach ($_POST['trans'] as $lang_id => $data) {
            $stmt = $pdo->prepare("SELECT id FROM reference_translations WHERE reference_id = ? AND lang_id = ?");
            $stmt->execute([$rid, $lang_id]);
            $exists = $stmt->fetch();

            if ($exists) {
                $stmt = $pdo->prepare("UPDATE reference_translations SET title = ?, description = ? WHERE id = ?");
                $stmt->execute([$data['title'], $data['description'], $exists['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO reference_translations (reference_id, lang_id, title, description) VALUES (?, ?, ?, ?)");
                $stmt->execute([$rid, $lang_id, $data['title'], $data['description']]);
            }
        }
        header('Location: references.php?success=1');
        exit;
    }

    if (isset($_POST['delete_id'])) {
        $pdo->prepare("DELETE FROM `references` WHERE id = ?")->execute([$_POST['delete_id']]);
        header('Location: references.php?success=1');
        exit;
    }
}

// Fetch References
$refs = $pdo->query("SELECT * FROM `references` ORDER BY sort_order ASC, id DESC")->fetchAll();
?>

<div class="dashboard-header">
    <div>
        <h1>Referans Yönetimi</h1>
        <p>Çalıştığınız kurumları ve referanslarınızı bu alandan yönetebilirsiniz.</p>
    </div>
    <button class="btn-primary-admin" onclick="showAddForm()">
        <i class="fas fa-plus"></i> Yeni Referans Ekle
    </button>
</div>

<!-- Visibility Toggle Section -->
<div class="card-admin" style="margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; background: #fdfdfd; border-left: 4px solid <?php echo $show_references == '1' ? '#10b981' : '#64748b'; ?>; flex-wrap: wrap; gap: 20px;">
    <div style="flex: 1; min-width: 250px;">
        <h3 style="margin: 0; font-size: 16px; color: var(--admin-primary);">
            <i class="fas <?php echo $show_references == '1' ? 'fa-eye' : 'fa-eye-slash'; ?>" style="margin-right: 10px; color: <?php echo $show_references == '1' ? '#10b981' : '#64748b'; ?>;"></i>
            Referanslar Sayfası Görünürlüğü
        </h3>
        <p style="margin: 5px 0 0 28px; font-size: 13px; color: var(--admin-text-muted);">
            <?php echo $show_references == '1' ? 'Referanslar sayfası şu anda web sitesinde yayında.' : 'Referanslar sayfası şu anda gizli, ziyaretçiler göremez.'; ?>
        </p>
    </div>
    <form method="POST" style="width: 100%; max-width: 200px;">
        <input type="hidden" name="toggle_visibility" value="1">
        <input type="hidden" name="show_references_val" value="<?php echo $show_references == '1' ? '0' : '1'; ?>">
        <button type="submit" class="btn-primary-admin" style="background: <?php echo $show_references == '1' ? '#ef4444' : '#10b981'; ?>; padding: 10px 20px; width: 100%;">
            <?php echo $show_references == '1' ? '<i class="fas fa-eye-slash"></i> Sayfayı Gizle' : '<i class="fas fa-eye"></i> Sayfayı Göster'; ?>
        </button>
    </form>
</div>

<?php if(isset($_GET['success'])): ?>
<div style="background: #ecfdf5; color: #065f46; padding: 15px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #10b981;">
    <i class="fas fa-check-circle"></i> İşlem başarıyla gerçekleştirildi!
</div>
<?php endif; ?>

<!-- Add/Edit Form -->
<div id="refForm" class="form-card-admin" style="display:none; border-left: 5px solid var(--admin-accent);">
    <h3 style="margin-bottom: 25px; color: var(--admin-primary);">
        <i class="fas fa-handshake"></i> <span id="formTitle">Referans Bilgileri</span>
    </h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="rid">
        <input type="hidden" name="old_image" id="rold_image">
        
        <div class="admin-form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 25px;">
            <div class="form-group-admin">
                <label>Görüntüleme Sırası</label>
                <input type="number" name="sort_order" id="rsort" class="form-control-admin" value="0">
            </div>
            <div class="form-group-admin">
                <label>Kurum Logosu (Dosya Seçin)</label>
                <input type="file" name="logo" id="rlogo" class="form-control-admin" accept="image/*">
                <div id="current_logo_preview" style="margin-top: 10px; display: none;">
                    <span style="font-size: 12px; color: var(--admin-text-muted);">Mevcut Logo:</span>
                    <img id="logo_img_preview" src="" style="display: block; max-height: 50px; margin-top: 5px; border-radius: 4px; border: 1px solid var(--admin-border); padding: 5px;">
                </div>
            </div>
        </div>

        <div class="admin-form-grid">
            <?php foreach($langs as $lang): ?>
            <div style="background: #F8FAFC; padding: 20px; border-radius: 12px; border: 1px solid var(--admin-border);">
                <div class="badge-admin badge-blue" style="margin-bottom: 10px; display: inline-block;">
                    <i class="fas fa-flag"></i> <?php echo $lang['name']; ?> İçerik
                </div>
                <div class="form-group-admin">
                    <label>Firma / Kurum Adı</label>
                    <input type="text" name="trans[<?php echo $lang['id']; ?>][title]" id="rtitle_<?php echo $lang['id']; ?>" class="form-control-admin" placeholder="Örn: ABC Holding" required>
                </div>
                <div class="form-group-admin">
                    <label>Kısa Not / Açıklama</label>
                    <textarea name="trans[<?php echo $lang['id']; ?>][description]" id="rdesc_<?php echo $lang['id']; ?>" class="form-control-admin" style="height: 80px;" placeholder="Yapılan çalışma hakkında kısa bir not..."></textarea>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: right; padding-top: 20px; border-top: 1px solid var(--admin-border);">
            <button type="button" onclick="hideAddForm()" class="btn-primary-admin" style="background:#64748B; margin-right: 10px;">İptal</button>
            <button type="submit" name="save_reference" class="btn-primary-admin" style="background: var(--admin-accent);">
                <i class="fas fa-save"></i> Referansı Kaydet
            </button>
        </div>
    </form>
</div>

<div class="card-admin">
    <div class="table-responsive-container">
        <table class="table-admin">
            <thead>
                <tr>
                    <th width="100">Logo</th>
                    <th>Firma / Kurum (TR)</th>
                    <th>Açıklama</th>
                    <th width="150" style="text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($refs as $r): 
                    $stmt = $pdo->prepare("SELECT title, description FROM reference_translations WHERE reference_id = ? AND lang_id = 1");
                    $stmt->execute([$r['id']]);
                    $data_tr = $stmt->fetch();
                    
                    // Fetch all trans for JS
                    $stmt = $pdo->prepare("SELECT lang_id, title, description FROM reference_translations WHERE reference_id = ?");
                    $stmt->execute([$r['id']]);
                    $all_trans = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC);
                ?>
                <tr>
                    <td>
                        <div class="service-icon-preview">
                            <?php if($r['image']): ?>
                                <img src="../<?php echo htmlspecialchars($r['image']); ?>" style="max-width: 40px; max-height: 40px; object-fit: contain;">
                            <?php else: ?>
                                <i class="fas fa-building"></i>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--admin-primary);"><?php echo htmlspecialchars($data_tr['title'] ?? 'İsimsiz'); ?></div>
                    </td>
                    <td>
                        <div style="font-size: 13px; color: var(--admin-text-muted);">
                            <?php echo mb_substr(strip_tags($data_tr['description'] ?? ''), 0, 80) . '...'; ?>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons" style="justify-content: flex-end;">
                            <button class="btn-action edit" onclick='editRef(<?php echo json_encode($r); ?>, <?php echo json_encode($all_trans); ?>)' title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Bu referansı silmek istediğinize emin misiniz?')">
                                <input type="hidden" name="delete_id" value="<?php echo $r['id']; ?>">
                                <button type="submit" class="btn-action delete" title="Sil">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($refs)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 50px; color: var(--admin-text-muted);">
                        <i class="fas fa-info-circle"></i> Henüz bir referans eklemediniz.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function showAddForm() {
    document.getElementById('refForm').style.display = 'block';
    document.getElementById('rid').value = '';
    document.getElementById('rsort').value = '0';
    document.getElementById('rold_image').value = '';
    document.getElementById('rlogo').value = '';
    document.getElementById('formTitle').innerText = 'Yeni Referans Ekle';
    document.getElementById('current_logo_preview').style.display = 'none';
    
    <?php foreach($langs as $lang): ?>
    document.getElementById('rtitle_<?php echo $lang['id']; ?>').value = '';
    document.getElementById('rdesc_<?php echo $lang['id']; ?>').value = '';
    <?php endforeach; ?>
    
    document.getElementById('refForm').scrollIntoView({ behavior: 'smooth' });
}

function hideAddForm() {
    document.getElementById('refForm').style.display = 'none';
}

function editRef(r, trans) {
    document.getElementById('refForm').style.display = 'block';
    document.getElementById('rid').value = r.id;
    document.getElementById('rsort').value = r.sort_order;
    document.getElementById('rold_image').value = r.image;
    document.getElementById('rlogo').value = '';
    document.getElementById('formTitle').innerText = 'Referansı Düzenle';
    
    if (r.image) {
        document.getElementById('logo_img_preview').src = '../' + r.image;
        document.getElementById('current_logo_preview').style.display = 'block';
    } else {
        document.getElementById('current_logo_preview').style.display = 'none';
    }
    
    for (const [langId, data] of Object.entries(trans)) {
        const titleEl = document.getElementById('rtitle_' + langId);
        const descEl = document.getElementById('rdesc_' + langId);
        if (titleEl) titleEl.value = data.title;
        if (descEl) descEl.value = data.description;
    }
    
    document.getElementById('refForm').scrollIntoView({ behavior: 'smooth' });
}
</script>

<?php include 'footer.php'; ?>
