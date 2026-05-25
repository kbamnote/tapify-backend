<?php
/**
 * Script to automatically inject new sections into tapify templates
 * Run this from CLI: php update_templates.php
 * Or via browser: http://localhost/tapify-backend/update_templates.php
 */

$templatesDir = __DIR__ . '/templates';
if (!is_dir($templatesDir)) {
    die("Templates directory not found.\n");
}

$success = [];
$errors = [];

// Helper function to inject CSS if not present
function injectCss($content, $css) {
    if (strpos($content, '.gal-grid') !== false) return $content; // already injected
    return preg_replace('/(<\/style>)/i', $css . "\n$1", $content, 1);
}

// 1. Process vcard01 to vcard18
for ($i = 1; $i <= 18; $i++) {
    $num = str_pad($i, 2, '0', STR_PAD_LEFT);
    $file = $templatesDir . "/vcard{$num}.php";
    
    if (!file_exists($file)) continue;
    
    $content = file_get_contents($file);
    
    // Inject CSS
    $cssToInject = "
.gal-grid{display:grid;grid-template-columns:repeat(auto-fill, minmax(120px, 1fr));gap:10px;padding:0 22px 20px;}
.gal-item{width:100%;height:120px;border-radius:12px;overflow:hidden;border:1px solid var(--border, #ccc);}
.gal-item img{width:100%;height:100%;object-fit:cover;transition:transform 0.3s;}
.gal-item:hover img{transform:scale(1.05);}
.gal-item iframe{width:100%;height:100%;border:none;object-fit:cover;}
";
    $content = injectCss($content, $cssToInject);

    // Cover Image to Video Logic
    $videoLogic = <<<HTML
<?php if ((\$vcard['cover_type'] ?? 'image') === 'video' && !empty(\$vcard['cover_image'])): ?>
        <?php
        \$videoUrl = \$vcard['cover_image'];
        if (strpos(\$videoUrl, 'youtube.com') !== false || strpos(\$videoUrl, 'youtu.be') !== false) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', \$videoUrl, \$match);
            \$ytId = \$match[1] ?? '';
            echo '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/'.\$ytId.'?autoplay=1&mute=1&loop=1&playlist='.\$ytId.'&controls=0&showinfo=0&rel=0" frameborder="0" allow="autoplay; encrypted-media" style="object-fit:cover;pointer-events:none;"></iframe>';
        } elseif (strpos(\$videoUrl, 'instagram.com') !== false) {
            \$embedUrl = rtrim(\$videoUrl, '/') . '/embed';
            echo '<iframe width="100%" height="100%" src="'.\$embedUrl.'" frameborder="0" allowtransparency="true" style="object-fit:cover;"></iframe>';
        } else {
            echo '<video src="'.imgUrl(\$videoUrl).'" autoplay loop muted playsinline style="width:100%;height:100%;object-fit:cover;"></video>';
        }
        ?>
    <?php else: ?>
        <img src="<?= \$coverImg ?>" alt="">
    <?php endif; ?>
HTML;
    
    // Attempt to replace cover image logic
    $content = preg_replace('/<img\s+src="<\?=\s*\$coverImg\s*\?>"\s*alt="[^"]*">/i', $videoLogic, $content);
    
    // Inject Products, Galleries, Testimonials, Insta Feed
    $newSections = <<<HTML
  <?php if (!empty(\$products)): ?>
  <div class="sec fade-in-section"><div class="sec-h" style="padding:0 22px 14px;font-weight:bold;font-size:18px;">Products</div></div>
  <div class="car-wrap fade-in-section" id="carousel{$num}_prod">
    <div class="car-track" id="carousel{$num}_prod-track">
      <?php foreach (\$products as \$prd): ?>
      <div class="srv-card c-card" style="min-width:160px;background:var(--white);border:1px solid var(--border);border-radius:16px;overflow:hidden;flex-shrink:0;">
        <?php if (!empty(\$prd['image'])): ?><img src="<?= imgUrl(\$prd['image']) ?>" alt="<?= htmlspecialchars(\$prd['name']) ?>" style="width:100%;height:120px;object-fit:cover;"><?php endif; ?>
        <div style="padding:12px;">
          <div style="font-size:13px;font-weight:bold;margin-bottom:4px;"><?= htmlspecialchars(\$prd['name']) ?></div>
          <?php if (\$prd['price'] !== null): ?><div style="font-size:12px;font-weight:700;color:var(--primary, #000);"><?= htmlspecialchars(\$prd['currency'] ?: 'INR') ?> <?= number_format((float)\$prd['price'], 2) ?></div><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="c-dots carousel-dots" id="carousel{$num}_prod-dots"></div>
  </div>
  <?php endif; ?>

  <?php if (!empty(\$galleries)): ?>
  <div class="sec fade-in-section"><div class="sec-h" style="padding:0 22px 14px;font-weight:bold;font-size:18px;">Gallery</div></div>
  <div class="gal-wrap fade-in-section">
    <?php foreach (\$galleries as \$g): ?>
      <?php if (!empty(\$g['images'])): ?>
      <div class="gal-grid">
        <?php foreach (\$g['images'] as \$img): ?>
        <div class="gal-item"><a href="/<?= htmlspecialchars(\$img['image_url']) ?>" target="_blank"><img src="/<?= htmlspecialchars(\$img['image_url']) ?>" alt="Gallery Image"></a></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty(\$testimonials)): ?>
  <div class="sec fade-in-section"><div class="sec-h" style="padding:0 22px 14px;font-weight:bold;font-size:18px;">Testimonials</div></div>
  <div class="car-wrap fade-in-section" id="carousel{$num}_test">
    <div class="car-track" id="carousel{$num}_test-track">
      <?php foreach (\$testimonials as \$t): ?>
      <div class="srv-card c-card" style="min-width:200px;background:var(--card, #fff);border:1px solid var(--border);border-radius:16px;padding:16px;flex-shrink:0;">
        <div style="color:#f59e0b;font-size:12px;margin-bottom:8px;"><?= str_repeat('★', \$t['rating']) . str_repeat('☆', 5 - \$t['rating']) ?></div>
        <div style="font-size:13px;font-style:italic;margin-bottom:12px;">"<?= htmlspecialchars(\$t['message']) ?>"</div>
        <div style="font-size:12px;font-weight:bold;"><?= htmlspecialchars(\$t['name']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="c-dots carousel-dots" id="carousel{$num}_test-dots"></div>
  </div>
  <?php endif; ?>

  <?php if (!empty(\$vcard['show_iframes']) && !empty(\$iframes)): ?>
  <div class="sec fade-in-section"><div class="sec-h" style="padding:0 22px 14px;font-weight:bold;font-size:18px;">Featured Videos</div></div>
  <div class="gal-grid fade-in-section">
      <?php foreach (\$iframes as \$iframe): ?>
      <div class="gal-item" style="border:none;height:180px;">
          <iframe src="<?= htmlspecialchars(\$iframe['url']) ?>" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>
      </div>
      <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty(\$vcard['show_instagram']) && !empty(\$insta_feed)): ?>
  <div class="sec fade-in-section"><div class="sec-h" style="padding:0 22px 14px;font-weight:bold;font-size:18px;">Instagram Feed</div></div>
  <div class="gal-grid fade-in-section">
      <?php foreach (\$insta_feed as \$insta): ?>
      <div class="gal-item" style="border:none;height:250px;">
          <?= \$insta['tag'] ?? '<iframe src="'.htmlspecialchars(\$insta['embed_url'] ?? '').'" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>' ?>
      </div>
      <?php endforeach; ?>
  </div>
  <?php endif; ?>
HTML;

    // Inject before the QR Block or Share Area
    if (strpos($content, 'carousel' . $num . '_prod') === false) {
        $content = preg_replace('/(<div class="qr-block[^>]*>)/i', $newSections . "\n  $1", $content, 1);
        // fallback
        if (strpos($content, 'carousel' . $num . '_prod') === false) {
            $content = preg_replace('/(<div class="qr-blk[^>]*>)/i', $newSections . "\n  $1", $content, 1);
        }
    }
    
    // Inject Carousel JS Init
    if (strpos($content, "initCarousel('carousel{$num}_prod');") === false) {
        $jsToInject = "initCarousel('carousel{$num}');\ninitCarousel('carousel{$num}_prod');\ninitCarousel('carousel{$num}_test');";
        $content = str_replace("initCarousel('carousel{$num}');", $jsToInject, $content);
    }
    
    file_put_contents($file, $content);
    $success[] = "Updated vcard{$num}.php";
}

