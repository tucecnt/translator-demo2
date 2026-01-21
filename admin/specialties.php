<?php
include 'header.php';

$langs = getAvailableLanguages($pdo);

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_specialty'])) {
        $sid = $_POST['id'];
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
        foreach ($_POST['trans'] as $lang_id => $title) {
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
        header('Location: specialties.php?success=1');
        exit;
    }

    if (isset($_POST['delete_id'])) {
        $pdo->prepare("DELETE FROM specialties WHERE id = ?")->execute([$_POST['delete_id']]);
        header('Location: specialties.php?success=1');
        exit;
    }
}

// Fetch Specialties
$stmt = $pdo->query("SELECT * FROM specialties ORDER BY sort_order ASC, id DESC");
$specialties = $stmt->fetchAll();
?>

<div class="dashboard-header">
    <div>
        <h1>Uzmanlık Alanları</h1>
        <p>Hakkımda sayfasındaki uzmanlık listesini yönetin.</p>
    </div>
    <button class="btn-primary-admin" onclick="showAddForm()">
        <i class="fas fa-plus"></i> Yeni Uzmanlık Ekle
    </button>
</div>

<?php if(isset($_GET['success'])): ?>
<div style="background: #ecfdf5; color: #065f46; padding: 15px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #10b981;">
    <i class="fas fa-check-circle"></i> İşlem başarıyla gerçekleştirildi!
</div>
<?php endif; ?>

<!-- Add/Edit Form (Hidden by default) -->
<div id="specForm" class="form-card-admin" style="display:none; border-left: 5px solid var(--admin-accent);">
    <h3 id="formTitle" style="margin-bottom: 25px; color: var(--admin-primary);">
        <i class="fas fa-edit"></i> <span id="titleText">Uzmanlık Düzenle</span>
    </h3>
    <form method="POST">
        <input type="hidden" name="id" id="sid">
        
        <div class="admin-form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 25px;">
            <div class="form-group-admin">
                <label>Görüntüleme Sırası</label>
                <input type="number" name="sort_order" id="ssort" class="form-control-admin" value="0">
            </div>
            <div class="form-group-admin" style="display: flex; align-items: flex-end; padding-bottom: 12px;">
                <label class="switch-admin" style="display: flex; align-items: center; gap: 10px; cursor: pointer; border: 1px solid #ddd; padding: 8px 15px; border-radius: 8px; width: 100%; background: #fff;">
                    <input type="checkbox" name="is_active" id="sactive" checked style="width: 20px; height: 20px;">
                    <span style="font-weight: 600;">Bu Uzmanlığı Yayına Al</span>
                </label>
            </div>
        </div>

        <div class="admin-form-grid">
            <?php foreach($langs as $lang): ?>
            <div style="background: #F8FAFC; padding: 20px; border-radius: 12px; border: 1px solid var(--admin-border);">
                <div class="badge-admin badge-blue" style="margin-bottom: 10px; display: inline-block;">
                    <i class="fas fa-globe"></i> <?php echo $lang['name']; ?>
                </div>
                <div class="form-group-admin">
                    <label>Başlık</label>
                    <input type="text" name="trans[<?php echo $lang['id']; ?>]" id="stitle_<?php echo $lang['id']; ?>" class="form-control-admin" placeholder="Örn: Hukuki Tercüme" required>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: right; padding-top: 20px; border-top: 1px solid var(--admin-border);">
            <button type="button" onclick="hideAddForm()" class="btn-primary-admin" style="background:#64748B; margin-right: 10px;">İptal</button>
            <button type="submit" name="save_specialty" class="btn-primary-admin" style="background: var(--admin-accent);">
                <i class="fas fa-save"></i> Bilgileri Kaydet
            </button>
        </div>
    </form>
</div>

<div class="card-admin">
    <div class="table-responsive-container">
        <table class="table-admin">
            <thead>
                <tr>
                    <th width="60">Sıra</th>
                    <th width="100">Durum</th>
                    <th>Uzmanlık Alanı (TR)</th>
                    <th width="150" style="text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($specialties as $s): 
                    $stmt = $pdo->prepare("SELECT title FROM specialty_translations WHERE specialty_id = ? AND lang_id = 1");
                    $stmt->execute([$s['id']]);
                    $title_tr = $stmt->fetchColumn() ?: 'Başlıksız';
                    
                    // Fetch all trans for JS
                    $stmt = $pdo->prepare("SELECT lang_id, title FROM specialty_translations WHERE specialty_id = ?");
                    $stmt->execute([$s['id']]);
                    $all_trans = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                ?>
                <tr>
                    <td style="text-align: center;">
                        <span class="badge-admin" style="background: #F1F5F9; color: var(--admin-text-main);"><?php echo $s['sort_order']; ?></span>
                    </td>
                    <td>
                        <?php if($s['is_active']): ?>
                            <span class="badge-admin" style="background: #ECFDF5; color: #10B981;"><i class="fas fa-check"></i> Aktif</span>
                        <?php else: ?>
                            <span class="badge-admin" style="background: #FEF2F2; color: #EF4444;"><i class="fas fa-times"></i> Pasif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--admin-primary); font-size: 15px;">
                            <i class="fas fa-star" style="color: var(--admin-accent); margin-right: 8px; font-size: 12px;"></i>
                            <?php echo htmlspecialchars($title_tr); ?>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons" style="justify-content: flex-end;">
                            <button class="btn-action edit" onclick='editSpec(<?php echo json_encode($s); ?>, <?php echo json_encode($all_trans); ?>)' title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Bu uzmanlığı silmek istediğinize emin misiniz?')">
                                <input type="hidden" name="delete_id" value="<?php echo $s['id']; ?>">
                                <button type="submit" class="btn-action delete" title="Sil">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($specialties)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 50px; color: var(--admin-text-muted);">
                        <i class="fas fa-info-circle"></i> Henüz bir uzmanlık eklemediniz.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

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
</script>

<?php include 'footer.php'; ?>
