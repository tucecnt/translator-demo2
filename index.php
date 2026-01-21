<?php include 'includes/head.php'; ?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1><?php echo __('hero_title', 'Profesyonel Yeminli Tercüme'); ?></h1>
            <p><?php echo __('hero_subtitle', 'Sefa Kaya ile Türkiye, Almanya ve İngilizce konuşulan ülkeler arasında hukuki, resmi ve ticari belgeleriniz için güvenilir çeviri çözümleri.'); ?></p>
            <a href="iletisim.php" class="btn"><?php echo __('hero_btn', 'Teklif Alın'); ?></a>
        </div>
    </section>

    <!-- Trust Highlights -->
    <section class="section trust-highlights" style="background: linear-gradient(to bottom, var(--white), var(--light-gray)); padding: 100px 0;">
        <div class="container">
            <div class="section-title" style="margin-bottom: 70px;">
                <span style="font-size: 13px; font-weight: 700; color: var(--accent-color); text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 15px;"><?php echo __('trust_label', 'NEDEN BİZİ SEÇMELİSİNİZ?'); ?></span>
                <h2 style="font-size: 38px; color: var(--primary-color); font-weight: 800;"><?php echo __('trust_title', 'Kalite ve Güvenin Adresi'); ?></h2>
            </div>
            
            <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 35px;">
                <!-- Card 1 -->
                <div class="trust-card" style="position: relative; padding: 45px 35px; background: var(--white); border-radius: 4px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); transition: all 0.3s ease; border-bottom: 3px solid transparent;">
                    <div style="width: 70px; height: 70px; background: rgba(var(--accent-rgb), 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 30px; transition: 0.3s;">
                        <i class="fas fa-stamp" style="font-size: 28px; color: var(--accent-color);"></i>
                    </div>
                    <h3 style="font-size: 22px; margin-bottom: 15px; color: var(--primary-color); font-weight: 700;"><?php echo __('trust_card1_title', 'Noter Tasdikli'); ?></h3>
                    <p style="color: var(--text-color); line-height: 1.7; font-size: 15px;"><?php echo __('trust_card1_text', 'Resmi makamlarca kabul edilen, yeminli ve noter onaylı profesyonel çeviri hizmeti sunuyoruz. Belgeleriniz güvende.'); ?></p>
                </div>

                <!-- Card 2 -->
                <div class="trust-card" style="position: relative; padding: 45px 35px; background: var(--white); border-radius: 4px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); transition: all 0.3s ease; border-bottom: 3px solid transparent;">
                     <div style="width: 70px; height: 70px; background: rgba(var(--accent-rgb), 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 30px; transition: 0.3s;">
                        <i class="fas fa-balance-scale" style="font-size: 28px; color: var(--accent-color);"></i>
                    </div>
                    <h3 style="font-size: 22px; margin-bottom: 15px; color: var(--primary-color); font-weight: 700;"><?php echo __('trust_card2_title', 'Hukuki Uzmanlık'); ?></h3>
                    <p style="color: var(--text-color); line-height: 1.7; font-size: 15px;"><?php echo __('trust_card2_text', 'Hukuk terminolojisine hakim uzman kadromuzla, en karmaşık yasal metinlerde bile %100 doğruluk garantisi.'); ?></p>
                </div>

                <!-- Card 3 -->
                <div class="trust-card" style="position: relative; padding: 45px 35px; background: var(--white); border-radius: 4px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); transition: all 0.3s ease; border-bottom: 3px solid transparent;">
                     <div style="width: 70px; height: 70px; background: rgba(var(--accent-rgb), 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 30px; transition: 0.3s;">
                        <i class="fas fa-globe" style="font-size: 28px; color: var(--accent-color);"></i>
                    </div>
                    <h3 style="font-size: 22px; margin-bottom: 15px; color: var(--primary-color); font-weight: 700;"><?php echo __('trust_card3_title', 'Uluslararası Geçerlilik'); ?></h3>
                    <p style="color: var(--text-color); line-height: 1.7; font-size: 15px;"><?php echo __('trust_card3_text', 'Türkiye, Almanya, İngiltere ve ABD başta olmak üzere, çevirilerimiz tüm dünyada resmi makamlarca tanınır.'); ?></p>
                </div>
            </div>
            
            <style>
            .trust-card:hover { 
                transform: translateY(-10px); 
                box-shadow: 0 20px 50px rgba(0,0,0,0.1) !important;
                border-bottom-color: var(--accent-color) !important;
            }
            .trust-card:hover div {
                background: var(--accent-color) !important;
            }
            .trust-card:hover i {
                color: var(--white) !important;
            }
            </style>
        </div>
    </section>

    <!-- Brief Services -->
    <section class="section services-brief" style="background: linear-gradient(135deg, var(--primary-color) 0%, rgba(var(--primary-rgb), 0.8) 100%); padding: 100px 0; position: relative; overflow: hidden;">
        <!-- Background Pattern -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.03; background-image: radial-gradient(circle, var(--white) 1px, transparent 1px); background-size: 30px 30px;"></div>
        
        <div class="container" style="position: relative; z-index: 1;">
            <div class="section-title" style="margin-bottom: 70px; text-align: center;">
                <span style="font-size: 13px; font-weight: 700; color: var(--accent-color); text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 15px;"><?php echo __('services_label_top', 'UZMANLIK ALANLARIMIZ'); ?></span>
                <h2 style="font-size: 42px; color: var(--white); font-weight: 800; margin-bottom: 20px;"><?php echo __('services_title', 'Hizmet Alanlarımız'); ?></h2>
                <div style="width: 80px; height: 4px; background: var(--accent-color); margin: 0 auto; border-radius: 2px;"></div>
            </div>
            
            <div class="services-grid-modern" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; margin-bottom: 50px;">
                <?php 
                $stmt = $pdo->prepare("
                    SELECT s.icon, st.title 
                    FROM services s 
                    JOIN service_translations st ON s.id = st.service_id 
                    WHERE st.lang_id = ? 
                    ORDER BY s.sort_order ASC, s.id DESC 
                    LIMIT 8
                ");
                $stmt->execute([$langObj->getLangId()]);
                $services = $stmt->fetchAll();
                foreach ($services as $index => $s):
                ?>
                <div class="service-card-modern" style="
                    background: rgba(255, 255, 255, 0.05);
                    backdrop-filter: blur(10px);
                    border: 1px solid rgba(255, 255, 255, 0.1);
                    border-radius: 16px;
                    padding: 35px 30px;
                    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                    position: relative;
                    overflow: hidden;
                    cursor: pointer;
                    animation: fadeInUp 0.6s ease-out <?php echo $index * 0.1; ?>s both;
                    text-align: center;
                ">
                    <!-- Gradient Overlay on Hover -->
                    <div class="card-gradient" style="
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: linear-gradient(135deg, rgba(var(--accent-rgb), 0.2) 0%, rgba(var(--accent-rgb), 0) 100%);
                        opacity: 0;
                        transition: opacity 0.4s;
                        pointer-events: none;
                    "></div>
                    
                    <!-- Icon Container -->
                    <div style="
                        width: 70px;
                        height: 70px;
                        background: var(--accent-color);
                        border-radius: 16px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 25px;
                        box-shadow: 0 8px 20px rgba(var(--accent-rgb), 0.3);
                        transition: all 0.4s;
                        position: relative;
                        z-index: 1;
                    ">
                        <i class="<?php echo htmlspecialchars($s['icon']); ?>" style="font-size: 28px; color: var(--white); transition: transform 0.4s;"></i>
                    </div>
                    
                    <!-- Title -->
                    <h4 style="
                        margin: 0;
                        font-size: 17px;
                        font-weight: 700;
                        color: var(--white);
                        line-height: 1.4;
                        position: relative;
                        z-index: 1;
                        transition: color 0.3s;
                    "><?php echo htmlspecialchars($s['title']); ?></h4>
                    
                    <!-- Decorative Line -->
                    <div class="service-line" style="
                        width: 0;
                        height: 3px;
                        background: var(--accent-color);
                        margin: 20px auto 0;
                        transition: width 0.4s;
                        border-radius: 2px;
                    "></div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center; margin-top: 50px;">
                <a href="hizmetler.php" class="btn-services-all" style="
                    display: inline-flex;
                    align-items: center;
                    gap: 12px;
                    background: var(--accent-color);
                    color: var(--white);
                    border: none;
                    padding: 16px 40px;
                    font-size: 16px;
                    font-weight: 700;
                    border-radius: 50px;
                    text-decoration: none;
                    transition: all 0.3s;
                    box-shadow: 0 8px 25px rgba(var(--accent-rgb), 0.4);
                ">
                    <?php echo __('view_all_services', 'Tüm Hizmetleri Gör'); ?>
                    <i class="fas fa-arrow-right" style="transition: transform 0.3s;"></i>
                </a>
            </div>
        </div>
        
        <style>
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .service-card-modern:hover {
                transform: translateY(-8px);
                background: rgba(255, 255, 255, 0.08);
                border-color: var(--accent-color);
                box-shadow: 0 20px 50px rgba(var(--accent-rgb), 0.3);
            }
            
            .service-card-modern:hover .card-gradient {
                opacity: 1;
            }
            
            .service-card-modern:hover .service-line {
                width: 60px;
            }
            
            .service-card-modern:hover i {
                transform: scale(1.1) rotate(5deg);
            }
            
            .btn-services-all:hover {
                background: var(--accent-color);
                opacity: 0.9;
                transform: translateY(-3px);
                box-shadow: 0 12px 35px rgba(var(--accent-rgb), 0.5);
            }
            
            .btn-services-all:hover i {
                transform: translateX(5px);
            }
            
            /* Responsive Grid */
            @media (max-width: 1200px) {
                .services-grid-modern {
                    grid-template-columns: repeat(3, 1fr) !important;
                }
            }
            
            @media (max-width: 900px) {
                .services-grid-modern {
                    grid-template-columns: repeat(2, 1fr) !important;
                }
            }
            
            @media (max-width: 600px) {
                .services-grid-modern {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>
    </section>
</main>

<?php include 'includes/foot.php'; ?>
