<?php
include 'header.php';

$langs = getAvailableLanguages($pdo);

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM document_categories WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: document-categories.php');
    exit;
}

// Helper to create slug
function createSlug($str) {
    if (empty($str)) return uniqid();
    $turkish = array("ı", "ğ", "ü", "ş", "ö", "ç", "İ", "Ğ", "Ü", "Ş", "Ö", "Ç");
    $latin   = array("i", "g", "u", "s", "o", "c", "i", "g", "u", "s", "o", "c");
    $str = str_replace($turkish, $latin, $str);
    $str = strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9-]/', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-');
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_category'])) {
    $icon = $_POST['icon'];
    $sort_order = $_POST['sort_order'];
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $stmt = $pdo->prepare("UPDATE document_categories SET icon = ?, sort_order = ? WHERE id = ?");
        $stmt->execute([$icon, $sort_order, $_POST['id']]);
        $cat_id = $_POST['id'];
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO document_categories (icon, sort_order) VALUES (?, ?)");
        $stmt->execute([$icon, $sort_order]);
        $cat_id = $pdo->lastInsertId();
    }
    
    // Save translations
    foreach ($_POST['trans'] as $lang_id => $data) {
        $stmt = $pdo->prepare("SELECT id FROM document_category_translations WHERE category_id = ? AND lang_id = ?");
        $stmt->execute([$cat_id, $lang_id]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            $stmt = $pdo->prepare("UPDATE document_category_translations SET title = ? WHERE id = ?");
            $stmt->execute([$data['title'], $exists['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO document_category_translations (category_id, lang_id, title) VALUES (?, ?, ?)");
            $stmt->execute([$cat_id, $lang_id, $data['title']]);
        }
    }
    header('Location: document-categories.php?success=1');
    exit;
}

$categories = $pdo->query("SELECT * FROM document_categories ORDER BY sort_order ASC, id ASC")->fetchAll();
?>

<div class="dashboard-header">
    <div>
        <h1>Belge Kategorileri Yönetimi</h1>
        <p>Çeviri yaptığınız belge türlerini bu alandan yönetebilirsiniz.</p>
    </div>
    <button class="btn-primary-admin" onclick="showAddForm()">
        <i class="fas fa-plus"></i> Yeni Belge Kategorisi Ekle
    </button>
</div>

<!-- Add/Edit Form -->
<div id="categoryForm" class="form-card-admin" style="display:none;">
    <form method="POST">
        <input type="hidden" name="id" id="cid">
        
        <div class="form-group-admin">
            <label>İkon Seçin</label>
            <input type="hidden" name="icon" id="cicon">
            <div class="icon-grid-admin">
                <?php 
                $available_icons = [
                    'fas fa-graduation-cap', 'fas fa-certificate', 'fas fa-award', 'fas fa-university',
                    'fas fa-file-invoice', 'fas fa-clipboard-check', 'fas fa-handshake', 'fas fa-chart-line',
                    'fas fa-file-contract', 'fas fa-baby', 'fas fa-id-card', 'fas fa-ring',
                    'fas fa-file-signature', 'fas fa-address-card', 'fas fa-cross', 'fas fa-passport',
                    'fas fa-home', 'fas fa-stamp', 'fas fa-shield-halved', 'fas fa-globe',
                    'fas fa-syringe', 'fas fa-user-md', 'fas fa-hospital', 'fas fa-heartbeat',
                    'fas fa-file-alt', 'fas fa-key', 'fas fa-gavel', 'fas fa-dollar-sign',
                    'fas fa-scroll', 'fas fa-flag', 'fas fa-signature', 'fas fa-briefcase',
                    'fas fa-building', 'fas fa-landmark', 'fas fa-balance-scale', 'fas fa-book',
                    'fas fa-pen-fancy', 'fas fa-feather-alt', 'fas fa-edit', 'fas fa-file-pdf'
                ];
                foreach ($available_icons as $ico):
                ?>
                <div class="icon-item-admin" onclick="selectIcon('<?php echo $ico; ?>', this)" title="<?php echo $ico; ?>">
                    <i class="<?php echo $ico; ?>"></i>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group-admin">
            <label>Kategori Seçin</label>
            <select name="sort_order" id="csort" class="form-control-admin" required>
                <option value="">-- Kategori Seçin --</option>
                <option value="1">📚 Akademik Belgeler</option>
                <option value="2">💼 İş Belgeleri</option>
                <option value="3">👤 Kişisel Belgeler</option>
                <option value="4">🏛️ Resmi Belgeler</option>
                <option value="5">🏥 Tıbbi Belgeler</option>
                <option value="6">⚖️ Yasal Belgeler</option>
            </select>
            <small style="color: var(--admin-text-muted); display: block; margin-top: 5px;">
                Bu belge hangi kategoriye ait?
            </small>
        </div>
        
        <div class="admin-form-grid">
            <?php foreach($langs as $lang): ?>
            <div style="background: #F8FAFC; padding: 20px; border-radius: 12px; border: 1px solid var(--admin-border);">
                <span class="badge-admin badge-blue" style="margin-bottom: 15px; display: inline-block;">
                    <i class="fas fa-flag"></i> <?php echo $lang['name']; ?> İçerik
                </span>
                <div class="form-group-admin">
                    <label>Belge Adı</label>
                    <input type="text" name="trans[<?php echo $lang['id']; ?>][title]" id="ctitle_<?php echo $lang['id']; ?>" class="form-control-admin" placeholder="Örn: Diploma, Pasaport, Sözleşme" required>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div style="margin-top: 30px; text-align: right; pt-20">
            <hr style="border: none; border-top: 1px solid var(--admin-border); margin-bottom: 25px;">
            <button type="button" onclick="hideAddForm()" class="btn-primary-admin" style="background:#64748B; margin-right: 10px;">İptal</button>
            <button type="submit" name="save_category" class="btn-primary-admin" style="background:var(--admin-accent);">Değişiklikleri Kaydet</button>
        </div>
    </form>
</div>

<?php if(isset($_GET['success'])): ?>
<div style="background: #ecfdf5; color: #065f46; padding: 15px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #10b981;">
    <i class="fas fa-check-circle"></i> İşlem başarıyla gerçekleştirildi!
</div>
<?php endif; ?>

<div class="card-admin">
    <div class="table-responsive-container">
        <table class="table-admin">
            <thead>
                <tr>
                    <th width="80">İkon</th>
                    <th>Belge Adı (TR)</th>
                    <th width="150" style="text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $category_names = [
                    1 => '📚 Akademik Belgeler',
                    2 => '💼 İş Belgeleri',
                    3 => '👤 Kişisel Belgeler',
                    4 => '🏛️ Resmi Belgeler',
                    5 => '🏥 Tıbbi Belgeler',
                    6 => '⚖️ Yasal Belgeler'
                ];
                
                $current_group = null;
                
                foreach($categories as $c): 
                    // Group Header
                    if ($current_group !== $c['sort_order']) {
                        $current_group = $c['sort_order'];
                        // Added default rotation to -90deg (collapsed)
                        echo '<tr class="group-header" onclick="toggleGroup(' . $current_group . ')" style="cursor: pointer;">
                                <td colspan="3" style="background: #F8FAFC; color: var(--admin-sidebar-bg); font-weight: 800; padding: 15px 20px; font-size: 15px; border-bottom: 2px solid var(--admin-border);">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>' . ($category_names[$current_group] ?? 'Diğer') . '</span>
                                        <i class="fas fa-chevron-down" id="icon-group-' . $current_group . '" style="transition: transform 0.3s; transform: rotate(-90deg);"></i>
                                    </div>
                                </td>
                              </tr>';
                    }
                
                    $stmt = $pdo->prepare("SELECT title FROM document_category_translations WHERE category_id = ? AND lang_id = 1");
                    $stmt->execute([$c['id']]);
                    $tr_data = $stmt->fetch();
                    
                    // Get all translations for JS edit
                    $stmt = $pdo->prepare("SELECT lang_id, title FROM document_category_translations WHERE category_id = ?");
                    $stmt->execute([$c['id']]);
                    $all_trans = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC);
                ?>
                <!-- Added style display:none for default collapsed state -->
                <tr class="group-row group-<?php echo $c['sort_order']; ?>" style="display: none;">
                    <td>
                        <div class="service-icon-preview">
                            <i class="<?php echo $c['icon']; ?>"></i>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--admin-primary);"><?php echo htmlspecialchars($tr_data['title'] ?? 'Başlıksız'); ?></div>
                    </td>
                    <td>
                        <div class="action-buttons" style="justify-content: flex-end;">
                            <button class="btn-action edit" onclick='editCategory(<?php echo json_encode($c); ?>, <?php echo json_encode($all_trans); ?>)' title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete=<?php echo $c['id']; ?>" class="btn-action delete" onclick="return confirm('Bu belge kategorisini silmek istediğinize emin misiniz?')" title="Sil">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($categories)): ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 50px; color: var(--admin-text-muted);">
                        Henüz belge kategorisi eklenmemiş.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleGroup(groupId) {
    // Toggle rows
    var rows = document.querySelectorAll('.group-' + groupId);
    var isHidden = false;
    
    rows.forEach(function(row) {
        if (row.style.display === 'none') {
            row.style.display = 'table-row';
            isHidden = false;
        } else {
            row.style.display = 'none';
            isHidden = true;
        }
    });

    // Rotate Icon
    var icon = document.getElementById('icon-group-' + groupId);
    if (isHidden) {
        icon.style.transform = 'rotate(-90deg)';
    } else {
        icon.style.transform = 'rotate(0deg)';
    }
}

