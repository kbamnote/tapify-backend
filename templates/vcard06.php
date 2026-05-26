<?php
/**
 * Tapify vCard Template: vcard06
 * Fitness Trainer — Dark/Orange theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://tapify-backend-production.up.railway.app/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['occupation'] ?? 'Digital Card') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#0d0d0d;--card:#1a1a1a;--orange:#f97316;--orange2:#fb923c;--white:#f5f5f5;--text:#9ca3af;--border:#2a2a2a;}
body{background:var(--bg);font-family:'Barlow',sans-serif;color:var(--white);overflow-x:hidden;}
.energy-bars{position:fixed;left:0;top:0;bottom:0;width:4px;z-index:0;pointer-events:none;}
.energy-bars::before{content:'';position:absolute;inset:0;background:linear-gradient(to bottom,transparent,var(--orange),transparent);animation:energyPulse 2s ease-in-out infinite;}
@keyframes energyPulse{0%,100%{opacity:.3;}50%{opacity:.8;}}
.card-wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--bg);position:relative;z-index:1;overflow:hidden;}
.banner{position:relative;height:240px;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;filter:brightness(.4) contrast(1.2);}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(13,13,13,.3),rgba(13,13,13,.9));}
.banner-text{position:absolute;bottom:20px;left:20px;right:20px;}
.train-text{font-family:'Bebas Neue',cursive;font-size:42px;color:var(--white);letter-spacing:4px;line-height:1;}
.orange-bar{position:absolute;bottom:0;left:0;right:0;height:3px;background:var(--orange);box-shadow:0 0 10px var(--orange);}
.profile-section{padding:0 20px 18px;position:relative;z-index:5;}
.profile-top{display:flex;align-items:flex-end;justify-content:space-between;margin-top:-48px;margin-bottom:14px;}
.av-clip{width:96px;height:96px;clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);background:linear-gradient(135deg,var(--orange),#c05000);padding:3px;flex-shrink:0;}
.av-clip img{width:100%;height:100%;object-fit:cover;clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);}
.book-btn{display:flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--orange),#c05000);color:#fff;font-family:'Bebas Neue',cursive;font-size:15px;padding:11px 20px;text-decoration:none;letter-spacing:2px;border-radius:4px;transition:all .3s;}
.book-btn:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(249,115,22,.4);}
.trainer-name{font-family:'Bebas Neue',cursive;font-size:28px;letter-spacing:2px;color:var(--white);margin-bottom:3px;}
.trainer-role{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--orange);margin-bottom:3px;}
.trainer-company{font-size:13px;color:var(--text);margin-bottom:12px;}
.stats-bar{display:flex;border:1px solid var(--border);border-radius:10px;overflow:hidden;margin:12px 0;}
.stat{flex:1;text-align:center;padding:10px 4px;border-right:1px solid var(--border);}
.stat:last-child{border:none;}
.stat-num{font-family:'Bebas Neue',cursive;font-size:22px;color:var(--orange);display:block;letter-spacing:1px;}
.stat-lbl{font-size:9px;text-transform:uppercase;letter-spacing:1px;color:var(--text);}
.social-row{display:flex;gap:8px;padding:10px 0;}
.soc{width:38px;height:38px;clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);background:var(--card);display:flex;align-items:center;justify-content:center;color:var(--text);font-size:14px;text-decoration:none;transition:all .3s;}
.soc:hover{background:var(--orange);color:#fff;}
.bio{margin:0 20px 18px;padding:16px 18px;background:var(--card);border-left:3px solid var(--orange);border-radius:0 10px 10px 0;}
.bio p{font-size:13px;line-height:1.8;color:var(--text);}
.section{padding:0 20px 18px;}
.sec-h{font-family:'Bebas Neue',cursive;font-size:20px;letter-spacing:3px;color:var(--orange);margin-bottom:14px;display:flex;align-items:center;gap:10px;}
.sec-h::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--border),transparent);}
.contact-list{display:flex;flex-direction:column;gap:10px;}
.ci{display:flex;align-items:center;gap:12px;padding:13px 16px;background:var(--card);border:1px solid var(--border);border-radius:10px;text-decoration:none;color:var(--white);transition:all .3s;}
.ci:hover{border-color:var(--orange);background:rgba(249,115,22,.08);}
.ci-ico{width:34px;height:34px;background:rgba(249,115,22,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--orange);font-size:13px;flex-shrink:0;}
.ci-lbl{font-size:9px;color:var(--text);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;}
.ci-val{font-size:12px;font-weight:600;word-break:break-word;}
.prog-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.prog{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:14px;transition:all .3s;}
.prog:hover{border-color:var(--orange);transform:translateY(-3px);}
.prog-icon{font-size:22px;margin-bottom:8px;}
.prog-title{font-family:'Bebas Neue',cursive;font-size:15px;letter-spacing:1px;color:var(--white);margin-bottom:3px;}
.prog-desc{font-size:11px;color:var(--text);line-height:1.5;}
.qr-block{margin:0 20px 18px;background:linear-gradient(135deg,var(--orange),#c05000);border-radius:12px;padding:20px;display:flex;align-items:center;gap:16px;}
.qr-box{background:#fff;padding:8px;border-radius:8px;width:84px;height:84px;flex-shrink:0;}
.qr-box img{width:68px;height:68px;}
.qr-txt{color:#fff;}
.qr-txt h4{font-family:'Bebas Neue',cursive;font-size:20px;letter-spacing:2px;margin-bottom:4px;}
.qr-txt p{font-size:12px;opacity:.9;line-height:1.5;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 40px);margin:0 20px 18px;padding:15px;background:linear-gradient(135deg,var(--orange),#c05000);color:#fff;font-family:'Bebas Neue',cursive;font-size:16px;letter-spacing:3px;border-radius:4px;border:none;cursor:pointer;transition:all .3s;}
.save-btn:hover{box-shadow:0 8px 24px rgba(249,115,22,.4);}
.share-area{padding:0 20px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid var(--border);color:var(--text);background:var(--card);transition:all .3s;cursor:pointer;}
.sh:hover{border-color:var(--orange);color:var(--orange);}
.copy-row{display:flex;border:1px solid var(--border);border-radius:6px;overflow:hidden;background:var(--card);}
.copy-inp{flex:1;background:transparent;border:none;color:var(--text);font-size:11px;padding:12px 14px;outline:none;}
.cp-btn{background:var(--orange);color:#fff;border:none;padding:12px 18px;cursor:pointer;font-weight:700;font-size:12px;}
.footer{text-align:center;padding:16px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--text);border-top:1px solid var(--border);}
.footer a{color:var(--orange);text-decoration:none;}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}
.fade-in-section.visible{opacity:1;transform:translateY(0);}
</style>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
</head>
<body>
<div class="energy-bars"></div>
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
    <div class="banner-text">
      <div class="train-text">TRAIN HARDER</div>
    </div>
    <div class="orange-bar"></div>
  </div>
  <div class="profile-section">
    <div class="profile-top">
      <div class="av-clip"><img src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($fullName) ?>"></div>
      <?php if (!empty($vcard['phone'])): ?><a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="book-btn"><i class="fas fa-dumbbell"></i> Book</a><?php endif; ?>
    </div>
    <div class="trainer-name"><?= htmlspecialchars($fullName) ?></div>
    <?php if (!empty($vcard['occupation'])): ?><div class="trainer-role"><?= htmlspecialchars($vcard['occupation']) ?></div><?php endif; ?>
    <?php if (!empty($vcard['company'])): ?><div class="trainer-company"><?= htmlspecialchars($vcard['company']) ?></div><?php endif; ?>
    <div class="stats-bar">
      <div class="stat"><span class="stat-num">—</span><span class="stat-lbl">Clients</span></div>
      <div class="stat"><span class="stat-num">—</span><span class="stat-lbl">Sessions</span></div>
      <div class="stat"><span class="stat-num">—</span><span class="stat-lbl">Yrs Exp</span></div>
      <div class="stat"><span class="stat-num">—</span><span class="stat-lbl">Rating</span></div>
    </div>
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
    <div class="sec-h">Contact</div>
    <div class="contact-list">
      <?php if (!empty($vcard['email'])): ?><a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="ci"><div class="ci-ico"><i class="fas fa-envelope"></i></div><div><div class="ci-lbl">Email</div><div class="ci-val"><?= htmlspecialchars($vcard['email']) ?></div></div></a><?php endif; ?>
      <?php if (!empty($vcard['phone'])): ?><a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="ci"><div class="ci-ico"><i class="fas fa-phone"></i></div><div><div class="ci-lbl">Phone</div><div class="ci-val"><?= htmlspecialchars($vcard['phone']) ?></div></div></a><?php endif; ?>
      <?php if (!empty($waPhone)): ?><a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="ci"><div class="ci-ico"><i class="fab fa-whatsapp"></i></div><div><div class="ci-lbl">WhatsApp</div><div class="ci-val"><?= htmlspecialchars($vcard['phone']) ?></div></div></a><?php endif; ?>
      <?php if (!empty($vcard['location'])): ?><a href="<?= htmlspecialchars($locationUrl) ?>" target="_blank" class="ci"><div class="ci-ico"><i class="fas fa-map-marker-alt"></i></div><div><div class="ci-lbl">Location</div><div class="ci-val"><?= htmlspecialchars($vcard['location']) ?></div></div></a><?php endif; ?>
    </div>
  </div>
  <?php if (!empty($services)): ?>
  <div class="section fade-in-section">
    <div class="sec-h">Programs</div>
    <div class="prog-grid">
      <?php foreach ($services as $srv): ?>
      <div class="prog">
        <div class="prog-icon">🏋️</div>
        <div class="prog-title"><?= htmlspecialchars($srv['title']??'') ?></div>
        <?php if (!empty($srv['description'])): ?><div class="prog-desc"><?= htmlspecialchars($srv['description']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
<?php include __DIR__ . '/_features.php'; ?>
  <div class="qr-block fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code" width="68" height="68"></div>
    <div class="qr-txt"><h4>Scan &amp; Train</h4><p>Scan to save contact or book a session.</p></div>
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
  <div style="text-align:center;font-size:0.7rem;padding:0 16px 14px;color:inherit;opacity:0.6;">A unit of <strong>Mr Print World</strong></div>
</div>
<script>
const _obs=new IntersectionObserver(e=>{e.forEach(el=>{if(el.isIntersecting)el.target.classList.add('visible');});},{threshold:0.1});
document.querySelectorAll('.fade-in-section').forEach(el=>_obs.observe(el));
</script>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
