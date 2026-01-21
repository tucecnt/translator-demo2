<?php include 'includes/head.php'; ?>

<main style="background: #f8fafc;">
    <style>
        @media (max-width: 768px) {
            .about-grid {
                grid-template-columns: 1fr !important;
                gap: 30px !important;
                text-align: center;
            }
            .about-image {
                max-width: 300px;
                margin: 0 auto;
            }
            .expertise-list {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <!-- Page Header -->
    <section class="page-header" style="background: linear-gradient(135deg, var(--primary-color) 0%, rgba(var(--primary-rgb), 0.8) 100%); padding: 80px 0; position: relative; overflow: hidden; text-align: center;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.05; background-image: radial-gradient(circle, var(--white) 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <span style="font-size: 13px; font-weight: 700; color: var(--accent-color); text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 15px;"><?php echo __('about_label', 'YEMİNLİ TERCÜMAN'); ?></span>
            <h1 style="font-size: 42px; color: var(--white); font-weight: 800; margin-bottom: 20px;"><?php echo __('about_title', 'Hakkımda'); ?></h1>
            <div style="width: 80px; height: 4px; background: var(--accent-color); margin: 0 auto; border-radius: 2px;"></div>
        </div>
    </section>

    <section class="section" style="padding: 100px 0;">
        <div class="container">
            
            <div class="about-grid" style="display: grid; grid-template-columns: 1fr 2fr; gap: 50px; align-items: center;">
                <div class="about-image">
                    <?php
                    // Fetch dynamic image
                    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'about_image'");
                    $stmt->execute();
                    $about_img = $stmt->fetchColumn();
                    if (!$about_img) {
                        $about_img = "assets/img/profile.jpg";
                    }
                    ?>
                    <img src="<?php echo htmlspecialchars($about_img); ?>" alt="Sefa Kaya" style="width: 100%; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                </div>
                <div class="about-text">
                    <h3>Sefa Kaya</h3>
                    <p style="margin-bottom: 20px; font-size: 18px; color: var(--primary-color); font-weight: 500;"><?php echo __('about_tagline', 'Yeminli Tercüman & Dil Uzmanı'); ?></p>
                    <p style="margin-bottom: 15px;"><?php echo __('about_p1', 'Profesyonel kariyerim boyunca, hukuki ve resmi belgelerin dil bariyerlerini aşarak uluslararası geçerlilik kazanması konusunda uzmanlaştım. Noter huzurunda yeminli tercüman olarak, her çevirinin doğruluğunu ve resmiyetini garanti altına alıyorum.'); ?></p>
                    <p style="margin-bottom: 15px;"><?php echo __('about_p2', 'Türkiye, Almanya ve İngilizce konuşulan ülkeler (ABD, İngiltere, Kanada vb.) arasındaki resmi süreçlerde, belgelerinizin sorunsuz kabul edilmesi için gerekli tüm teknik ve hukuki donanıma sahibim.'); ?></p>
                    
                    <div class="expertise-list" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 30px;">
                        <?php 
                        $stmt = $pdo->prepare("
                            SELECT t.title 
                            FROM specialties s 
                            JOIN specialty_translations t ON s.id = t.specialty_id 
                            WHERE s.is_active = 1 AND t.lang_id = ? 
                            ORDER BY s.sort_order ASC
                        ");
                        $stmt->execute([$langObj->getLangId()]);
                        $specialties = $stmt->fetchAll();
                        foreach ($specialties as $spec): 
                        ?>
                            <div class="exp-item" style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-check-circle" style="color: var(--primary-color);"></i>
                                <span><?php echo htmlspecialchars($spec['title']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="countries-served" style="margin-top: 40px; padding: 20px; background: var(--secondary-color); border-radius: 4px;">
                        <h4 style="margin-bottom: 10px; color: var(--primary-color);"><?php echo __('countries_title', 'Hizmet Verilen Ülkeler'); ?></h4>
                        <p><?php echo __('countries_list', 'Türkiye • Almanya • İngiltere • ABD • Kanada • Avusturya • İsviçre'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/foot.php'; ?>
