<?php

class Language {
    private $pdo;
    private $current_lang;
    private $current_lang_id;
    private $translations = [];

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->initLanguage();
        $this->loadTranslations();
    }

    private function initLanguage() {
        if (isset($_GET['lang'])) {
            $lang = $_GET['lang'];
            $_SESSION['lang'] = $lang;
        } elseif (isset($_SESSION['lang'])) {
            $lang = $_SESSION['lang'];
        } else {
            $lang = DEFAULT_LANG;
        }

        // Validate lang exists in DB
        $stmt = $this->pdo->prepare("SELECT id, code FROM languages WHERE code = ?");
        $stmt->execute([$lang]);
        $row = $stmt->fetch();
        if ($row) {
            $this->current_lang = $row['code'];
            $this->current_lang_id = $row['id'];
        } else {
            // Fallback to default
            $stmt = $this->pdo->prepare("SELECT id, code FROM languages WHERE is_default = 1 LIMIT 1");
            $stmt->execute();
            $default = $stmt->fetch();
            $this->current_lang = $default['code'];
            $this->current_lang_id = $default['id'];
        }
    }

    private function loadTranslations() {
        $stmt = $this->pdo->prepare("
            SELECT t.key, t.value 
            FROM translations t 
            JOIN languages l ON t.lang_id = l.id 
            WHERE l.code = ?
        ");
        $stmt->execute([$this->current_lang]);
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $this->translations[$row['key']] = $row['value'];
        }
    }

    public function get($key, $default = null) {
        return $this->translations[$key] ?? ($default ?? $key);
    }

    public function getCurrentLang() {
        return $this->current_lang;
    }

    public function getLangId() {
        return $this->current_lang_id;
    }
}
?>
