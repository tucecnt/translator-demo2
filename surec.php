<?php include 'includes/head.php'; ?>

<main style="background: #f8fafc; padding-bottom: 80px;">
    <!-- Page Header -->
    <section class="page-header" style="background: linear-gradient(135deg, var(--primary-color) 0%, rgba(var(--primary-rgb), 0.8) 100%); padding: 100px 0; position: relative; overflow: hidden; text-align: center;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.05; background-image: radial-gradient(circle, var(--white) 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <span style="font-size: 13px; font-weight: 700; color: var(--accent-color); text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 15px;"><?php echo __('process_label', 'PROFESYONEL İŞ AKIŞI'); ?></span>
            <h1 style="font-size: 48px; color: var(--white); font-weight: 800; margin-bottom: 25px;"><?php echo __('process_title', 'Çeviri Sürecimiz'); ?></h1>
            <div style="width: 80px; height: 4px; background: var(--accent-color); margin: 0 auto; border-radius: 2px;"></div>
            <p style="max-width: 800px; margin: 30px auto 0; color: rgba(255,255,255,0.7); font-size: 17px; line-height: 1.7;"><?php echo __('process_subtitle', 'Belgelerinizin güvenliği ve doğruluğu için her adımda titizlikle uyguladığımız 5 aşamalı profesyonel iş akışımız.'); ?></p>
        </div>
    </section>

    <!-- Process Steps Grid -->
    <section class="section" style="padding: 100px 0;">
        <div class="container">
            <div class="process-grid" style="
                display: grid; 
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
                gap: 30px; 
                justify-content: center;
            ">
                <?php 
                $steps = [
                    [
                        'id' => 1,
                        'icon' => 'fas fa-file-upload',
                        'title' => __('process_step1_title', 'Belge İletimi'),
                        'text' => __('process_step1_text', 'Belgenizi dijital veya elden ulaştırırsınız.')
                    ],
                    [
                        'id' => 2,
                        'icon' => 'fas fa-search-dollar',
                        'title' => __('process_step2_title', 'Teklif Sunumu'),
                        'text' => __('process_step2_text', 'Hızlı fiyat ve süre değerlendirmesi yapılır.')
                    ],
                    [
                        'id' => 3,
                        'icon' => 'fas fa-feather-alt',
                        'title' => __('process_step3_title', 'Profesyonel Çeviri'),
                        'text' => __('process_step3_text', 'Yeminli tercüman tarafından çeviri yapılır.')
                    ],
                    [
                        'id' => 4,
                        'icon' => 'fas fa-user-check',
                        'title' => __('process_step4_title', 'Kontrol & Tasdik'),
                        'text' => __('process_step4_text', 'Editör kontrolü ve resmi onaylar tamamlanır.')
                    ],
                    [
                        'id' => 5,
                        'icon' => 'fas fa-shipping-fast',
                        'title' => __('process_step5_title', 'Güvenli Teslimat'),
                        'text' => __('process_step5_text', 'Belgeniz dilediğiniz şekilde teslim edilir.')
                    ]
                ];

                foreach ($steps as $index => $step):
                ?>
                <div class="process-card" style="
                    background: var(--white); 
                    padding: 40px 25px; 
                    border-radius: 20px; 
                    box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
                    text-align: center;
                    border: 1px solid var(--light-gray);
                    transition: all 0.4s ease;
                    position: relative;
                    animation: fadeInUp 0.6s ease-out <?php echo $index * 0.1; ?>s both;
                ">
                    <!-- Step Number Badge -->
                    <div style="
                        position: absolute;
                        top: -15px;
                        left: 50%;
                        transform: translateX(-50%);
                        background: var(--accent-color);
                        color: var(--white);
                        padding: 5px 15px;
                        border-radius: 20px;
                        font-size: 12px;
                        font-weight: 800;
                        box-shadow: 0 5px 15px rgba(var(--accent-rgb), 0.3);
                    ">
                        <?php echo $step['id']; ?>. ADIM
                    </div>

                    <!-- Icon -->
                    <div style="
                        width: 70px; 
                        height: 70px; 
                        background: var(--light-gray); 
                        border-radius: 50%; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center; 
                        margin: 0 auto 25px;
                        color: var(--accent-color);
                        font-size: 28px;
                        transition: all 0.3s;
                    ">
                        <i class="<?php echo $step['icon']; ?>"></i>
                    </div>

                    <h4 style="font-size: 19px; color: var(--primary-color); font-weight: 700; margin-bottom: 15px;"><?php echo $step['title']; ?></h4>
                    <p style="font-size: 14px; color: var(--text-color); line-height: 1.5; margin-bottom: 0;"><?php echo $step['text']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <?php
    // Static translations for CTA based on language
    $cta_title = 'Belgeleriniz İçin Hemen Teklif Alın';
    $cta_desc = 'Uzman çeviri hizmetlerimizle tanışmak ve sürecimizi deneyimlemek için bizimle iletişime geçin.';
    $cta_btn_text = 'HEMEN TEKLİF İSTEYİN';

    if ($currentLang == 'en') {
        $cta_title = 'Get a Fast Quote for Your Documents';
        $cta_desc = 'Contact us now to discover our expert translation services and experience our process.';
        $cta_btn_text = 'GET A QUOTE NOW';
    } elseif ($currentLang == 'de') {
        $cta_title = 'Holen Sie sich jetzt ein Angebot für Ihre Dokumente';
        $cta_desc = 'Kontaktieren Sie uns, um unsere Experten-Übersetzungsdienste kennenzulernen.';
        $cta_btn_text = 'JETZT ANGEBOT ANFORDERN';
    } elseif ($currentLang == 'ru') {
        $cta_title = 'Получите расчет стоимости перевода ваших документов';
        $cta_desc = 'Свяжитесь с нами, чтобы узнать о наших экспертных услугах по переводу.';
        $cta_btn_text = 'ПОЛУЧИТЬ РАСЧЕТ';
    }
    ?>
    <section class="section cta-section" style="padding-bottom: 100px;">
        <div class="container">
            <div style="
                background: linear-gradient(135deg, var(--primary-color) 0%, rgba(var(--primary-rgb), 0.8) 100%);
                border-radius: 30px;
                padding: 60px 40px;
                text-align: center;
                position: relative;
                overflow: hidden;
                box-shadow: 0 20px 50px rgba(var(--primary-rgb), 0.2);
            ">
                <!-- Background Pattern -->
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.05; background-image: radial-gradient(circle, var(--white) 1px, transparent 1px); background-size: 20px 20px;"></div>
                
                <h2 style="color: var(--white); font-size: 32px; font-weight: 800; margin-bottom: 20px; position: relative;"><?php echo $cta_title; ?></h2>
                <p style="color: rgba(255,255,255,0.7); max-width: 600px; margin: 0 auto 40px; font-size: 17px; position: relative;"><?php echo $cta_desc; ?></p>
                
                <a href="iletisim.php" class="btn-premium" style="
                    display: inline-block;
                    background: var(--accent-color);
                    color: var(--white);
                    padding: 18px 45px;
                    border-radius: 50px;
                    font-weight: 700;
                    text-decoration: none;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    transition: all 0.3s;
                    box-shadow: 0 10px 25px rgba(var(--accent-rgb), 0.4);
                    position: relative;
                ">
                    <?php echo $cta_btn_text; ?>
                </a>
            </div>
        </div>
    </section>

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

        .process-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.06);
            border-color: var(--accent-color);
        }

        .process-card:hover div[style*="background: var(--light-gray)"] {
            background: var(--accent-color) !important;
            color: var(--white) !important;
        }

        .btn-premium:hover {
            background: var(--accent-color);
            opacity: 0.9;
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(var(--accent-rgb), 0.5);
        }

        @media (max-width: 991px) {
            .process-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        @media (max-width: 600px) {
            .process-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</main>

<?php include 'includes/foot.php'; ?>
