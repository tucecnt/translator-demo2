<?php
include 'header.php';

$langs = getAvailableLanguages($pdo);

// Grouping and Descriptive Labels Mapping
$mapping = [
    'Ana Sayfa - Giriş (Hero)' => [
        'hero_title' => 'Büyük Başlık (H1)',
        'hero_subtitle' => 'Alt Başlık / Açıklama',
        'hero_btn' => 'Buton Metni (Teklif Al)',
    ],
    'Ana Sayfa - Neden Biz?' => [
        'trust_title' => 'Bölüm Başlığı',
        'trust_card1_title' => 'Kart 1: Başlık',
        'trust_card1_text' => 'Kart 1: Açıklama',
        'trust_card2_title' => 'Kart 2: Başlık',
        'trust_card2_text' => 'Kart 2: Açıklama',
        'trust_card3_title' => 'Kart 3: Başlık',
        'trust_card3_text' => 'Kart 3: Açıklama',
    ],
    'Çeviri Süreci' => [
        'process_title' => 'Sayfa Başlığı',
        'process_subtitle' => 'Alt Başlık',
        'process_step1_title' => 'Adım 1: Başlık',
        'process_step1_text' => 'Adım 1: Açıklama',
        'process_step2_title' => 'Adım 2: Başlık',
        'process_step2_text' => 'Adım 2: Açıklama',
        'process_step3_title' => 'Adım 3: Başlık',
        'process_step3_text' => 'Adım 3: Açıklama',
        'process_step4_title' => 'Adım 4: Başlık',
        'process_step4_text' => 'Adım 4: Açıklama',
        'process_step5_title' => 'Adım 5: Başlık',
        'process_step5_text' => 'Adım 5: Açıklama',
    ]
];

// Handle Save
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_translation'])) {
    foreach ($_POST['trans'] as $lang_id => $keys) {
        foreach ($keys as $key => $value) {
            $stmt = $pdo->prepare("REPLACE INTO translations (lang_id, `key`, `value`) VALUES (?, ?, ?)");
            $stmt->execute([$lang_id, $key, $value]);
        }
    }
    echo "<div class='alert alert-success'>Tüm değişiklikler başarıyla kaydedildi!</div>";
}

?>

