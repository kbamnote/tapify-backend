const fs = require('fs');
const path = require('path');

const templatesDir = path.join(__dirname, 'templates');

// The new video logic for the cover
const videoLogic = `<?php if (($vcard['cover_type'] ?? 'image') === 'video' && !empty($vcard['cover_image'])): ?>
        <?php
        $videoUrl = $vcard['cover_image'];
        if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
            preg_match('/(?:youtube\\.com\\/(?:[^\\/]+\\/.+\\/|(?:v|e(?:mbed)?)\\/|.*[?&]v=)|youtu\\.be\\/)([^"&?\\/\\s]{11})/i', $videoUrl, $match);
            $ytId = $match[1] ?? '';
            echo '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/'.$ytId.'?autoplay=1&mute=1&loop=1&playlist='.$ytId.'&controls=1&showinfo=0&rel=0&playsinline=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen style="object-fit:cover;"></iframe>';
        } elseif (strpos($videoUrl, 'instagram.com') !== false) {
            $embedUrl = rtrim($videoUrl, '/') . '/embed';
            echo '<iframe width="100%" height="100%" src="'.$embedUrl.'" frameborder="0" allowtransparency="true" style="object-fit:cover;"></iframe>';
        } else {
            echo '<video src="'.imgUrl($videoUrl).'" autoplay loop muted playsinline style="width:100%;height:100%;object-fit:cover;"></video>';
        }
        ?>
    <?php else: ?>
        <img src="<?= htmlspecialchars($coverImg) ?>" alt="Cover">
    <?php endif; ?>`;

// The new logic for the Featured Videos section (Iframes)
const iframesLogic = `<?php if (!empty($iframes)): ?>
  <div class="sec fade-in-section"><div class="sec-h" style="padding:0 22px 14px;font-weight:bold;font-size:18px;">Featured Videos</div></div>
  <div class="gal-grid fade-in-section">
      <?php foreach ($iframes as $iframe): ?>
      <div class="gal-item" style="border:none;height:180px;">
          <?php 
          $url = $iframe['url'] ?? '';
          if (strpos($url, '<iframe') !== false) {
              echo $url;
          } else {
              echo '<iframe src="'.htmlspecialchars($url).'" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>';
          }
          ?>
      </div>
      <?php endforeach; ?>
  </div>
  <?php endif; ?>`;

const instaLogic = `<?php if (!empty($insta_feed)): ?>
  <div class="sec fade-in-section"><div class="sec-h" style="padding:0 22px 14px;font-weight:bold;font-size:18px;">Instagram Feed</div></div>
  <div class="gal-grid fade-in-section">
      <?php foreach ($insta_feed as $insta): ?>
      <div class="gal-item" style="border:none;height:250px;">
          <?= $insta['tag'] ?? '<iframe src="'.htmlspecialchars($insta['embed_url'] ?? '').'" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>' ?>
      </div>
      <?php endforeach; ?>
  </div>
  <?php endif; ?>`;

let updatedCount = 0;

for (let i = 1; i <= 28; i++) {
    const num = String(i).padStart(2, '0');
    const file = path.join(templatesDir, `vcard${num}.php`);
    
    if (!fs.existsSync(file)) continue;
    
    let content = fs.readFileSync(file, 'utf8');
    let modified = false;

    // 1. Fix Cover Video
    const oldCoverRegex = /<img\s+src="<\?=\s*(?:htmlspecialchars\()?\$coverImg(?:\))?\s*\?>"\s*alt="[^"]*">/ig;
    if (!content.includes("=== 'video'")) {
        const newContent = content.replace(oldCoverRegex, videoLogic);
        if (newContent !== content) {
            content = newContent;
            modified = true;
            console.log(`[vcard${num}.php] Injected Cover Video logic.`);
        }
    } else {
        // Update existing video logic with new params if present
        const blockRegex = /<\?php if \(\(\$vcard\['cover_type'\] \?\? 'image'\) === 'video' && !empty\(\$vcard\['cover_image'\]\)\): \?>[\s\S]*?<\?php endif; \?>/g;
        if (blockRegex.test(content) && !content.includes("playsinline=1")) {
            content = content.replace(blockRegex, videoLogic);
            modified = true;
            console.log(`[vcard${num}.php] Updated Cover Video logic (added playsinline).`);
        }
    }

    // 2. Fix Iframes and Instagram (Remove show_iframes toggle bug)
    // We will do a generic replacement for the buggy blocks:
    const oldIframeRegex = /<\?php if \(!empty\(\$vcard\['show_iframes'\]\) && !empty\(\$iframes\)\): \?>[\s\S]*?(?=<\?php if \(!empty\(\$vcard\['show_instagram'\]\)|\n\s*<\/div>\n\s*<script>)/i;
    
    if (oldIframeRegex.test(content)) {
        content = content.replace(oldIframeRegex, iframesLogic + "\n\n  ");
        modified = true;
        console.log(`[vcard${num}.php] Fixed Featured Videos section.`);
    }

    const oldInstaRegex = /<\?php if \(!empty\(\$vcard\['show_instagram'\]\) && !empty\(\$insta_feed\)\): \?>[\s\S]*?(?=<\/div>\n\s*<script>|HTML;)/i;
    if (oldInstaRegex.test(content)) {
        content = content.replace(oldInstaRegex, instaLogic + "\n\n  ");
        modified = true;
        console.log(`[vcard${num}.php] Fixed Instagram section.`);
    }
    
    // Also check if they were injected in previous runs using the single block regex from fix_templates.js
    const oldBlockRegex = /<\?php if \(!empty\(\$vcard\['show_iframes'\]\)[\s\S]*?<\?php endif; \?>\s*<\?php if \(!empty\(\$vcard\['show_instagram'\]\)[\s\S]*?<\?php endif; \?>/g;
    if (oldBlockRegex.test(content)) {
         content = content.replace(oldBlockRegex, iframesLogic + "\n\n  " + instaLogic);
         modified = true;
         console.log(`[vcard${num}.php] Fixed BOTH Featured Videos and Instagram sections.`);
    }

    if (modified) {
        fs.writeFileSync(file, content);
        updatedCount++;
    }
}

if (updatedCount === 0) {
    console.log("No templates needed updating (they are already perfect!).");
} else {
    console.log(`SUCCESS! Fixed ${updatedCount} templates.`);
}
