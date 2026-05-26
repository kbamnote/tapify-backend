<?php
/**
 * Unified vCard template renderer — unique layout per theme via CSS + structure variants.
 */
$theme = $TAPIFY_THEME;
$layout = $theme['layout'] ?? 'classic';
$isDark = !empty($theme['dark']);

$primary = !empty($vcard['primary_color']) ? $vcard['primary_color'] : ($theme['primary'] ?? '#8338ec');
$secondary = !empty($vcard['secondary_color']) ? $vcard['secondary_color'] : ($theme['secondary'] ?? '#a855f7');
$bg = !empty($vcard['bg_color']) ? $vcard['bg_color'] : ($theme['bg'] ?? '#ffffff');
$surface = $theme['surface'] ?? ($isDark ? '#1f2937' : '#f9fafb');
$text = $isDark ? '#f3f4f6' : '#1a2035';
$muted = $isDark ? '#9ca3af' : '#6b7280';

$fonts = [
    'poppins' => 'Poppins:wght@300;400;500;600;700;800',
    'inter' => 'Inter:wght@300;400;500;600;700',
    'montserrat' => 'Montserrat:wght@300;400;500;600;700',
    'raleway' => 'Raleway:wght@300;400;500;600;700',
    'playfair' => 'Playfair+Display:wght@400;600;700',
    'lora' => 'Lora:wght@400;500;600;700',
    'oswald' => 'Oswald:wght@400;500;600;700',
    'merriweather' => 'Merriweather:wght@300;400;700',
    'cormorant' => 'Cormorant+Garamond:wght@400;500;600;700',
    'nunito' => 'Nunito:wght@300;400;600;700',
    'open-sans' => 'Open+Sans:wght@300;400;600;700',
    'roboto' => 'Roboto:wght@300;400;500;700',
];
$fontKey = $theme['font'] ?? 'poppins';
$fontQuery = $fonts[$fontKey] ?? $fonts['poppins'];

// CSS font-family values for each font key
$fontFamilyMap = [
    'poppins'      => "'Poppins', sans-serif",
    'inter'        => "'Inter', sans-serif",
    'montserrat'   => "'Montserrat', sans-serif",
    'raleway'      => "'Raleway', sans-serif",
    'playfair'     => "'Playfair Display', serif",
    'lora'         => "'Lora', serif",
    'oswald'       => "'Oswald', sans-serif",
    'merriweather' => "'Merriweather', serif",
    'cormorant'    => "'Cormorant Garamond', serif",
    'nunito'       => "'Nunito', sans-serif",
    'open-sans'    => "'Open Sans', sans-serif",
    'roboto'       => "'Roboto', sans-serif",
];
$bodyFont    = $fontFamilyMap[$fontKey] ?? "'Poppins', sans-serif";
$headingFont = $bodyFont; // headings use same theme font; layouts can override via CSS

$coverH = match ($layout) {
    'luxury', 'legacy', 'photo' => '280px',
    'minimal', 'bio-light', 'bio-dark' => '120px',
    'bold', 'wave' => '260px',
    default => '220px',
};

$profileClass = match ($layout) {
    'portfolio', 'photo', 'architect' => 'profile-photo shape-square',
    'soft', 'playful', 'nature' => 'profile-photo shape-rounded',
    default => 'profile-photo shape-circle',
};

$containerExtra = 'layout-' . preg_replace('/[^a-z0-9-]/', '', $layout);
if ($isDark) $containerExtra .= ' theme-dark';

$badgeHtml = '';
if ($layout === 'clinic') {
    $badgeHtml = '<span class="profile-badge"><i class="fas fa-paw"></i> Pet Care</span>';
} elseif ($layout === 'education') {
    $badgeHtml = '<span class="profile-badge"><i class="fas fa-graduation-cap"></i> Education</span>';
} elseif ($layout === 'tech') {
    $badgeHtml = '<span class="profile-badge"><i class="fas fa-code"></i> Developer</span>';
} elseif ($layout === 'wedding') {
    $badgeHtml = '<span class="profile-badge"><i class="fas fa-heart"></i> Events</span>';
}

