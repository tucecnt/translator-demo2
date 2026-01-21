<?php
include 'header.php';

$langs = getAvailableLanguages($pdo);

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: services.php');
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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_service'])) {
    // Generate slug from Turkish title (lang_id 1)
    $slug = createSlug($_POST['trans'][1]['title']);
    $icon = $_POST['icon'];
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $stmt = $pdo->prepare("UPDATE services SET slug = ?, icon = ? WHERE id = ?");
        $stmt->execute([$slug, $icon, $_POST['id']]);
        $service_id = $_POST['id'];
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO services (slug, icon) VALUES (?, ?)");
        $stmt->execute([$slug, $icon]);
        $service_id = $pdo->lastInsertId();
    }
    
    // Save translations
    foreach ($_POST['trans'] as $lang_id => $data) {
        $stmt = $pdo->prepare("SELECT id FROM service_translations WHERE service_id = ? AND lang_id = ?");
        $stmt->execute([$service_id, $lang_id]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            $stmt = $pdo->prepare("UPDATE service_translations SET title = ?, content = ? WHERE id = ?");
            $stmt->execute([$data['title'], $data['content'], $exists['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO service_translations (service_id, lang_id, title, content) VALUES (?, ?, ?, ?)");
            $stmt->execute([$service_id, $lang_id, $data['title'], $data['content']]);
        }
    }
    header('Location: services.php?success=1');
    exit;
}

$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC")->fetchAll();
?>

<div class="dashboard-header">
    <div>
        <h1>Hizmet Yönetimi</h1>
        <p>Hizmetlerinizi ekleyin, düzenleyin veya kaldırın.</p>
    </div>
    <button class="btn-primary-admin" onclick="showAddForm()">
        <i class="fas fa-plus"></i> Yeni Hizmet Ekle
    </button>
</div>

<!-- Add/Edit Form -->
<div id="serviceForm" class="form-card-admin" style="display:none;">
    <form method="POST">
        <input type="hidden" name="id" id="sid">
        
        <div class="form-group-admin">
            <label>İkon Seçin</label>
            <input type="hidden" name="icon" id="sicon">
            <div class="icon-grid-admin">
                <?php 
                $available_icons = [
                    // Mevcut ikonlar
                    'fas fa-stamp', 'fas fa-balance-scale', 'fas fa-globe', 'fas fa-file-invoice',
                    'fas fa-gavel', 'fas fa-user-tie', 'fas fa-briefcase', 'fas fa-graduation-cap',
                    'fas fa-certificate', 'fas fa-landmark', 'fas fa-language', 'fas fa-handshake',
                    'fas fa-microchip', 'fas fa-heartbeat', 'fas fa-shield-halved', 'fas fa-file-contract',
                    'fas fa-passport', 'fas fa-signature', 'fas fa-university', 'fas fa-scroll',
                    'fas fa-briefcase-medical', 'fas fa-book-legal', 'fas fa-award',
                    // Yeni eklenen ikonlar
                    'fas fa-file-alt', 'fas fa-file-pdf', 'fas fa-file-word', 'fas fa-clipboard-check',
                    'fas fa-pen-fancy', 'fas fa-pen-nib', 'fas fa-feather-alt', 'fas fa-edit',
                    'fas fa-book', 'fas fa-book-open', 'fas fa-bookmark', 'fas fa-glasses',
                    'fas fa-search', 'fas fa-search-plus', 'fas fa-check-circle', 'fas fa-check-double',
                    'fas fa-star', 'fas fa-medal', 'fas fa-trophy', 'fas fa-crown',
                    'fas fa-building', 'fas fa-city', 'fas fa-home', 'fas fa-hospital',
                    'fas fa-school', 'fas fa-store', 'fas fa-warehouse', 'fas fa-industry',
                    'fas fa-plane', 'fas fa-ship', 'fas fa-truck', 'fas fa-car',
                    'fas fa-users', 'fas fa-user-graduate', 'fas fa-user-md', 'fas fa-user-shield',
                    'fas fa-comments', 'fas fa-comment-dots', 'fas fa-envelope', 'fas fa-envelope-open',
                    'fas fa-phone', 'fas fa-fax', 'fas fa-print', 'fas fa-desktop',
                    'fas fa-laptop', 'fas fa-mobile-alt', 'fas fa-tablet-alt', 'fas fa-keyboard',
                    'fas fa-chart-line', 'fas fa-chart-bar', 'fas fa-chart-pie', 'fas fa-project-diagram',
                    'fas fa-sitemap', 'fas fa-network-wired', 'fas fa-server', 'fas fa-database',
                    'fas fa-cog', 'fas fa-cogs', 'fas fa-tools', 'fas fa-wrench',
                    'fas fa-hammer', 'fas fa-screwdriver', 'fas fa-paint-brush', 'fas fa-palette'
                ];
                foreach ($available_icons as $ico):
                ?>
                <div class="icon-item-admin" onclick="selectIcon('<?php echo $ico; ?>', this)" title="<?php echo $ico; ?>">
                    <i class="<?php echo $ico; ?>"></i>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="admin-form-grid">
            <?php foreach($langs as $lang): ?>
            <div style="background: #F8FAFC; padding: 20px; border-radius: 12px; border: 1px solid var(--admin-border);">
                <span class="badge-admin badge-blue" style="margin-bottom: 15px; display: inline-block;">
                    <i class="fas fa-flag"></i> <?php echo $lang['name']; ?> İçerik
                </span>
                <div class="form-group-admin">
                    <label>Hizmet Başlığı</label>
                    <input type="text" name="trans[<?php echo $lang['id']; ?>][title]" id="stitle_<?php echo $lang['id']; ?>" class="form-control-admin" placeholder="Örn: Noter Tasdikli Tercüme">
                </div>
                <div class="form-group-admin">
                    <label>Açıklama</label>
                    <textarea name="trans[<?php echo $lang['id']; ?>][content]" id="scontent_<?php echo $lang['id']; ?>" class="form-control-admin" style="height: 100px;" placeholder="Hizmet detaylarını buraya yazın..."></textarea>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div style="margin-top: 30px; text-align: right; pt-20">
            <hr style="border: none; border-top: 1px solid var(--admin-border); margin-bottom: 25px;">
            <button type="button" onclick="hideAddForm()" class="btn-primary-admin" style="background:#64748B; margin-right: 10px;">İptal</button>
            <button type="submit" name="save_service" class="btn-primary-admin" style="background:var(--admin-accent);">Değişiklikleri Kaydet</button>
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
                    <th>Hizmet Adı (TR)</th>
                    <th width="150" style="text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($services as $s): 
                    $stmt = $pdo->prepare("SELECT title, content FROM service_translations WHERE service_id = ? AND lang_id = 1");
                    $stmt->execute([$s['id']]);
                    $tr_data = $stmt->fetch();
                    
                    // Get all translations for JS edit
                    $stmt = $pdo->prepare("SELECT lang_id, title, content FROM service_translations WHERE service_id = ?");
                    $stmt->execute([$s['id']]);
                    $all_trans = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC);
                ?>
                <tr>
                    <td>
                        <div class="service-icon-preview">
                            <i class="<?php echo $s['icon']; ?>"></i>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--admin-primary);"><?php echo htmlspecialchars($tr_data['title'] ?? 'Başlıksız'); ?></div>
                        <div style="font-size: 13px; color: var(--admin-text-muted);"><?php echo mb_substr(strip_tags($tr_data['content']), 0, 80) . '...'; ?></div>
                    </td>
                    <td>
                        <div class="action-buttons" style="justify-content: flex-end;">
                            <button class="btn-action edit" onclick='editService(<?php echo json_encode($s); ?>, <?php echo json_encode($all_trans); ?>)' title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete=<?php echo $s['id']; ?>" class="btn-action delete" onclick="return confirm('Bu hizmeti silmek istediğinize emin misiniz?')" title="Sil">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($services)): ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 50px; color: var(--admin-text-muted);">
                        Henüz hizmet eklenmemiş.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function showAddForm() {
    document.getElementById('serviceForm').style.display = 'block';
    document.getElementById('sid').value = '';
    document.getElementById('sicon').value = '';
    
    // Clear fields
    <?php foreach($langs as $lang): ?>
    document.getElementById('stitle_<?php echo $lang['id']; ?>').value = '';
    document.getElementById('scontent_<?php echo $lang['id']; ?>').value = '';
    <?php endforeach; ?>
    
    document.querySelectorAll('.icon-item-admin').forEach(opt => opt.classList.remove('selected'));
    document.getElementById('serviceForm').scrollIntoView({ behavior: 'smooth' });
}

function hideAddForm() {
    document.getElementById('serviceForm').style.display = 'none';
}

function selectIcon(icon, el) {
    document.getElementById('sicon').value = icon;
    document.querySelectorAll('.icon-item-admin').forEach(opt => opt.classList.remove('selected'));
    if(el) el.classList.add('selected');
}

function editService(s, trans) {
    showAddForm();
    document.getElementById('sid').value = s.id;
    document.getElementById('sicon').value = s.icon;
    
    // Set field values
    for (const [langId, data] of Object.entries(trans)) {
        const titleEl = document.getElementById('stitle_' + langId);
        const contentEl = document.getElementById('scontent_' + langId);
        if (titleEl) titleEl.value = data.title;
        if (contentEl) contentEl.value = data.content;
    }
    
    // Highlight selected icon
    document.querySelectorAll('.icon-item-admin').forEach(opt => {
        opt.classList.remove('selected');
        if(opt.getAttribute('title') === s.icon) {
            opt.classList.add('selected');
        }
    });
}
</script>

<?php include 'footer.php'; ?>
