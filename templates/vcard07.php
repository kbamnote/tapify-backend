<?php
/**
 * Tapify vCard Template: vcard07
 * Tech Developer — Dark Cyan/Matrix theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://'.$_SERVER['HTTP_HOST'].'/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['occupation'] ?? 'Digital Card') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#050d12;--card:#0a1a22;--cyan:#06b6d4;--cyan2:#22d3ee;--green:#10b981;--white:#e2f8ff;--text:#4b7d8a;--border:#0e2835;}
body{background:var(--bg);font-family:'Space Grotesk',sans-serif;color:var(--white);overflow-x:hidden;}
.matrix-bg{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.matrix-col{position:absolute;top:-100%;font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--cyan);opacity:.06;animation:matrixFall linear infinite;writing-mode:vertical-lr;letter-spacing:4px;}
.matrix-col:nth-child(1){left:5%;animation-duration:12s;animation-delay:0s;}
.matrix-col:nth-child(2){left:20%;animation-duration:15s;animation-delay:3s;font-size:10px;}
.matrix-col:nth-child(3){left:40%;animation-duration:10s;animation-delay:6s;}
.matrix-col:nth-child(4){left:60%;animation-duration:14s;animation-delay:2s;font-size:11px;}
.matrix-col:nth-child(5){left:80%;animation-duration:11s;animation-delay:8s;}
.matrix-col:nth-child(6){left:92%;animation-duration:13s;animation-delay:4s;font-size:10px;}
@keyframes matrixFall{from{transform:translateY(0);}to{transform:translateY(200vh);}}
.grid-lines{position:fixed;inset:0;pointer-events:none;z-index:0;background-image:linear-gradient(rgba(6,182,212,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(6,182,212,.03) 1px,transparent 1px);background-size:40px 40px;}
.card-wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--bg);position:relative;z-index:1;overflow:hidden;}
.banner{position:relative;height:220px;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;filter:brightness(.3) saturate(.5) hue-rotate(180deg);}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(160deg,rgba(5,13,18,.5),rgba(6,182,212,.1),rgba(5,13,18,.9));}
.terminal-line{position:absolute;top:20px;left:20px;font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--cyan);letter-spacing:1px;}
.cursor{display:inline-block;width:8px;height:14px;background:var(--cyan);animation:blink 1s step-end infinite;vertical-align:middle;margin-left:2px;}
@keyframes blink{0%,100%{opacity:1;}50%{opacity:0;}}
.banner-name{position:absolute;bottom:40px;left:20px;font-family:'JetBrains Mono',monospace;font-size:28px;font-weight:700;color:var(--white);letter-spacing:2px;}
.banner-role{position:absolute;bottom:20px;left:20px;font-size:12px;color:var(--cyan);letter-spacing:2px;}
.cyan-bar{position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--cyan),var(--cyan2),transparent);box-shadow:0 0 8px var(--cyan);}
.profile-area{padding:0 20px 18px;position:relative;z-index:5;}
.av-hex{width:88px;height:88px;clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);background:linear-gradient(135deg,var(--cyan),var(--green));padding:2px;margin-top:-40px;margin-bottom:14px;flex-shrink:0;}
.av-hex img{width:100%;height:100%;object-fit:cover;clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);}
.hire-btn{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--cyan),var(--green));color:var(--bg);font-size:11px;font-weight:700;padding:10px 18px;border-radius:4px;text-decoration:none;letter-spacing:1px;font-family:'JetBrains Mono',monospace;transition:all .3s;margin-bottom:12px;}
.hire-btn:hover{box-shadow:0 6px 20px rgba(6,182,212,.4);}
.dev-name{font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:700;color:var(--white);margin-bottom:4px;}
.dev-role{font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--cyan);margin-bottom:3px;}
.dev-company{font-size:12px;color:var(--text);margin-bottom:10px;}
.stack-row{display:flex;gap:6px;flex-wrap:wrap;margin:10px 0;}
.stack-tag{font-family:'JetBrains Mono',monospace;font-size:10px;padding:3px 10px;border:1px solid rgba(6,182,212,.3);color:var(--cyan);background:rgba(6,182,212,.08);border-radius:4px;}
.social-row{display:flex;gap:8px;padding:8px 0;}
.soc{width:36px;height:36px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:14px;text-decoration:none;background:var(--card);border:1px solid var(--border);color:var(--text);transition:all .3s;}
.soc:hover{background:var(--cyan);color:var(--bg);}
.bio{margin:0 20px 18px;padding:16px 18px;background:var(--card);border:1px solid var(--border);border-left:2px solid var(--cyan);font-family:'JetBrains Mono',monospace;}
.bio p{font-size:12px;line-height:1.8;color:var(--text);}
.section{padding:0 20px 18px;}
.sec-h{font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--cyan);margin-bottom:12px;}
.contact-list{display:flex;flex-direction:column;gap:10px;}
.ci{display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--card);border:1px solid var(--border);border-radius:8px;text-decoration:none;color:var(--white);transition:all .3s;}
.ci:hover{border-color:var(--cyan);background:rgba(6,182,212,.06);}
.ci-ico{width:32px;height:32px;background:rgba(6,182,212,.15);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--cyan);font-size:13px;flex-shrink:0;}
.ci-lbl{font-size:9px;color:var(--text);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;}
.ci-val{font-size:12px;font-weight:500;word-break:break-word;}
.projects-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.proj{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:14px;transition:all .3s;}
.proj:hover{border-color:var(--cyan);}
.proj-name{font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--cyan);margin-bottom:4px;}
.proj-desc{font-size:11px;color:var(--text);line-height:1.5;}
.qr-block{margin:0 20px 18px;background:var(--card);border:1px solid rgba(6,182,212,.3);border-radius:12px;padding:20px;display:flex;align-items:center;gap:16px;}
.qr-box{background:#fff;padding:6px;border-radius:8px;width:84px;height:84px;flex-shrink:0;}
.qr-box img{width:72px;height:72px;}
.qr-txt h4{font-family:'JetBrains Mono',monospace;font-size:14px;color:var(--cyan);margin-bottom:4px;}
.qr-txt p{font-size:11px;color:var(--text);line-height:1.6;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 40px);margin:0 20px 18px;padding:14px;background:linear-gradient(135deg,var(--cyan),var(--green));color:var(--bg);font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:700;border-radius:6px;border:none;cursor:pointer;letter-spacing:2px;transition:all .3s;}
.save-btn:hover{box-shadow:0 8px 24px rgba(6,182,212,.4);}
.share-area{padding:0 20px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid var(--border);color:var(--text);background:var(--card);transition:all .3s;cursor:pointer;}
.sh:hover{border-color:var(--cyan);color:var(--cyan);}
.copy-row{display:flex;border:1px solid var(--border);border-radius:6px;overflow:hidden;background:var(--card);}
.copy-inp{flex:1;background:transparent;border:none;color:var(--text);font-size:11px;padding:12px 14px;font-family:'JetBrains Mono',monospace;outline:none;}
.cp-btn{background:var(--cyan);color:var(--bg);border:none;padding:12px 18px;cursor:pointer;font-weight:700;font-size:12px;}
.footer{text-align:center;padding:16px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--text);border-top:1px solid var(--border);}
.footer a{color:var(--cyan);text-decoration:none;}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}
.fade-in-section.visible{opacity:1;transform:translateY(0);}
</style>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
</head>
<body>
<div class="matrix-bg">
  <div class="matrix-col">10110100110</div>
  <div class="matrix-col">01001101011</div>
  <div class="matrix-col">11010011010</div>
  <div class="matrix-col">00110101101</div>
  <div class="matrix-col">10101100101</div>
  <div class="matrix-col">01100110010</div>
</div>
<div class="grid-lines"></div>
<div class="card-wrap">
  <div class="banner">
    <?php if (($vcard['cover_type'] ?? 'image') === 'video' && !empty($vcard['cover_image'])): ?>
        <?php
        $videoUrl = $vcard['cover_image'];
        if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $videoUrl, $match);
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
    <?php endif; ?>
    <div class="banner-overlay"></div>
    <div class="terminal-line">$ whoami<span class="cursor"></span></div>
    <div class="banner-name"><?= htmlspecialchars($fullName) ?></div>
    <div class="banner-role"><?= htmlspecialchars($vcard['occupation'] ?? 'Developer') ?></div>
    <div class="cyan-bar"></div>
  </div>
  <div class="profile-area">
    <div class="av-hex"><img src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($fullName) ?>"></div>
    <?php if (!empty($vcard['phone'])): ?><a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="hire-btn"><i class="fas fa-terminal"></i> Hire Me</a><?php endif; ?>
    <div class="dev-name"><?= htmlspecialchars($fullName) ?></div>
    <?php if (!empty($vcard['occupation'])): ?><div class="dev-role"><?= htmlspecialchars($vcard['occupation']) ?></div><?php endif; ?>
    <?php if (!empty($vcard['company'])): ?><div class="dev-company"><?= htmlspecialchars($vcard['company']) ?></div><?php endif; ?>
    <?php if (!empty($socialLinks)): ?>
    <div class="social-row">
      <?php foreach ($socialLinks as $sl): $p=strtolower($sl['platform']??''); $ico=$platformIcons[$p]??'fa-globe'; ?>
      <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" class="soc"><i class="fab <?= $ico ?>"></i></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php if (!empty($vcard['description'])): ?>
  <div class="bio fade-in-section"><p><?= nl2br(htmlspecialchars($vcard['description'])) ?></p></div>
  <?php endif; ?>
  <div class="section fade-in-section">
    <div class="sec-h">// Contact</div>
    <div class="contact-list">
      <?php if (!empty($vcard['email'])): ?><a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="ci"><div class="ci-ico"><i class="fas fa-envelope"></i></div><div><div class="ci-lbl">Email</div><div class="ci-val"><?= htmlspecialchars($vcard['email']) ?></div></div></a><?php endif; ?>
      <?php if (!empty($vcard['phone'])): ?><a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="ci"><div class="ci-ico"><i class="fas fa-phone"></i></div><div><div class="ci-lbl">Phone</div><div class="ci-val"><?= htmlspecialchars($vcard['phone']) ?></div></div></a><?php endif; ?>
      <?php if (!empty($waPhone)): ?><a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="ci"><div class="ci-ico"><i class="fab fa-whatsapp"></i></div><div><div class="ci-lbl">WhatsApp</div><div class="ci-val"><?= htmlspecialchars($vcard['phone']) ?></div></div></a><?php endif; ?>
      <?php if (!empty($vcard['location'])): ?><a href="<?= htmlspecialchars($locationUrl) ?>" target="_blank" class="ci"><div class="ci-ico"><i class="fas fa-map-marker-alt"></i></div><div><div class="ci-lbl">Location</div><div class="ci-val"><?= htmlspecialchars($vcard['location']) ?></div></div></a><?php endif; ?>
    </div>
  </div>
  <?php if (!empty($services)): ?>
  <div class="section fade-in-section">
    <div class="sec-h">// Projects &amp; Services</div>
    <div class="projects-grid">
      <?php foreach ($services as $srv): ?>
      <div class="proj">
        <div class="proj-name"><?= htmlspecialchars($srv['title']??'') ?></div>
        <?php if (!empty($srv['description'])): ?><div class="proj-desc"><?= htmlspecialchars($srv['description']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
  <div class="qr-block fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code" width="72" height="72"></div>
    <div class="qr-txt"><h4>Scan &amp; Connect</h4><p>Scan to save contact or view portfolio.</p></div>
  </div>
  <button class="save-btn fade-in-section" onclick="saveContact()"><i class="fas fa-address-card"></i> Save Contact</button>
  <div class="share-area fade-in-section">
    <div class="sh-row">
      <?php if (!empty($waPhone)): ?><a href="https://wa.me/?text=<?= urlencode($cardUrl) ?>" target="_blank" class="sh"><i class="fab fa-whatsapp"></i> Share</a><?php endif; ?>
      <button class="sh" onclick="shareCard()"><i class="fas fa-share-alt"></i> Share</button>
    </div>
    <div class="copy-row"><input class="copy-inp" id="cardUrlInput" value="<?= htmlspecialchars($cardUrl) ?>" readonly><button class="cp-btn" onclick="navigator.clipboard.writeText(document.getElementById('cardUrlInput').value);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)">Copy</button></div>
  </div>
  <?php if (empty($vcard['remove_branding'])): ?><div class="footer">Powered by <a href="https://tapify.in" target="_blank">Tapify</a></div><?php endif; ?>
</div>
<script>
const _obs=new IntersectionObserver(e=>{e.forEach(el=>{if(el.isIntersecting)el.target.classList.add('visible');});},{threshold:0.1});
document.querySelectorAll('.fade-in-section').forEach(el=>_obs.observe(el));
</script>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
