<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Girdileri temizle ve al
    $name    = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email   = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_SPECIAL_CHARS);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

    // Basit doğrulama
    if (!$name || !$email || !$message) {
        $_SESSION['contact_error'] = "Lütfen tüm zorunlu alanları doldurun.";
        header("Location: iletisim.php");
        exit;
    }

    try {
        // Veritabanına kaydet
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, service, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $service, $message]);

        $_SESSION['contact_success'] = "Mesajınız başarıyla iletildi. En kısa sürede size dönüş yapacağız.";
    } catch (PDOException $e) {
        $_SESSION['contact_error'] = "Mesaj iletilirken bir hata oluştu: " . $e->getMessage();
    }

    header("Location: iletisim.php");
    exit;

} else {
    header("Location: iletisim.php");
    exit;
}
?>