function showAddForm() {
    document.getElementById('categoryForm').style.display = 'block';
    
    document.getElementById('cid').value = '';
    document.getElementById('cicon').value = '';
    document.getElementById('csort').value = '';
    
    // Clear fields
    <?php foreach($langs as $lang): ?>
    document.getElementById('ctitle_<?php echo $lang['id']; ?>').value = '';
    <?php endforeach; ?>
    
    document.querySelectorAll('.icon-item-admin').forEach(opt => opt.classList.remove('selected'));
    document.getElementById('categoryForm').scrollIntoView({ behavior: 'smooth' });
}

function hideAddForm() {
    document.getElementById('categoryForm').style.display = 'none';
}

function selectIcon(icon, el) {
    document.getElementById('cicon').value = icon;
    document.querySelectorAll('.icon-item-admin').forEach(opt => opt.classList.remove('selected'));
    if(el) el.classList.add('selected');
}

function editCategory(c, trans) {
    showAddForm();
    document.getElementById('cid').value = c.id;
    document.getElementById('cicon').value = c.icon;
    document.getElementById('csort').value = c.sort_order;
    
    // Set field values
    for (const [langId, data] of Object.entries(trans)) {
        const titleEl = document.getElementById('ctitle_' + langId);
        if (titleEl) titleEl.value = data.title;
    }
    
    // Highlight selected icon
    document.querySelectorAll('.icon-item-admin').forEach(opt => {
        opt.classList.remove('selected');
        if(opt.getAttribute('title') === c.icon) {
            opt.classList.add('selected');
        }
    });
}
</script>

<?php include 'footer.php'; ?>
