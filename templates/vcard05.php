<?php
/**
 * Tapify vCard Template: vcard05
 * Restaurant Chef — Dark Red/Gold theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://tapify-backend-production.up.railway.app/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['occupation'] ?? 'Digital Card') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Italiana&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#1a0a0a;--card:#2a1010;--red:#c0392b;--gold:#d4a017;--gold2:#f0c040;--white:#fff5e6;--text:#b8a090;--border:#3a2020;}
body{background:var(--bg);font-family:'Raleway',sans-serif;color:var(--white);overflow-x:hidden;}
.flames{position:fixed;bottom:0;left:0;right:0;height:120px;pointer-events:none;z-index:0;display:flex;justify-content:space-around;align-items:flex-end;}
.flame{width:20px;height:60px;border-radius:50% 50% 20% 20%;animation:flameFlicker 1.5s ease-in-out infinite;}
.flame:nth-child(1){background:linear-gradient(to top,#c0392b,#e74c3c,#f39c12);height:70px;animation-delay:0s;}
.flame:nth-child(2){background:linear-gradient(to top,#922b21,#cb4335,#f1c40f);height:50px;animation-delay:.3s;width:14px;}
.flame:nth-child(3){background:linear-gradient(to top,#c0392b,#e74c3c,#f39c12);height:80px;animation-delay:.6s;width:24px;}
.flame:nth-child(4){background:linear-gradient(to top,#922b21,#cb4335,#f1c40f);height:55px;animation-delay:.9s;width:16px;}
.flame:nth-child(5){background:linear-gradient(to top,#c0392b,#e74c3c,#f39c12);height:65px;animation-delay:.15s;}
@keyframes flameFlicker{0%,100%{transform:scaleX(1) scaleY(1);}25%{transform:scaleX(.9) scaleY(1.05);}75%{transform:scaleX(1.1) scaleY(.95);}}
.card-wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--bg);position:relative;z-index:1;overflow:hidden;}
.banner{position:relative;height:240px;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;filter:brightness(.4);}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(26,10,10,.3),rgba(26,10,10,.92));}
.banner-content{position:absolute;bottom:0;left:0;right:0;text-align:center;padding:20px;}
.rest-name{font-family:'Italiana',serif;font-size:30px;color:var(--white);line-height:1.2;margin-bottom:4px;}
.rest-tagline{font-size:11px;letter-spacing:3px;text-transform:uppercase;color:var(--gold);opacity:.85;}
.gold-bottom{position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--gold),var(--gold2),var(--gold),transparent);}
.profile-area{padding:22px 22px 18px;text-align:center;position:relative;z-index:5;}
.chef-avatar{width:104px;height:104px;border-radius:50%;object-fit:cover;border:3px solid var(--gold);margin:0 auto 14px;display:block;box-shadow:0 0 30px rgba(192,57,43,.4);}
.chef-name{font-family:'Italiana',serif;font-size:26px;color:var(--white);margin-bottom:5px;}
.divider-ornament{font-size:14px;color:var(--gold);letter-spacing:8px;margin:4px 0;}
.chef-role{font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--gold);margin-bottom:3px;}
.chef-company{font-size:13px;color:var(--text);margin-bottom:12px;}
.social-strip{display:flex;justify-content:center;gap:10px;padding:8px 0;}
.soc{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:15px;text-decoration:none;background:var(--card);border:1px solid var(--border);color:var(--text);transition:all .3s;}
.soc:hover{background:var(--red);border-color:var(--red);color:#fff;}
.bio-area{margin:0 22px 18px;padding:18px 20px;background:var(--card);border:1px solid var(--border);border-left:3px solid var(--gold);border-radius:0 12px 12px 0;}
.bio-area p{font-size:13px;line-height:1.9;color:var(--text);}
.section{padding:0 22px 18px;}
.sec-h{font-family:'Italiana',serif;font-size:18px;color:var(--gold);margin-bottom:14px;display:flex;align-items:center;gap:10px;}
.sec-h::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(212,160,23,.3),transparent);}
.contact-list{display:flex;flex-direction:column;gap:10px;}
.ci{display:flex;align-items:center;gap:12px;padding:13px 16px;background:var(--card);border:1px solid var(--border);border-radius:12px;text-decoration:none;color:var(--white);transition:all .3s;}
.ci:hover{border-color:var(--gold);background:rgba(212,160,23,.08);}
.ci-ico{width:36px;height:36px;border-radius:8px;background:rgba(212,160,23,.15);display:flex;align-items:center;justify-content:center;color:var(--gold);font-size:13px;flex-shrink:0;}
.ci-lbl{font-size:9px;color:var(--text);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;}
.ci-val{font-size:12px;font-weight:600;word-break:break-word;}
.menu-scroll{display:flex;gap:12px;overflow-x:auto;padding:4px 2px 14px;scrollbar-width:none;}
.menu-scroll::-webkit-scrollbar{display:none;}
.menu-card{min-width:148px;background:var(--card);border:1px solid var(--border);border-radius:12px;overflow:hidden;flex-shrink:0;transition:all .3s;}
.menu-card:hover{border-color:var(--gold);transform:translateY(-4px);}
.menu-card img{width:100%;height:88px;object-fit:cover;filter:brightness(.75);}
.menu-body{padding:10px 12px;}
.menu-title{font-family:'Italiana',serif;font-size:14px;color:var(--white);margin-bottom:3px;}
.menu-desc{font-size:11px;color:var(--text);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.qr-block{margin:0 22px 18px;background:var(--card);border:1px solid rgba(212,160,23,.3);border-radius:18px;padding:22px;display:flex;align-items:center;gap:18px;}
.qr-box{background:#fff;padding:8px;border-radius:10px;width:88px;height:88px;flex-shrink:0;}
.qr-box img{width:72px;height:72px;}
.qr-txt h4{font-family:'Italiana',serif;font-size:20px;color:var(--gold);margin-bottom:5px;}
.qr-txt p{font-size:12px;color:var(--text);line-height:1.6;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 44px);margin:0 22px 18px;padding:15px;background:linear-gradient(135deg,var(--gold),#8b6010);color:var(--bg);font-size:13px;font-weight:700;border-radius:10px;border:none;cursor:pointer;letter-spacing:1px;transition:all .3s;}
.save-btn:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(212,160,23,.4);}
.share-area{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border-radius:10px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid var(--border);color:var(--text);background:var(--card);transition:all .3s;cursor:pointer;}
.sh:hover{border-color:var(--gold);color:var(--gold);}
.copy-row{display:flex;border:1px solid var(--border);border-radius:10px;overflow:hidden;background:var(--card);}
.copy-inp{flex:1;background:transparent;border:none;color:var(--text);font-size:11px;padding:12px 14px;outline:none;}
.cp-btn{background:var(--gold);color:var(--bg);border:none;padding:12px 18px;cursor:pointer;font-weight:700;font-size:12px;}
.footer{text-align:center;padding:16px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--text);border-top:1px solid var(--border);}
.footer a{color:var(--gold);text-decoration:none;}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}
.fade-in-section.visible{opacity:1;transform:translateY(0);}
</style>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
</head>
<body>
<div class="flames">
  <div class="flame"></div><div class="flame"></div><div class="flame"></div>
  <div class="flame"></div><div class="flame"></div>
</div>
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
    <div class="banner-content">
      <div class="rest-name"><?= htmlspecialchars($vcard['company'] ?? $fullName) ?></div>
      <div class="rest-tagline"><?= htmlspecialchars($vcard['occupation'] ?? 'Fine Dining') ?></div>
    </div>
    <div class="gold-bottom"></div>
  </div>
  <div class="profile-area">
    <img class="chef-avatar" src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($fullName) ?>">
    <div class="chef-name"><?= htmlspecialchars($fullName) ?></div>
    <div class="divider-ornament">—— ✦ ——</div>
    <?php if (!empty($vcard['occupation'])): ?><div class="chef-role"><?= htmlspecialchars($vcard['occupation']) ?></div><?php endif; ?>
    <?php if (!empty($vcard['company'])): ?><div class="chef-company"><?= htmlspecialchars($vcard['company']) ?></div><?php endif; ?>
    <?php if (!empty($socialLinks)): ?>
    <div class="social-strip">
      <?php foreach ($socialLinks as $sl): $p=strtolower($sl['platform']??''); $ico=$platformIcons[$p]??'fa-globe'; ?>
      <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" class="soc"><i class="fab <?= $ico ?>"></i></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php if (!empty($vcard['description'])): ?>
  <div class="bio-area fade-in-section"><p><?= nl2br(htmlspecialchars($vcard['description'])) ?></p></div>
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
    <div class="sec-h">Menu &amp; Services</div>
    <div class="menu-scroll">
      <?php foreach ($services as $srv): ?>
      <div class="menu-card">
        <?php if (!empty($srv['image'])): ?><img src="<?= htmlspecialchars(imgUrl($srv['image'])) ?>" alt=""><?php endif; ?>
        <div class="menu-body"><div class="menu-title"><?= htmlspecialchars($srv['title']??'') ?></div><?php if (!empty($srv['description'])): ?><div class="menu-desc"><?= htmlspecialchars($srv['description']) ?></div><?php endif; ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
<?php include __DIR__ . '/_features.php'; ?>
  <div class="qr-block fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code" width="72" height="72"></div>
    <div class="qr-txt"><h4>Reserve a Table</h4><p>Scan to save contact or make a reservation.</p></div>
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
