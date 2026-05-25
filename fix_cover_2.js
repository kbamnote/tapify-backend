const fs = require('fs');
const path = require('path');

const templatesDir = path.join(__dirname, 'templates');

const videoLogic = `<?php if (($vcard['cover_type'] ?? 'image') === 'video' && !empty($vcard['cover_image'])): ?>
        <?php
        $videoUrl = $vcard['cover_image'];
        if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
            preg_match('/(?:youtube\\.com\\/(?:[^\\/]+\\/.+\\/|(?:v|e(?:mbed)?)\\/|.*[?&]v=)|youtu\\.be\\/)([^"&?\\/\\s]{11})/i', $videoUrl, $match);
            $ytId = $match[1] ?? '';
            // Added playsinline=1 and removed pointer-events:none so it can be clicked if autoplay is blocked
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

let updatedCount = 0;

for (let i = 1; i <= 28; i++) {
    const num = String(i).padStart(2, '0');
    const file = path.join(templatesDir, `vcard${num}.php`);
    
    if (!fs.existsSync(file)) continue;
    
    let content = fs.readFileSync(file, 'utf8');
    
    // Replace the block we injected previously
    const blockRegex = /<\?php if \(\(\$vcard\['cover_type'\] \?\? 'image'\) === 'video' && !empty\(\$vcard\['cover_image'\]\)\): \?>[\s\S]*?<\?php endif; \?>/g;
    
    if (blockRegex.test(content)) {
        const newContent = content.replace(blockRegex, videoLogic);
        if (newContent !== content) {
            fs.writeFileSync(file, newContent);
            console.log(`Updated YouTube player in vcard${num}.php`);
            updatedCount++;
        }
    }
}

console.log(`Finished updating ${updatedCount} templates!`);
