<?php include 'includes/head.php'; ?>

<main style="background: #f8fafc;">
    <!-- Page Header -->
    <section class="page-header" style="background: linear-gradient(135deg, var(--primary-color) 0%, rgba(var(--primary-rgb), 0.8) 100%); padding: 80px 0; position: relative; overflow: hidden; text-align: center;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.05; background-image: radial-gradient(circle, var(--white) 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <span style="font-size: 13px; font-weight: 700; color: var(--accent-color); text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 15px;"><?php echo __('services_label', 'KAYITLI & YEMİNLİ'); ?></span>
            <h1 style="font-size: 42px; color: var(--white); font-weight: 800; margin-bottom: 20px;"><?php echo __('services_page_title', 'Hizmetlerimiz'); ?></h1>
            <div style="width: 80px; height: 4px; background: var(--accent-color); margin: 0 auto; border-radius: 2px;"></div>
            <p style="max-width: 800px; margin: 25px auto 0; color: rgba(255,255,255,0.7); font-size: 16px; line-height: 1.6;"><?php echo __('services_desc', 'Resmi ve profesyonel ihtiyaçlarınız için geniş bir yelpazede yeminli tercüme hizmetleri sunuyoruz.'); ?></p>
        </div>
    </section>

    <section class="section" style="padding: 100px 0;">
        <div class="container">

            <!-- Dynamic Services Info Section -->
            <?php 
            $stmt = $pdo->prepare("
                SELECT s.icon, st.title, st.content 
                FROM services s 
                JOIN service_translations st ON s.id = st.service_id 
                WHERE st.lang_id = ? 
                ORDER BY s.sort_order ASC, s.id ASC
            ");
            $stmt->execute([$langObj->getLangId()]);
            $info_services = $stmt->fetchAll();
            
            if (!empty($info_services)):
            ?>
            <div class="services-info-section" style="max-width: 1000px; margin: 50px auto 60px; background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%); border-radius: 16px; padding: 45px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border-left: 4px solid var(--accent-color);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 35px;">
                    <?php foreach ($info_services as $info): ?>
                    <div class="info-item" style="display: flex; gap: 20px;">
                        <div style="flex-shrink: 0;">
                            <div style="width: 55px; height: 55px; border-radius: 12px; background: var(--accent-color); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(var(--accent-rgb), 0.3);">
                                <i class="<?php echo htmlspecialchars($info['icon']); ?>" style="font-size: 24px; color: var(--white);"></i>
                            </div>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 10px 0; color: var(--primary-color); font-size: 18px; font-weight: 700;"><?php echo htmlspecialchars($info['title']); ?></h4>
                            <p style="margin: 0; color: var(--text-color); font-size: 14px; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($info['content'])); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 35px; padding-top: 30px; border-top: 1px solid rgba(0,0,0,0.08); text-align: center;">
                    <div style="background: rgba(var(--accent-rgb), 0.05); color: var(--primary-color); padding: 15px 20px; border-radius: 8px; font-size: 14px; border-left: 4px solid var(--accent-color);">
                        <?php echo __('privacy_notice', '<strong>Önemli:</strong> Tüm belgeleriniz gizlilik prensiplerine uygun olarak işlenir. Yeminli tercüman olarak, mesleki etik kurallarına ve veri gizliliğine tam uyum sağlarız.'); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Detailed Document Types -->
            <div class="document-types-section" style="margin-top: 100px;">
                <div style="text-align: center; margin-bottom: 60px;">
                    <span style="font-size: 14px; font-weight: 700; color: var(--accent-color); text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 10px;"><?php echo __('doc_types_subtitle', 'UZMANLIK ALANLARIMIZ'); ?></span>
                    <h3 style="font-size: 36px; color: var(--primary-color); font-weight: 800; margin: 0;"><?php echo __('doc_types_title', 'Çevirisini Yaptığımız Belge Türleri'); ?></h3>
                </div>
                
                <?php 
                // Fetch all document categories grouped by sort_order
                $stmt = $pdo->prepare("
                    SELECT dc.id, dc.icon, dc.sort_order, dct.title 
                    FROM document_categories dc 
                    JOIN document_category_translations dct ON dc.id = dct.category_id 
                    WHERE dct.lang_id = ? 
                    ORDER BY dc.sort_order ASC, dc.id ASC
                ");
                $stmt->execute([$langObj->getLangId()]);
                $all_docs = $stmt->fetchAll();
                
                // Group by sort_order
                $grouped = [];
                // Fixed icons for categories, but using uniform styling
                $category_icons = [
                    1 => ['title' => __('doc_cat_academic', 'Akademik Belgeler'), 'icon' => 'fas fa-graduation-cap'],
                    2 => ['title' => __('doc_cat_business', 'İş Belgeleri'), 'icon' => 'fas fa-briefcase'],
                    3 => ['title' => __('doc_cat_personal', 'Kişisel Belgeler'), 'icon' => 'fas fa-user'],
                    4 => ['title' => __('doc_cat_official', 'Resmi Belgeler'), 'icon' => 'fas fa-file-signature'],
                    5 => ['title' => __('doc_cat_medical', 'Tıbbi Belgeler'), 'icon' => 'fas fa-heartbeat'],
                    6 => ['title' => __('doc_cat_legal', 'Yasal Belgeler'), 'icon' => 'fas fa-balance-scale']
                ];
                
                foreach ($all_docs as $doc) {
                    $grouped[$doc['sort_order']][] = $doc;
                }
                ?>
                
                <div class="doc-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;">
                    <?php foreach ($grouped as $order => $docs): 
                        $info = $category_icons[$order] ?? ['title' => 'Diğer Belgeler', 'icon' => 'fas fa-folder'];
                    ?>
                    <div class="doc-card" style="background: var(--white); border-radius: 12px; padding: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.04); border-top: 3px solid var(--accent-color); transition: transform 0.3s ease;">
                        <div class="doc-card-header" style="display: flex; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--light-gray);">
                            <div class="doc-icon-wrapper" style="width: 45px; height: 45px; border-radius: 50%; background: rgba(var(--accent-rgb), 0.1); color: var(--accent-color); display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 15px;">
                                <i class="<?php echo $info['icon']; ?>"></i>
                            </div>
                            <h4 style="font-size: 19px; font-weight: 700; color: var(--primary-color); margin: 0;"><?php echo $info['title']; ?></h4>
                        </div>
                        
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <?php foreach ($docs as $doc): ?>
                            <li style="margin-bottom: 10px; display: flex; align-items: flex-start; color: var(--text-color); font-size: 15px;">
                                <span style="color: var(--accent-color); font-size: 14px; margin-right: 10px; margin-top: 3px;">
                                    <i class="fas fa-check"></i>
                                </span>
                                <?php echo htmlspecialchars($doc['title']); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/foot.php'; ?>