$saveLabel = match ($layout) {
    'luxury', 'legacy' => 'Save Contact',
    'transport' => 'Book Ride',
    'retail' => 'Shop Now',
    default => 'Save to Contacts',
};

// Build cover media HTML (image or video)
$coverMediaHtml = '';
if (($vcard['cover_type'] ?? 'image') === 'video' && !empty($vcard['cover_image'])) {
    $cvUrl = $vcard['cover_image'];
    if (strpos($cvUrl, 'youtube.com') !== false || strpos($cvUrl, 'youtu.be') !== false) {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $cvUrl, $_m);
        $_ytId = $_m[1] ?? '';
        $coverMediaHtml = '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/'.$_ytId.'?autoplay=1&mute=1&loop=1&playlist='.$_ytId.'&controls=1&showinfo=0&rel=0&playsinline=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="width:100%;height:100%;object-fit:cover;display:block;border:none;"></iframe>';
    } elseif (strpos($cvUrl, 'instagram.com') !== false) {
        $coverMediaHtml = '<iframe width="100%" height="100%" src="'.htmlspecialchars(rtrim($cvUrl,'/').'/embed').'" frameborder="0" allowtransparency="true" style="width:100%;height:100%;object-fit:cover;display:block;border:none;"></iframe>';
    } else {
        $coverMediaHtml = '<video src="'.htmlspecialchars(imgUrl($cvUrl)).'" autoplay loop muted playsinline style="width:100%;height:100%;object-fit:cover;display:block;"></video>';
    }
} elseif (!empty($vcard['cover_image'])) {
    $coverMediaHtml = '<img src="'.htmlspecialchars(imgUrl($vcard['cover_image'])).'" alt="Cover">';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($fullName) ?> - <?= htmlspecialchars($vcard['occupation'] ?? $theme['name'] ?? '') ?></title>
    <meta name="description" content="<?= htmlspecialchars(strip_tags($vcard['description'] ?? '')) ?>">
    <link rel="icon" type="image/png" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
    <link href="https://fonts.googleapis.com/css2?family=<?= $fontQuery ?>&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: <?= htmlspecialchars($primary) ?>;
            --secondary: <?= htmlspecialchars($secondary) ?>;
            --bg: <?= htmlspecialchars($bg) ?>;
            --surface: <?= htmlspecialchars($surface) ?>;
            --text: <?= htmlspecialchars($text) ?>;
            --muted: <?= htmlspecialchars($muted) ?>;
            --cover-h: <?= $coverH ?>;
            --font-body: <?= $bodyFont ?>;
            --font-heading: <?= $headingFont ?>;
        }
