<?php
// Theme Definitions
function getAvailableThemes() {
    return [
        'old-money-wood' => [
            'name' => 'Old Money Wood',
            'description' => 'Ahşap tonları ve klasik zarafet',
            'colors' => [
                'primary' => '#5D4037',
                'secondary' => '#FDFBF7',
                'text' => '#2C1E11',
                'light-gray' => '#E8E2D9',
                'accent' => '#C5A059'
            ]
        ],
        'dark-academia' => [
            'name' => 'Dark Academia',
            'description' => 'Koyu yeşil ve altın - entelektüel derinlik',
            'colors' => [
                'primary' => '#1B3022',
                'secondary' => '#F9F7F2',
                'text' => '#2A1B10',
                'light-gray' => '#E8E4DC',
                'accent' => '#D4AF37'
            ]
        ],
        'royal-navy' => [
            'name' => 'Royal Navy',
            'description' => 'Lacivert ve gümüş - aristokrat zarafet',
            'colors' => [
                'primary' => '#002347',
                'secondary' => '#F4F7F9',
                'text' => '#1A1A1A',
                'light-gray' => '#E1E8ED',
                'accent' => '#A7A9AC'
            ]
        ],
        'modern-corporate' => [
            'name' => 'Modern Corporate',
            'description' => 'Slate mavi ve bronz - güncel profesyonellik',
            'colors' => [
                'primary' => '#2F4858',
                'secondary' => '#F8F9FA',
                'text' => '#1A202C',
                'light-gray' => '#E2E8F0',
                'accent' => '#9E7E56'
            ]
        ],
        'sworn-minimalism' => [
            'name' => 'Sworn Minimalism',
            'description' => 'Kömür ve kum - minimalist lüks',
            'colors' => [
                'primary' => '#343A40',
                'secondary' => '#F1F3F5',
                'text' => '#212529',
                'light-gray' => '#DEE2E6',
                'accent' => '#C2996B'
            ]
        ],
        'mediterranean-classic' => [
            'name' => 'Mediterranean Classic',
            'description' => 'Terracotta ve taş - sıcak ve köklü',
            'colors' => [
                'primary' => '#8B4513',
                'secondary' => '#F5F1E9',
                'text' => '#3E2723',
                'light-gray' => '#E8E0D5',
                'accent' => '#556B2F'
            ]
        ],
        'global-vision' => [
            'name' => 'Global Vision',
            'description' => 'Gece mavisi ve şampanya - küresel prestij',
            'colors' => [
                'primary' => '#0F172A',
                'secondary' => '#FDFBFA',
                'text' => '#1E293B',
                'light-gray' => '#F1F5F9',
                'accent' => '#AF8B58'
            ]
        ]
    ];
}

function getActiveTheme($pdo) {
    try {
        $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'active_theme'");
        $theme = $stmt->fetchColumn();
        return $theme ?: 'global-vision';
    } catch (Exception $e) {
        return 'global-vision';
    }
}

function hexToRgb($hex) {
    $hex = str_replace("#", "", $hex);
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    return "$r, $g, $b";
}

function generateThemeCSS($themeName) {
    $themes = getAvailableThemes();
    if (!isset($themes[$themeName])) {
        $themeName = 'global-vision';
    }
    
    $theme = $themes[$themeName];
    $colors = $theme['colors'];
    
    $primaryRgb = hexToRgb($colors['primary']);
    $accentRgb = hexToRgb($colors['accent']);
    
    return "
:root {
    --primary-color: {$colors['primary']};
    --primary-rgb: {$primaryRgb};
    --secondary-color: {$colors['secondary']};
    --text-color: {$colors['text']};
    --light-gray: {$colors['light-gray']};
    --white: #FFFFFF;
    --accent-color: {$colors['accent']};
    --accent-rgb: {$accentRgb};
    --max-width: 1200px;
    --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}
";
}
?>