// 2. Scaffold vcard19.php to vcard28.php
$registryFile = $templatesDir . '/_theme-registry.php';
$themes = file_exists($registryFile) ? include($registryFile) : [];
$baseTemplate = file_get_contents($templatesDir . '/vcard01.php');

for ($i = 19; $i <= 28; $i++) {
    $num = str_pad($i, 2, '0', STR_PAD_LEFT);
    $file = $templatesDir . "/vcard{$num}.php";
    
    // We want to OVERWRITE the legacy 8-line bootstrap files for 19-28
    // Simple scaffold based on vcard01, but we change the template number
    $newContent = str_replace('vcard01', "vcard{$num}", $baseTemplate);
    $newContent = str_replace('carousel01', "carousel{$num}", $newContent);
    
    // If theme info exists, we could inject custom CSS variables here
    if (isset($themes["vcard{$num}"])) {
        $t = $themes["vcard{$num}"];
        $primary = $t['colors']['primary'] ?? '#000';
        $secondary = $t['colors']['secondary'] ?? '#333';
        $newContent = preg_replace('/--primary:[^;]+;/', "--primary:{$primary};", $newContent);
        $newContent = preg_replace('/--secondary:[^;]+;/', "--secondary:{$secondary};", $newContent);
    }
    
    file_put_contents($file, $newContent);
    $success[] = "Generated full standalone vcard{$num}.php";
}

echo "Success:\n" . implode("\n", $success) . "\n";
echo "Errors:\n" . implode("\n", $errors) . "\n";