<?php
echo file_get_contents(__DIR__ . '/_theme-base.css');
echo file_get_contents(__DIR__ . '/_theme-layouts.css');
?>
        <?= $vcard['custom_css'] ?? '' ?>
        
        /* FAB Styles */
        .fab-container { position: fixed; bottom: 25px; right: 25px; z-index: 9999; display: flex; flex-direction: column; align-items: center; }
        .fab-options { display: flex; flex-direction: column; gap: 12px; margin-bottom: 15px; opacity: 0; visibility: hidden; transform: translateY(20px); transition: all 0.3s ease; }
        .fab-container.active .fab-options { opacity: 1; visibility: visible; transform: translateY(0); }
        .fab-option { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: transform 0.2s; }
        .fab-option:hover { transform: scale(1.1); color: white; }
        .fab-wa { background-color: #25D366; }
        .fab-share { background-color: var(--primary); }
        .fab-main { width: 55px; height: 55px; border-radius: 50%; background-color: var(--primary); color: white; border: none; font-size: 24px; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.3s ease; display: flex; align-items: center; justify-content: center; }
        .fab-container.active .fab-main { transform: rotate(45deg); }
        
        /* WhatsApp Modal */
        .wa-modal { display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .wa-modal-content { background-color: var(--surface); color: var(--text); padding: 25px; border-radius: 16px; width: 90%; max-width: 350px; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.3); font-family: var(--font-body); }
        .wa-close { position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; color: var(--muted); line-height: 1; }
        .wa-modal-content h3 { margin: 0 0 10px 0; font-size: 18px; font-weight: 600; }
        .wa-modal-content p { font-size: 13px; color: var(--muted); margin: 0 0 15px 0; line-height: 1.4; }
        .wa-input { width: 100%; padding: 12px 15px; border: 1px solid var(--muted); border-radius: 8px; margin-bottom: 15px; font-size: 15px; background: var(--bg); color: var(--text); box-sizing: border-box; outline: none; }
        .wa-input:focus { border-color: var(--primary); }
        .wa-btn { width: 100%; padding: 12px; background-color: #25D366; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .wa-btn:hover { background-color: #1ebd5a; }
    </style>
</head>
<body class="<?= $isDark ? 'body-dark' : '' ?>">
<div class="vcard-container <?= htmlspecialchars($containerExtra) ?>" data-template="<?= htmlspecialchars($theme['slug']) ?>">

    <?php if ($layout === 'split'): ?>
    <div class="split-header">
        <div class="split-cover cover-section">
            <?= $coverMediaHtml ?>
        </div>
        <div class="split-profile profile-section">
    <?php elseif ($layout === 'sidebar'): ?>
    <div class="sidebar-accent"></div>
    <div class="cover-section"><?= $coverMediaHtml ?></div>
    <div class="profile-section">
    <?php elseif ($layout === 'minimal' || $layout === 'bio-light' || $layout === 'bio-dark'): ?>
    <div class="cover-section cover-minimal"></div>
    <div class="profile-section profile-minimal">
    <?php elseif ($layout === 'social' || $layout === 'social-grid'): ?>
    <div class="social-hero">
        <div class="cover-section"><?= $coverMediaHtml ?></div>
    </div>
    <div class="profile-section">
    <?php else: ?>
    <div class="cover-section">
        <?= $coverMediaHtml ?>
        <?php if ($layout === 'luxury'): ?>
        <div class="top-badge"><span class="agent-badge">Premium</span><span class="luxury-tag"><?= htmlspecialchars($vcard['occupation'] ?: 'Professional') ?></span></div>
        <?php endif; ?>
    </div>
    <div class="profile-section <?= $layout === 'floating' ? 'profile-floating' : '' ?>">
    <?php endif; ?>

        <div class="<?= $profileClass ?>">
            <?php if ($vcard['profile_image']): ?>
                <img src="<?= imgUrl($vcard['profile_image']) ?>" alt="<?= htmlspecialchars($fullName) ?>">
            <?php else: ?>
                <div class="placeholder"><?= strtoupper(substr($fullName, 0, 1)) ?></div>
            <?php endif; ?>
        </div>
        <?= $badgeHtml ?>
        <h1 class="profile-name"><?= htmlspecialchars($fullName) ?></h1>
        <?php if (!empty($vcard['occupation'])): ?><p class="profile-title"><?= htmlspecialchars($vcard['occupation']) ?></p><?php endif; ?>
        <?php if (!empty($vcard['company'])): ?><p class="profile-company"><i class="fas fa-building"></i> <?= htmlspecialchars($vcard['company']) ?></p><?php endif; ?>
        <?php if (!empty($vcard['description'])): ?><div class="profile-desc"><?= $vcard['description'] ?></div><?php endif; ?>

        <a href="javascript:saveContact()" class="save-contact-btn"><i class="fas fa-user-plus"></i> <?= htmlspecialchars($saveLabel) ?></a>
    </div>

    <?php if ($layout === 'split'): ?></div><?php endif; ?>

    <div class="quick-actions <?= in_array($layout, ['social-stack', 'transport'], true) ? 'quick-actions-row' : '' ?>">
        <?php if (!empty($vcard['phone'])): ?>
            <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="quick-action call"><i class="fas fa-phone"></i><span>Call</span></a>
            <a href="https://wa.me/<?= preg_replace('/\D/', '', $vcard['phone']) ?>" target="_blank" class="quick-action whatsapp"><i class="fab fa-whatsapp"></i><span>WhatsApp</span></a>
        <?php endif; ?>
        <?php if (!empty($vcard['email'])): ?>
            <a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="quick-action email"><i class="fas fa-envelope"></i><span>Email</span></a>
        <?php endif; ?>
        <?php if (!empty($vcard['location_url']) || !empty($vcard['location'])): ?>
            <a href="<?= !empty($vcard['location_url']) ? htmlspecialchars($vcard['location_url']) : 'https://maps.google.com/?q=' . urlencode($vcard['location']) ?>" target="_blank" class="quick-action location"><i class="fas fa-map-marker-alt"></i><span>Location</span></a>
        <?php endif; ?>
        <a href="javascript:shareCard()" class="quick-action"><i class="fas fa-share-alt"></i><span>Share</span></a>
    </div>

    <?php if ($layout === 'social-stack' && count($socialLinks) > 0): ?>
    <div class="section social-highlight">
        <h3 class="section-title"><i class="fas fa-share-nodes"></i> Follow</h3>
        <div class="social-grid social-grid-large">
            <?php
            $iconMap = [
                'facebook'  => 'fab fa-facebook-f',  'instagram' => 'fab fa-instagram',
                'twitter'   => 'fab fa-twitter',      'x'         => 'fab fa-x-twitter',
                'linkedin'  => 'fab fa-linkedin-in',  'whatsapp'  => 'fab fa-whatsapp',
                'youtube'   => 'fab fa-youtube',      'tiktok'    => 'fab fa-tiktok',
                'pinterest' => 'fab fa-pinterest-p',  'snapchat'  => 'fab fa-snapchat-ghost',
                'github'    => 'fab fa-github',       'spotify'   => 'fab fa-spotify',
                'behance'   => 'fab fa-behance',      'dribbble'  => 'fab fa-dribbble',
                'telegram'  => 'fab fa-telegram',
            ];
            foreach ($socialLinks as $sl):
                $platformKey = strtolower($sl['platform'] ?? '');
                $iconClass = $iconMap[$platformKey] ?? 'fas fa-link';
            ?>
            <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" class="social-icon social-<?= htmlspecialchars($platformKey) ?>"><i class="<?= $iconClass ?>"></i></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php include __DIR__ . '/_sections.php'; ?>

    <div class="vcard-footer">
        <div class="view-counter"><i class="fas fa-eye"></i> <?= number_format((int)$vcard['view_count']) ?> views</div>
        <?php if (empty($vcard['remove_branding'])): ?>
        <p>Powered by <a href="/">Tapify</a></p>
        <?php endif; ?>
        <p style="margin-top:3px;opacity:0.6;">An innovative Product From : <strong>Mr Print World Pvt Ltd.</strong></p>
        <p class="footer-year">© <?= date('Y') ?></p>
    </div>
</div>

    <!-- FAB (Floating Action Button) -->
    <div class="fab-container">
        <div class="fab-options">
            <a href="javascript:void(0)" onclick="openWhatsAppModal()" class="fab-option fab-wa" title="Share via WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
            <a href="javascript:shareCard()" class="fab-option fab-share" title="Share">
                <i class="fas fa-share-alt"></i>
            </a>
        </div>
        <button class="fab-main" onclick="toggleFab()">
            <i class="fas fa-plus"></i>
        </button>
    </div>

    <!-- WhatsApp Share Modal -->
    <div id="waModal" class="wa-modal">
        <div class="wa-modal-content">
            <span class="wa-close" onclick="closeWhatsAppModal()">&times;</span>
            <h3>Share via WhatsApp</h3>
            <p>Enter WhatsApp number with country code (e.g., 919876543210) to share your profile link.</p>
            <input type="tel" id="waNumber" placeholder="919876543210" class="wa-input">
            <button onclick="sendWhatsApp()" class="wa-btn"><i class="fab fa-whatsapp"></i> Send on WhatsApp</button>
        </div>
    </div>

<?php include __DIR__ . '/_shared-scripts.php'; ?>
<?= $vcard['custom_js'] ?? '' ?>
</body>
</html>
