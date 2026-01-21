    <?php
    // Fetch contact info for footer
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'contact_email'");
    $stmt->execute();
    $footer_email = $stmt->fetchColumn() ?: 'info@example.com';
    
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'contact_phone'");
    $stmt->execute();
    $footer_phone = $stmt->fetchColumn() ?: '+90 XXX XXX XX XX';
    ?>
    <footer class="footer">
        <div class="footer-content container">
            <div class="footer-section about">
                <h3>Sefa Kaya</h3>
                <p><?php echo __('footer_about', 'Profesyonel Yeminli Tercüme Hizmetleri. Türkiye, Almanya ve İngilizce konuşulan ülkeler için resmi belgelerinizde güvenilir çözüm ortağınız.'); ?></p>
            </div>
            <div class="footer-section links">
                <h4><?php echo __('footer_links', 'Hızlı Bağlantılar'); ?></h4>
                <ul>
                    <li><a href="hakkimda.php"><?php echo __('nav_about', 'Hakkımda'); ?></a></li>
                    <li><a href="hizmetler.php"><?php echo __('nav_services', 'Hizmetler'); ?></a></li>
                    <li><a href="surec.php"><?php echo __('nav_process', 'Çeviri Süreci'); ?></a></li>
                    <li><a href="iletisim.php"><?php echo __('nav_contact', 'İletişim'); ?></a></li>
                </ul>
            </div>
            <div class="footer-section contact-info">
                <h4><?php echo __('footer_contact', 'İletişim'); ?></h4>
                <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($footer_email); ?>" style="color: inherit;"><?php echo htmlspecialchars($footer_email); ?></a></p>
                <p><i class="fas fa-phone"></i> <a href="tel:<?php echo htmlspecialchars($footer_phone); ?>" style="color: inherit;"><?php echo htmlspecialchars($footer_phone); ?></a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Sefa Kaya. <?php echo __('footer_rights', 'Tüm Hakları Saklıdır.'); ?></p>
        </div>
    </footer>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
