<?php
include 'header.php';

$langs = getAvailableLanguages($pdo);

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM faqs WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: faqs.php');
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_faq'])) {
    $sort_order = $_POST['sort_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $stmt = $pdo->prepare("UPDATE faqs SET sort_order = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$sort_order, $is_active, $_POST['id']]);
        $faq_id = $_POST['id'];
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO faqs (sort_order, is_active) VALUES (?, ?)");
        $stmt->execute([$sort_order, $is_active]);
        $faq_id = $pdo->lastInsertId();
    }
    
    // Save translations
    foreach ($_POST['trans'] as $lang_id => $data) {
        $stmt = $pdo->prepare("SELECT id FROM faq_translations WHERE faq_id = ? AND lang_id = ?");
        $stmt->execute([$faq_id, $lang_id]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            $stmt = $pdo->prepare("UPDATE faq_translations SET question = ?, answer = ? WHERE id = ?");
            $stmt->execute([$data['question'], $data['answer'], $exists['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO faq_translations (faq_id, lang_id, question, answer) VALUES (?, ?, ?, ?)");
            $stmt->execute([$faq_id, $lang_id, $data['question'], $data['answer']]);
        }
    }
    header('Location: faqs.php?success=1');
    exit;
}

$faqs = $pdo->query("SELECT * FROM faqs ORDER BY sort_order ASC, id DESC")->fetchAll();
?>

<div class="dashboard-header">
    <div>
        <h1>SSS Yönetimi</h1>
        <p>Sıkça sorulan soruları ekleyin, düzenleyin veya kaldırın.</p>
    </div>
    <button class="btn-primary-admin" onclick="showAddForm()">
        <i class="fas fa-plus"></i> Yeni Soru Ekle
    </button>
</div>

<!-- Add/Edit Form -->
<div id="faqForm" class="form-card-admin" style="display:none;">
    <form method="POST">
        <input type="hidden" name="id" id="fid">
        
        <div class="admin-form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 20px;">
            <div class="form-group-admin">
                <label>Görüntüleme Sırası</label>
                <input type="number" name="sort_order" id="fsort" class="form-control-admin" value="0">
            </div>
            <div class="form-group-admin" style="display: flex; align-items: flex-end; padding-bottom: 10px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; border: 1px solid #ddd; padding: 8px 15px; border-radius: 8px; width: 100%; background: #fff;">
                    <input type="checkbox" name="is_active" id="factive" checked style="width: 20px; height: 20px;">
                    <span style="font-weight: 600;">Aktif / Yayında</span>
                </label>
            </div>
        </div>
        
        <div class="admin-form-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <?php foreach($langs as $lang): ?>
            <div style="background: #F8FAFC; padding: 20px; border-radius: 12px; border: 1px solid var(--admin-border);">
                <span class="badge-admin badge-blue" style="margin-bottom: 15px; display: inline-block;">
                    <i class="fas fa-flag"></i> <?php echo $lang['name']; ?> İçerik
                </span>
                <div class="form-group-admin">
                    <label>Soru</label>
                    <input type="text" name="trans[<?php echo $lang['id']; ?>][question]" id="fquestion_<?php echo $lang['id']; ?>" class="form-control-admin" placeholder="Örn: Tercüme süreci nasıl işler?">
                </div>
                <div class="form-group-admin">
                    <label>Cevap</label>
                    <textarea name="trans[<?php echo $lang['id']; ?>][answer]" id="fanswer_<?php echo $lang['id']; ?>" class="form-control-admin" style="height: 120px;" placeholder="Cevabı buraya yazın..."></textarea>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div style="margin-top: 30px; text-align: right;">
            <hr style="border: none; border-top: 1px solid var(--admin-border); margin-bottom: 25px;">
            <button type="button" onclick="hideAddForm()" class="btn-primary-admin" style="background:#64748B; margin-right: 10px;">İptal</button>
            <button type="submit" name="save_faq" class="btn-primary-admin" style="background:var(--admin-accent);">Değişiklikleri Kaydet</button>
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
                    <th width="80">Sıra</th>
                    <th width="100">Durum</th>
                    <th>Soru (TR)</th>
                    <th width="150" style="text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($faqs as $f): 
                    $stmt = $pdo->prepare("SELECT question FROM faq_translations WHERE faq_id = ? AND lang_id = 1");
                    $stmt->execute([$f['id']]);
                    $q_tr = $stmt->fetchColumn();
                    
                    // Get all translations for JS edit
                    $stmt = $pdo->prepare("SELECT lang_id, question, answer FROM faq_translations WHERE faq_id = ?");
                    $stmt->execute([$f['id']]);
                    $all_trans = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_ASSOC);
                ?>
                <tr>
                    <td>
                        <span class="badge-admin badge-blue"><?php echo $f['sort_order']; ?></span>
                    </td>
                    <td>
                        <?php if($f['is_active']): ?>
                            <span style="color: #10B981; font-weight: 600;"><i class="fas fa-check-circle"></i> Aktif</span>
                        <?php else: ?>
                            <span style="color: #EF4444; font-weight: 600;"><i class="fas fa-times-circle"></i> Pasif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--admin-primary);"><?php echo htmlspecialchars($q_tr ?: 'Başlıksız'); ?></div>
                    </td>
                    <td>
                        <div class="action-buttons" style="justify-content: flex-end;">
                            <button class="btn-action edit" onclick='editFaq(<?php echo json_encode($f); ?>, <?php echo json_encode($all_trans); ?>)' title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete=<?php echo $f['id']; ?>" class="btn-action delete" onclick="return confirm('Bu soruyu silmek istediğinize emin misiniz?')" title="Sil">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($faqs)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 50px; color: var(--admin-text-muted);">
                        Henüz soru eklenmemiş.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function showAddForm() {
    document.getElementById('faqForm').style.display = 'block';
    document.getElementById('fid').value = '';
    document.getElementById('fsort').value = '0';
    document.getElementById('factive').checked = true;
    
    // Clear fields
    <?php foreach($langs as $lang): ?>
    document.getElementById('fquestion_<?php echo $lang['id']; ?>').value = '';
    document.getElementById('fanswer_<?php echo $lang['id']; ?>').value = '';
    <?php endforeach; ?>
    
    document.getElementById('faqForm').scrollIntoView({ behavior: 'smooth' });
}

function hideAddForm() {
    document.getElementById('faqForm').style.display = 'none';
}

function editFaq(f, trans) {
    showAddForm();
    document.getElementById('fid').value = f.id;
    document.getElementById('fsort').value = f.sort_order;
    document.getElementById('factive').checked = f.is_active == 1;
    
    // Set field values
    for (const [langId, data] of Object.entries(trans)) {
        const qEl = document.getElementById('fquestion_' + langId);
        const aEl = document.getElementById('fanswer_' + langId);
        if (qEl) qEl.value = data.question;
        if (aEl) aEl.value = data.answer;
    }
}
</script>

<?php include 'footer.php'; ?>
