const fs = require('fs');
const path = require('path');

const templatesDir = path.join(__dirname, 'templates');

for (let i = 1; i <= 28; i++) {
    const num = String(i).padStart(2, '0');
    const file = path.join(templatesDir, `vcard${num}.php`);
    
    if (!fs.existsSync(file)) continue;
    
    let content = fs.readFileSync(file, 'utf8');
    
    // Replace the old iframes and instagram section with the fixed logic
    const oldRegex = /<\?php if \(!empty\(\$vcard\['show_iframes'\]\).*?<\?php endif; \?>\s*<\?php if \(!empty\(\$vcard\['show_instagram'\]\).*?<\?php endif; \?>/gs;
    
    const newSection = `<?php if (!empty($iframes)): ?>
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
  <?php endif; ?>

  <?php if (!empty($insta_feed)): ?>
  <div class="sec fade-in-section"><div class="sec-h" style="padding:0 22px 14px;font-weight:bold;font-size:18px;">Instagram Feed</div></div>
  <div class="gal-grid fade-in-section">
      <?php foreach ($insta_feed as $insta): ?>
      <div class="gal-item" style="border:none;height:250px;">
          <?= $insta['tag'] ?? '<iframe src="'.htmlspecialchars($insta['embed_url'] ?? '').'" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>' ?>
      </div>
      <?php endforeach; ?>
  </div>
  <?php endif; ?>`;

    content = content.replace(oldRegex, newSection);
    fs.writeFileSync(file, content);
    console.log(`Fixed iframe logic in vcard${num}.php`);
}
console.log('All templates successfully fixed using Node.js!');
