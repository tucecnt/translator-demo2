-- Database Initialization SQL

CREATE TABLE IF NOT EXISTS languages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    is_default TINYINT(1) DEFAULT 0
);

CREATE TABLE IF NOT EXISTS translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lang_id INT NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    `value` TEXT NOT NULL,
    FOREIGN KEY (lang_id) REFERENCES languages(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS page_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    lang_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    meta_title VARCHAR(255),
    meta_description TEXT,
    content TEXT,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_id) REFERENCES languages(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) NOT NULL UNIQUE,
    icon VARCHAR(100),
    sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS service_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    lang_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_id) REFERENCES languages(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `references` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255),
    sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS reference_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference_id INT NOT NULL,
    lang_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    FOREIGN KEY (reference_id) REFERENCES `references`(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_id) REFERENCES languages(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS specialties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS specialty_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    specialty_id INT NOT NULL,
    lang_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_id) REFERENCES languages(id) ON DELETE CASCADE
);

-- Seed Languages
INSERT IGNORE INTO languages (id, code, name, is_default) VALUES (1, 'tr', 'Türkçe', 1);
INSERT IGNORE INTO languages (id, code, name, is_default) VALUES (2, 'de', 'Deutsch', 0);
INSERT IGNORE INTO languages (id, code, name, is_default) VALUES (3, 'en', 'English', 0);

-- Seed Default Admin (password: admin123)
-- Hash represents 'admin123'
INSERT IGNORE INTO users (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Seed Default Theme
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('active_theme', 'global-vision');

-- Seed Translations (TR)
INSERT IGNORE INTO translations (lang_id, `key`, `value`) VALUES 
(1, 'nav_title', 'Yeminli Tercüman'),
(1, 'nav_home', 'Ana Sayfa'),
(1, 'nav_about', 'Hakkımda'),
(1, 'nav_services', 'Hizmetler'),
(1, 'nav_process', 'Çeviri Süreci'),
(1, 'nav_references', 'Referanslar'),
(1, 'nav_contact', 'İletişim'),
(1, 'hero_title', 'Profesyonel Yeminli Tercüme'),
(1, 'hero_subtitle', 'Sefa Kaya ile Türkiye, Almanya ve İngilizce konuşulan ülkeler arasında hukuki, resmi ve ticari belgeleriniz için güvenilir çeviri çözümleri.'),
(1, 'hero_btn', 'Teklif Alın'),
(1, 'trust_title', 'Neden Biz?');

-- Seed Translations (DE)
INSERT IGNORE INTO translations (lang_id, `key`, `value`) VALUES 
(2, 'nav_title', 'Vereidigter Übersetzer'),
(2, 'nav_home', 'Startseite'),
(2, 'nav_about', 'Über mich'),
(2, 'nav_services', 'Dienstleistungen'),
(2, 'nav_process', 'Ablauf'),
(2, 'nav_references', 'Referenzen'),
(2, 'nav_contact', 'Kontakt'),
(2, 'hero_title', 'Professionelle vereidigte Übersetzung'),
(2, 'hero_subtitle', 'Zuverlässige Übersetzungslösungen für Ihre rechtlichen, offiziellen und kommerziellen Dokumente zwischen der Türkei, Deutschland und englischsprachigen Ländern mit Sefa Kaya.'),
(2, 'hero_btn', 'Angebot anfordern'),
(2, 'trust_title', 'Warum wir?');

-- Seed Translations (EN)
INSERT IGNORE INTO translations (lang_id, `key`, `value`) VALUES 
(3, 'nav_title', 'Certified Interpreter'),
(3, 'nav_home', 'Home'),
(3, 'nav_about', 'About Me'),
(3, 'nav_services', 'Services'),
(3, 'nav_process', 'Process'),
(3, 'nav_references', 'References'),
(3, 'nav_contact', 'Contact'),
(3, 'hero_title', 'Professional Certified Translation'),
(3, 'hero_subtitle', 'Reliable translation solutions for your legal, official, and commercial documents between Turkey, Germany, and English-speaking countries with Sefa Kaya.'),
(3, 'hero_btn', 'Get a Quote'),
(3, 'trust_title', 'Why Us?');
