const fs = require('fs');
const path = require('path');

const templatesDir = path.join(__dirname, 'templates');

const videoLogic = `<?php if (($vcard['cover_type'] ?? 'image') === 'video' && !empty($vcard['cover_image'])): ?>
        <?php
        $videoUrl = $vcard['cover_image'];
        if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
            preg_match('/(?:youtube\\.com\\/(?:[^\\/]+\\/.+\\/|(?:v|e(?:mbed)?)\\/|.*[?&]v=)|youtu\\.be\\/)([^"&?\\/\\s]{11})/i', $videoUrl, $match);
            $ytId = $match[1] ?? '';
            echo '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/'.$ytId.'?autoplay=1&mute=1&loop=1&playlist='.$ytId.'&controls=0&showinfo=0&rel=0" frameborder="0" allow="autoplay; encrypted-media" style="object-fit:cover;pointer-events:none;"></iframe>';
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
    
    const oldRegex = /<img\s+src="<\?=\s*(?:htmlspecialchars\()?\$coverImg(?:\))?\s*\?>"\s*alt="[^"]*">/ig;
    
    if (!content.includes("=== 'video'")) {
        const newContent = content.replace(oldRegex, videoLogic);
        if (newContent !== content) {
            fs.writeFileSync(file, newContent);
            console.log(`Successfully injected Video Cover logic into vcard${num}.php`);
            updatedCount++;
        }
    }
}

if (updatedCount === 0) {
    console.log("No templates were updated. They might already have the video logic.");
} else {
    console.log(`Finished updating ${updatedCount} templates!`);
}
