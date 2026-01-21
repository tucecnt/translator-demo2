<?php
include 'includes/head.php';

// Fetch FAQs
$stmt = $pdo->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY sort_order ASC, id DESC");
$faqs = $stmt->fetchAll();
?>

<main style="background: #f8fafc; padding-bottom: 80px;">
    <!-- Hero Section -->
    <section class="page-hero" style="background: linear-gradient(135deg, var(--primary-color) 0%, rgba(var(--primary-rgb), 0.8) 100%); padding: 100px 0; position: relative; overflow: hidden; text-align: center;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.05; background-image: radial-gradient(circle, var(--white) 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <span style="font-size: 13px; font-weight: 700; color: var(--accent-color); text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 15px;"><?php echo ($currentLang == 'tr') ? 'MERAK EDİLENLER' : (($currentLang == 'en') ? 'FAQ' : (($currentLang == 'de') ? 'HÄUFIG GESTELLTE FRAGEN' : 'ЧАСТО ЗАДАВАЕМЫЕ ВОПРОСЫ')); ?></span>
            <h1 style="font-size: 48px; color: var(--white); font-weight: 800; margin-bottom: 25px;"><?php echo ($currentLang == 'tr') ? 'Sıkça Sorulan Sorular' : (($currentLang == 'en') ? 'Frequently Asked Questions' : (($currentLang == 'de') ? 'Häufig gestellte Fragen' : 'Часто задаваемые вопросы')); ?></h1>
            <div style="width: 80px; height: 4px; background: var(--accent-color); margin: 0 auto; border-radius: 2px;"></div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section" style="padding: 100px 0;">
        <div class="container">
            <div class="faq-container">
                <?php if (empty($faqs)): ?>
                    <div class="text-center">
                        <p><?php echo ($currentLang == 'tr') ? 'Henüz içerik eklenmemiş.' : 'No content added yet.'; ?></p>
                    </div>
                <?php else: ?>
                    <div class="accordion">
                        <?php foreach ($faqs as $index => $f): 
                            $stmt = $pdo->prepare("SELECT question, answer FROM faq_translations WHERE faq_id = ? AND lang_id = (SELECT id FROM languages WHERE code = ?)");
                            $stmt->execute([$f['id'], $currentLang]);
                            $trans = $stmt->fetch();
                            
                            if (!$trans) {
                                $stmt = $pdo->prepare("SELECT question, answer FROM faq_translations WHERE faq_id = ? AND lang_id = 1");
                                $stmt->execute([$f['id']]);
                                $trans = $stmt->fetch();
                            }
                        ?>
                        <div class="accordion-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="accordion-header">
                                <h3><?php echo htmlspecialchars($trans['question']); ?></h3>
                                <div class="accordion-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </div>
                            <div class="accordion-content">
                                <div class="content-inner">
                                    <p><?php echo nl2br(htmlspecialchars($trans['answer'])); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action Section (Same as surec.php) -->
    <?php
    $cta_title = 'Hala Sorunuz mu Var?';
    $cta_desc = 'Aradığınız cevabı bulamadıysanız bize her zaman ulaşabilirsiniz.';
    $cta_btn_text = 'BİZE ULAŞIN';

    if ($currentLang == 'en') {
        $cta_title = 'Still Have Questions?';
        $cta_desc = "If you couldn't find the answer you're looking for, you can always reach us.";
        $cta_btn_text = 'CONTACT US';
    } elseif ($currentLang == 'de') {
        $cta_title = 'Haben Sie noch Fragen?';
        $cta_desc = 'Wenn Sie die gesuchte Antwort nicht finden konnten, können Sie uns jederzeit kontaktieren.';
        $cta_btn_text = 'KONTAKTIEREN SIE UNS';
    } elseif ($currentLang == 'ru') {
        $cta_title = 'Остались вопросы?';
        $cta_desc = 'Если вы не нашли нужный ответ, вы всегда можете связаться с нами.';
        $cta_btn_text = 'СВЯЗАТЬСЯ С НАМИ';
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
    .faq-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .accordion {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .accordion-item {
        background: var(--white);
        border-radius: 12px;
        border: 1px solid var(--light-gray);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .accordion-item:hover {
        border-color: var(--accent-color);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .accordion-item.active {
        border-color: var(--accent-color);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    .accordion-header {
        padding: 24px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        user-select: none;
    }

    .accordion-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-color);
        line-height: 1.4;
    }

    .accordion-icon {
        width: 32px;
        height: 32px;
        background: var(--light-gray);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-color);
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .accordion-item.active .accordion-icon {
        transform: rotate(45deg);
        background: var(--accent-color);
        color: var(--white);
    }

    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0, 1, 0, 1);
    }

    .accordion-item.active .accordion-content {
        max-height: 1000px;
        transition: all 0.5s cubic-bezier(1, 0, 1, 0);
    }

    .content-inner {
        padding: 0 40px 40px;
        color: #64748b;
        line-height: 1.8;
        font-size: 16px;
    }

    .btn-premium:hover {
        background: var(--accent-color);
        opacity: 0.9;
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(var(--accent-rgb), 0.5);
    }

    @media (max-width: 768px) {
        .accordion-header {
            padding: 20px 25px;
        }
        .accordion-header h3 {
            font-size: 16px;
        }
        .content-inner {
            padding: 0 25px 25px;
            font-size: 15px;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const accordionItems = document.querySelectorAll('.accordion-item');
        
        accordionItems.forEach(item => {
            const header = item.querySelector('.accordion-header');
            header.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                
                accordionItems.forEach(otherItem => {
                    otherItem.classList.remove('active');
                });
                
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    });
    </script>
</main>

<?php include 'includes/foot.php'; ?>
