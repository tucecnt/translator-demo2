<?php include 'includes/head.php'; 

if ($show_references == '0') {
    header('Location: index.php');
    exit;
}
?>

<main style="background: #f8fafc;">
    <!-- Page Header -->
    <section class="page-header" style="background: linear-gradient(135deg, var(--primary-color) 0%, rgba(var(--primary-rgb), 0.8) 100%); padding: 80px 0; position: relative; overflow: hidden; text-align: center;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.05; background-image: radial-gradient(circle, var(--white) 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <span style="font-size: 13px; font-weight: 700; color: var(--accent-color); text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 15px;"><?php echo __('references_label', 'GÜVEN VE KALİTE'); ?></span>
            <h1 style="font-size: 42px; color: var(--white); font-weight: 800; margin-bottom: 20px;"><?php echo __('references_title', 'Referanslarımız'); ?></h1>
            <div style="width: 80px; height: 4px; background: var(--accent-color); margin: 0 auto; border-radius: 2px;"></div>
            <p style="max-width: 700px; margin: 25px auto 0; color: rgba(255,255,255,0.7); font-size: 16px; line-height: 1.6;"><?php echo __('references_subtitle', 'Güvenle hizmet verdiğimiz kurumlar ve tamamladığımız uluslararası projelerden bazıları.'); ?></p>
        </div>
    </section>

    <section class="section" style="padding: 100px 0;">
        <div class="container">
            <div class="references-grid-premium" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                <?php 
                $stmt = $pdo->prepare("
                    SELECT r.id, r.image, rt.title, rt.description 
                    FROM `references` r
                    JOIN reference_translations rt ON r.id = rt.reference_id 
                    WHERE rt.lang_id = ? 
                    ORDER BY r.sort_order ASC, r.id DESC
                ");
                $stmt->execute([$langObj->getLangId()]);
                $references = $stmt->fetchAll();
                
                if (!empty($references)):
                    foreach ($references as $index => $ref):
                ?>
                <div class="ref-card-premium" style="
                    background: var(--white);
                    border-radius: 20px;
                    padding: 40px 30px;
                    text-align: center;
                    box-shadow: 0 10px 30px rgba(var(--primary-rgb), 0.04);
                    border: 1px solid rgba(var(--primary-rgb), 0.05);
                    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                    position: relative;
                    animation: fadeInUp 0.6s ease-out <?php echo $index * 0.1; ?>s both;
                ">
                    <div class="ref-logo-wrap" style="
                        width: 140px;
                        height: 140px;
                        margin: 0 auto 25px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background: var(--light-gray);
                        border-radius: 50%;
                        transition: all 0.4s;
                        position: relative;
                        padding: 20px;
                    ">
                        <?php if($ref['image']): ?>
                            <img src="<?php echo htmlspecialchars($ref['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($ref['title']); ?>" 
                                 style="max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; filter: grayscale(100%); opacity: 0.7; transition: all 0.4s;">
                        <?php else: ?>
                            <i class="fas fa-building" style="font-size: 40px; color: var(--accent-color); opacity: 0.5;"></i>
                        <?php endif; ?>
                    </div>

                    <h3 style="font-size: 20px; color: var(--primary-color); font-weight: 700; margin-bottom: 12px; transition: color 0.3s;">
                        <?php echo htmlspecialchars($ref['title']); ?>
                    </h3>
                    
                    <?php if($ref['description']): ?>
                        <p style="font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 0;">
                            <?php echo htmlspecialchars($ref['description']); ?>
                        </p>
                    <?php endif; ?>

                    <!-- Bottom Accent Line -->
                    <div class="accent-line" style="
                        width: 0;
                        height: 3px;
                        background: var(--accent-color);
                        position: absolute;
                        bottom: 0;
                        left: 50%;
                        transform: translateX(-50%);
                        transition: width 0.4s;
                        border-radius: 3px 3px 0 0;
                    "></div>
                </div>
                <?php 
                    endforeach;
                else:
                ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 100px 20px; background: var(--white); border-radius: 20px; color: var(--text-color); border: 2px dashed var(--light-gray);">
                    <i class="fas fa-briefcase" style="font-size: 60px; color: var(--light-gray); margin-bottom: 25px;"></i>
                    <p style="font-size: 18px; font-weight: 500;">Henüz referans eklenmemiş.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="confidentiality-note" style="
                margin-top: 80px; 
                text-align: center; 
                padding: 30px; 
                background: var(--white); 
                border-radius: 16px; 
                border-left: 5px solid var(--accent-color);
                box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            ">
                <p style="margin: 0; color: #64748b; font-size: 15px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="fas fa-shield-alt" style="color: var(--accent-color); font-size: 20px;"></i>
                    <strong><?php echo __('note', 'Not:'); ?></strong> <?php echo __('ref_confidentiality', 'Gizlilik kuralları gereği bireysel referanslarımızı burada paylaşmıyoruz.'); ?>
                </p>
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

        .ref-card-premium:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(var(--primary-rgb), 0.08);
            border-color: rgba(var(--accent-rgb), 0.2);
        }

        .ref-card-premium:hover .ref-logo-wrap {
            background: var(--white);
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(var(--accent-rgb), 0.15);
        }

        .ref-card-premium:hover img {
            filter: grayscale(0%);
            opacity: 1;
            transform: scale(1.1);
        }

        .ref-card-premium:hover .accent-line {
            width: 80px;
        }

        .ref-card-premium:hover h3 {
            color: var(--accent-color);
        }
    </style>
</main>

<?php include 'includes/foot.php'; ?>
