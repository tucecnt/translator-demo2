<?php 
include 'includes/head.php'; 

// Fetch contact information from settings
$contactInfo = [];
$contactKeys = ['contact_phone', 'contact_email', 'contact_address', 'contact_whatsapp', 'contact_working_hours'];
foreach ($contactKeys as $key) {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $contactInfo[$key] = $stmt->fetchColumn() ?: '';
}

// Format WhatsApp number (remove spaces and special characters)
$whatsappNumber = preg_replace('/[^0-9]/', '', $contactInfo['contact_whatsapp']);
?>

<main style="background: #f8fafc;">
    <!-- Page Header -->
    <section class="page-header" style="background: linear-gradient(135deg, var(--primary-color) 0%, rgba(var(--primary-rgb), 0.8) 100%); padding: 80px 0; position: relative; overflow: hidden; text-align: center;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.05; background-image: radial-gradient(circle, var(--white) 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <span style="font-size: 13px; font-weight: 700; color: var(--accent-color); text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 15px;"><?php echo __('contact_label', 'BİZE ULAŞIN'); ?></span>
            <h1 style="font-size: 42px; color: var(--white); font-weight: 800; margin-bottom: 20px;"><?php echo __('contact_title', 'İletişim'); ?></h1>
            <div style="width: 80px; height: 4px; background: var(--accent-color); margin: 0 auto; border-radius: 2px;"></div>
            <p style="max-width: 800px; margin: 25px auto 0; color: rgba(255,255,255,0.7); font-size: 16px; line-height: 1.6;"><?php echo __('contact_subtitle', 'Sorularınız veya teklif talepleriniz için bizimle iletişime geçebilirsiniz.'); ?></p>
        </div>
    </section>

    <section class="section" style="padding: 100px 0;">
        <div class="container">

            <?php if (isset($_SESSION['contact_success'])): ?>
            <div style="background: #dcfce7; color: #166534; padding: 20px; border-radius: 12px; margin-bottom: 40px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                <p style="margin: 0; font-weight: 500;"><?php echo $_SESSION['contact_success']; unset($_SESSION['contact_success']); ?></p>
            </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['contact_error'])): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 20px; border-radius: 12px; margin-bottom: 40px; border: 1px solid #fecaca; display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-exclamation-circle" style="font-size: 24px;"></i>
                <p style="margin: 0; font-weight: 500;"><?php echo $_SESSION['contact_error']; unset($_SESSION['contact_error']); ?></p>
            </div>
            <?php endif; ?>

            <div class="contact-grid">
                <div class="contact-info-list" style="display: flex; flex-direction: column; gap: 40px;">
                    
                    <?php if (!empty($contactInfo['contact_email'])): ?>
                    <div class="info-item" style="display: flex; gap: 20px;">
                        <div class="icon" style="width: 50px; height: 50px; background: var(--secondary-color); color: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;"><?php echo __('contact_email_title', 'E-Posta'); ?></h4>
                            <p style="word-break: break-all;"><a href="mailto:<?php echo htmlspecialchars($contactInfo['contact_email']); ?>" style="color: var(--text-color);"><?php echo htmlspecialchars($contactInfo['contact_email']); ?></a></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($contactInfo['contact_phone'])): ?>
                    <div class="info-item" style="display: flex; gap: 20px;">
                        <div class="icon" style="width: 50px; height: 50px; background: var(--secondary-color); color: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;"><?php echo __('contact_phone_title', 'Telefon'); ?></h4>
                            <p><a href="tel:<?php echo htmlspecialchars($contactInfo['contact_phone']); ?>" style="color: var(--text-color);"><?php echo htmlspecialchars($contactInfo['contact_phone']); ?></a></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($contactInfo['contact_address'])): ?>
                    <div class="info-item" style="display: flex; gap: 20px;">
                        <div class="icon" style="width: 50px; height: 50px; background: var(--secondary-color); color: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;"><?php echo __('contact_address_title', 'Adres'); ?></h4>
                            <p style="font-size: 15px;"><?php echo nl2br(htmlspecialchars($contactInfo['contact_address'])); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($contactInfo['contact_working_hours'])): ?>
                    <div class="info-item" style="display: flex; gap: 20px;">
                        <div class="icon" style="width: 50px; height: 50px; background: var(--secondary-color); color: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 5px;"><?php echo __('contact_hours_title', 'Çalışma Saatleri'); ?></h4>
                            <p><?php echo htmlspecialchars($contactInfo['contact_working_hours']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($whatsappNumber)): ?>
                    <div class="whatsapp-btn-container" style="margin-top: 10px;">
                        <a href="https://wa.me/<?php echo $whatsappNumber; ?>" target="_blank" style="display: flex; align-items: center; gap: 10px; background: #25D366; color: white; padding: 15px 25px; border-radius: 4px; font-weight: bold; width: fit-content; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#1eb954'" onmouseout="this.style.background='#25D366'">
                            <i class="fab fa-whatsapp" style="font-size: 24px;"></i>
                            <?php echo __('whatsapp_btn', 'WhatsApp ile Yazın'); ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="contact-form-wrapper">
                    <form id="contactForm" action="send_mail.php" method="POST" class="contact-form-modern">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo __('form_name', 'Ad Soyad'); ?></label>
                                <input type="text" name="name" required placeholder="<?php echo __('form_name_placeholder', 'Adınız ve soyadınız...'); ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo __('form_email', 'E-Posta'); ?></label>
                                <input type="email" name="email" required placeholder="<?php echo __('form_email_placeholder', 'E-posta adresiniz...'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label><?php echo __('form_service', 'Hizmet Türü'); ?></label>
                            <select name="service">
                                <option value="yeminli"><?php echo __('service1', 'Resmi / Yeminli Tercüme'); ?></option>
                                <option value="noter"><?php echo __('service2', 'Noter Tasdikli Çeviri'); ?></option>
                                <option value="hukuki"><?php echo __('service3', 'Hukuki Çeviri'); ?></option>
                                <option value="diger"><?php echo __('service_other', 'Diğer'); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php echo __('form_message', 'Mesajınız'); ?></label>
                            <textarea name="message" rows="5" required placeholder="<?php echo __('form_message_placeholder', 'Mesajınızı buraya yazın...'); ?>"></textarea>
                        </div>
                        <button type="submit" class="btn-submit"><?php echo __('form_submit', 'Gönder'); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <style>
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 60px;
        }

        .contact-form-modern {
            display: flex;
            flex-direction: column;
            gap: 25px;
            background: #fff;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            background: #f8fafc;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent-color);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(var(--accent-rgb), 0.1);
        }

        .btn-submit {
            background: var(--primary-color);
            color: #fff;
            padding: 18px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: var(--primary-color);
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(var(--primary-rgb), 0.2);
        }

        @media (max-width: 1024px) {
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 50px;
            }
            .contact-form-modern {
                padding: 40px;
            }
        }

        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .contact-form-modern {
                padding: 30px 20px;
            }
            .section {
                padding: 60px 0 !important;
            }
            .page-header {
                padding: 60px 0 !important;
            }
            .page-header h1 {
                font-size: 32px !important;
            }
        }
    </style>
</main>

<?php include 'includes/foot.php'; ?>