<style>
    .trans-group { margin-bottom: 40px; background: #fff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; }
    .trans-group-header { background: #002B5B; color: #fff; padding: 15px 25px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
    .trans-group-content { padding: 25px; display: none; }
    .trans-row { display: grid; grid-template-columns: 200px 1fr 1fr 1fr; gap: 20px; align-items: start; padding: 15px 0; border-bottom: 1px solid #eee; }
    .trans-row:last-child { border-bottom: none; }
    .trans-label { font-size: 14px; font-weight: 600; color: #333; }
    .trans-key { font-size: 11px; color: #999; display: block; margin-top: 5px; font-family: monospace; }
    .lang-label { font-size: 12px; font-weight: bold; color: #002B5B; margin-bottom: 5px; display: block; }
    textarea.trans-input { width: 100%; border: 1px solid #ddd; border-radius: 6px; padding: 10px; font-size: 14px; transition: border-color 0.2s; min-height: 80px; resize: vertical; }
    textarea.trans-input:focus { border-color: #002B5B; outline: none; box-shadow: 0 0 0 3px rgba(0,43,91,0.1); }
    .alert { padding: 15px 25px; border-radius: 8px; margin-bottom: 25px; }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .search-box { width: 100%; padding: 12px 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 25px; font-size: 16px; }
    .sticky-actions { position: sticky; bottom: 20px; background: rgba(255,255,255,0.9); padding: 15px; border-radius: 12px; box-shadow: 0 -4px 10px rgba(0,0,0,0.1); backdrop-filter: blur(5px); z-index: 100; text-align: right; }
    
    /* Why Us Card Styling */
    .why-us-cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .why-us-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        padding: 25px;
        border: 2px solid #e5e7eb;
        transition: all 0.3s;
        position: relative;
    }
    
    .why-us-card:hover {
        border-color: #002B5B;
        box-shadow: 0 8px 20px rgba(0,43,91,0.15);
        transform: translateY(-3px);
    }
    
    .why-us-card-number {
        position: absolute;
        top: -15px;
        left: 20px;
        background: #002B5B;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        box-shadow: 0 4px 12px rgba(0,43,91,0.3);
    }
    
    .why-us-card-header {
        margin-top: 15px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .why-us-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #002B5B;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .why-us-lang-section {
        margin-bottom: 15px;
    }
    
    .why-us-lang-label {
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: block;
    }
    
    .why-us-input-title {
        width: 100%;
        border: 2px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s;
        margin-bottom: 8px;
    }
    
    .why-us-input-title:focus {
        border-color: #002B5B;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0,43,91,0.1);
    }
    
    .why-us-input-text {
        width: 100%;
        border: 2px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        transition: all 0.3s;
        min-height: 80px;
        resize: vertical;
    }
    
    .why-us-input-text:focus {
        border-color: #002B5B;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0,43,91,0.1);
    }

    @media (max-width: 768px) {
        .trans-row {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .trans-group-content {
            padding: 15px;
        }
        .why-us-cards-container {
            grid-template-columns: 1fr;
        }
        .sticky-actions {
            text-align: center;
        }
        .sticky-actions span {
            display: none;
        }
        .why-us-card {
            padding: 15px;
        }
    }
</style>

<div class="card-admin">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Profesyonel Çeviri Yönetimi</h2>
        <p style="color: #666;">Web sitenizdeki tüm metinleri buradan kolayca düzenleyebilirsiniz.</p>
    </div>

    <input type="text" id="transSearch" class="search-box" placeholder="Metin veya anahtar kelime ara...">

    <form method="POST">
        <?php foreach ($mapping as $groupTitle => $groupKeys): ?>
            <div class="trans-group">
                <div class="trans-group-header">
                    <span><i class="fas fa-folder-open" style="margin-right:10px;"></i> <?php echo $groupTitle; ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="trans-group-content">
                    <?php if ($groupTitle === 'Ana Sayfa - Neden Biz?'): ?>
                        <!-- Special Card Layout for Why Us Section -->
                        <div style="margin-bottom: 25px;">
                            <label style="font-weight: 600; color: #002B5B; margin-bottom: 10px; display: block;">
                                <i class="fas fa-heading"></i> Bölüm Başlığı
                            </label>
                            <div class="admin-form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                                <?php foreach ($langs as $lang): 
                                    $stmt = $pdo->prepare("SELECT `value` FROM translations WHERE lang_id = ? AND `key` = 'trust_title'");
                                    $stmt->execute([$lang['id']]);
                                    $val = $stmt->fetchColumn();
                                ?>
                                    <div>
                                        <span class="why-us-lang-label"><?php echo $lang['name']; ?></span>
                                        <input type="text" name="trans[<?php echo $lang['id']; ?>][trust_title]" class="why-us-input-title" value="<?php echo htmlspecialchars($val); ?>" placeholder="Bölüm başlığı">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <hr style="margin: 30px 0; border: none; border-top: 2px solid #e5e7eb;">
                        
                        <div class="why-us-cards-container">
                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                <div class="why-us-card">
                                    <div class="why-us-card-number"><?php echo $i; ?></div>
                                    <div class="why-us-card-header">
                                        <div class="why-us-card-title">
                                            <i class="fas fa-star" style="color: #f59e0b;"></i>
                                            Özellik Kartı <?php echo $i; ?>
                                        </div>
                                    </div>
                                    
                                    <?php foreach ($langs as $lang): 
                                        $title_key = "trust_card{$i}_title";
                                        $text_key = "trust_card{$i}_text";
                                        
                                        $stmt = $pdo->prepare("SELECT `value` FROM translations WHERE lang_id = ? AND `key` = ?");
                                        $stmt->execute([$lang['id'], $title_key]);
                                        $title_val = $stmt->fetchColumn();
                                        
                                        $stmt = $pdo->prepare("SELECT `value` FROM translations WHERE lang_id = ? AND `key` = ?");
                                        $stmt->execute([$lang['id'], $text_key]);
                                        $text_val = $stmt->fetchColumn();
                                    ?>
                                        <div class="why-us-lang-section">
                                            <span class="why-us-lang-label">
                                                <i class="fas fa-flag"></i> <?php echo $lang['name']; ?>
                                            </span>
                                            <input 
                                                type="text" 
                                                name="trans[<?php echo $lang['id']; ?>][<?php echo $title_key; ?>]" 
                                                class="why-us-input-title" 
                                                value="<?php echo htmlspecialchars($title_val); ?>" 
                                                placeholder="Başlık (<?php echo $lang['name']; ?>)">
                                            <textarea 
                                                name="trans[<?php echo $lang['id']; ?>][<?php echo $text_key; ?>]" 
                                                class="why-us-input-text" 
                                                placeholder="Açıklama metni (<?php echo $lang['name']; ?>)"><?php echo htmlspecialchars($text_val); ?></textarea>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php else: ?>
                        <!-- Standard Layout for Other Sections -->
                        <?php foreach ($groupKeys as $key => $label): ?>
                            <div class="trans-row" data-searchable="<?php echo strtolower($label . ' ' . $key); ?>">
                                <div>
                                    <span class="trans-label"><?php echo $label; ?></span>
                                    <span class="trans-key"><?php echo $key; ?></span>
                                </div>
                                <?php foreach ($langs as $lang): 
                                    $stmt = $pdo->prepare("SELECT `value` FROM translations WHERE lang_id = ? AND `key` = ?");
                                    $stmt->execute([$lang['id'], $key]);
                                    $val = $stmt->fetchColumn();
                                ?>
                                    <div>
                                        <span class="lang-label"><?php echo $lang['name']; ?></span>
                                        <textarea name="trans[<?php echo $lang['id']; ?>][<?php echo $key; ?>]" class="trans-input"><?php echo htmlspecialchars($val); ?></textarea>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="sticky-actions">
            <span style="float: left; color: #666; font-size: 14px; line-height: 45px;">* Değişikliklerin kaydedilmesi için "Kaydet" butonuna basmayı unutmayın.</span>
            <button type="submit" name="save_translation" class="btn btn-primary-admin" style="padding: 12px 40px; font-weight: 600;">
                <i class="fas fa-save" style="margin-right:8px;"></i> Değişiklikleri Kaydet
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('transSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.trans-row').forEach(row => {
            const content = row.getAttribute('data-searchable');
            if (content.includes(term)) {
                row.style.display = 'grid';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Hide empty groups
        document.querySelectorAll('.trans-group').forEach(group => {
            const hasVisible = Array.from(group.querySelectorAll('.trans-row')).some(row => row.style.display !== 'none');
            group.style.display = hasVisible ? 'block' : 'none';
        });
    });

    // Collapsible logic
    document.querySelectorAll('.trans-group-header').forEach(header => {
        header.addEventListener('click', () => {
            const content = header.nextElementSibling;
            const isVisible = content.style.display === 'block';
            content.style.display = isVisible ? 'none' : 'block';
            header.querySelector('.fa-chevron-down').style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
        });
    });
</script>

<?php include 'footer.php'; ?>
