<?php
/**
 * Tapify vCard Template: vcard04
 * Real Estate Agent — Cream/Copper theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://app.tapify.co.in/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['occupation'] ?? 'Digital Card') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#faf7f2;--white:#fff;--copper:#b5743a;--copper2:#d4943e;--cream:#fdf6ec;--dark:#2c1a0e;--text:#7a5c3a;--sub:#9a8272;--border:#e8d8c4;--card:#fffcf7;}
body{background:var(--bg);font-family:'Jost',sans-serif;color:var(--text);overflow-x:hidden;}
.texture{position:fixed;inset:0;background-image:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23b5743a' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");pointer-events:none;z-index:0;}
.card-wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--white);box-shadow:0 0 80px rgba(181,116,58,.1);overflow:hidden;position:relative;z-index:1;}
.bg-arch{position:absolute;top:0;right:-50px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(181,116,58,.06),transparent);pointer-events:none;}
.banner{position:relative;height:240px;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;transition:transform 10s ease;}
.banner:hover img{transform:scale(1.06);}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(160deg,rgba(44,26,14,.4),rgba(44,26,14,.75));}
.banner-badge{position:absolute;top:18px;right:18px;background:rgba(181,116,58,.9);color:#fff;font-size:9px;font-weight:700;letter-spacing:2px;padding:6px 14px;border-radius:4px;text-transform:uppercase;}
.banner-stats{position:absolute;bottom:0;left:0;right:0;display:flex;background:rgba(44,26,14,.6);backdrop-filter:blur(8px);}
.bstat{flex:1;text-align:center;padding:10px 4px;border-right:1px solid rgba(255,255,255,.1);}
.bstat:last-child{border:none;}
.bstat-num{font-family:'Cormorant Garamond',serif;font-size:18px;color:var(--copper2);font-weight:700;display:block;}
.bstat-lbl{font-size:8px;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.7);}
.profile-area{padding:0 22px 18px;position:relative;z-index:5;background:var(--white);}
.profile-top{display:flex;align-items:flex-end;justify-content:space-between;margin-top:-50px;margin-bottom:14px;}
.av-wrap{position:relative;width:100px;height:100px;}
.av-wrap img{width:100%;height:100%;border-radius:50%;object-fit:cover;border:4px solid var(--white);box-shadow:0 4px 20px rgba(181,116,58,.3);}
.call-btn{display:flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--copper),#8b4a1e);color:#fff;font-size:11px;font-weight:600;padding:11px 18px;border-radius:30px;text-decoration:none;letter-spacing:1px;transition:all .3s;}
.call-btn:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(181,116,58,.4);}
.agent-name{font-family:'Cormorant Garamond',serif;font-size:27px;font-weight:700;color:var(--dark);margin-bottom:5px;}
.ornament{font-size:18px;color:var(--copper);margin:4px 0;letter-spacing:6px;}
.agent-title{font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--sub);margin-bottom:3px;}
.agent-company{font-size:13px;color:var(--text);margin-bottom:12px;}
.social-strip{display:flex;gap:8px;padding:8px 0;}
.soc{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;text-decoration:none;background:rgba(181,116,58,.1);color:var(--copper);transition:all .3s;}
.soc:hover{background:var(--copper);color:#fff;}
.about-strip{margin:0 22px 18px;padding:18px 20px;background:linear-gradient(135deg,var(--cream),#fdf0e0);border-radius:18px;border-left:4px solid var(--copper);}
.about-strip p{font-size:13px;line-height:1.9;color:var(--text);}
.section{padding:0 22px 18px;}
.sec-h{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--copper);margin-bottom:14px;display:flex;align-items:center;gap:10px;font-weight:600;}
.sec-h::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--border),transparent);}
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.ci{display:flex;align-items:flex-start;gap:10px;padding:13px 14px;background:var(--card);border:1px solid var(--border);border-radius:14px;text-decoration:none;color:var(--text);transition:all .3s;}
.ci:hover{background:#fdf0e0;border-color:var(--copper);transform:translateY(-2px);}
.ci-ico{width:34px;height:34px;border-radius:10px;background:var(--copper);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;flex-shrink:0;}
.ci-lbl{font-size:9px;color:var(--sub);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;}
.ci-val{font-size:12px;font-weight:600;word-break:break-word;}
.services-scroll{display:flex;gap:12px;overflow-x:auto;padding:4px 2px 14px;scrollbar-width:none;}
.services-scroll::-webkit-scrollbar{display:none;}
.srv-card{min-width:150px;background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden;flex-shrink:0;transition:all .3s;}
.srv-card:hover{border-color:var(--copper);transform:translateY(-4px);box-shadow:0 10px 24px rgba(181,116,58,.15);}
.srv-card img{width:100%;height:90px;object-fit:cover;}
.srv-body{padding:10px 12px;}
.srv-title{font-family:'Cormorant Garamond',serif;font-size:14px;font-weight:600;color:var(--dark);margin-bottom:3px;}
.srv-desc{font-size:11px;color:var(--sub);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.qr-section{margin:0 22px 18px;background:linear-gradient(135deg,var(--dark),var(--copper));border-radius:18px;padding:22px;display:flex;align-items:center;gap:18px;}
.qr-box{background:#fff;padding:8px;border-radius:10px;width:88px;height:88px;flex-shrink:0;}
.qr-box img{width:72px;height:72px;}
.qr-txt{color:#fff;}
.qr-txt h4{font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:700;margin-bottom:5px;}
.qr-txt p{font-size:12px;opacity:.88;line-height:1.6;}
.save-btn-wrap{padding:0 22px 18px;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:15px;background:linear-gradient(135deg,var(--copper),#8b4a1e);color:#fff;font-size:13px;font-weight:700;border-radius:30px;border:none;cursor:pointer;letter-spacing:1px;transition:all .3s;}
.save-btn:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(181,116,58,.4);}
.share-area{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border-radius:14px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid var(--border);color:var(--sub);background:var(--card);transition:all .3s;cursor:pointer;}
.sh:hover{background:#fdf0e0;border-color:var(--copper);color:var(--copper);}
.copy-row{display:flex;border:1px solid var(--border);border-radius:14px;overflow:hidden;background:var(--card);}
.copy-inp{flex:1;background:transparent;border:none;color:var(--sub);font-size:11px;padding:12px 14px;outline:none;}
.cp-btn{background:var(--copper);color:#fff;border:none;padding:12px 18px;cursor:pointer;font-weight:700;font-size:12px;}
.footer{text-align:center;padding:16px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--sub);background:var(--cream);}
.footer a{color:var(--copper);text-decoration:none;font-weight:700;}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}
.fade-in-section.visible{opacity:1;transform:translateY(0);}
</style>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
</head>
<body>
<div class="texture"></div>
<div class="card-wrap">
  <div class="bg-arch"></div>
  <div class="banner">
    <?php if (($vcard['cover_type'] ?? 'image') === 'video' && !empty($vcard['cover_image'])): ?>
        <?php
        $videoUrl = $vcard['cover_image'];
        if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $videoUrl, $match);
            $ytId = $match[1] ?? '';
            // Added playsinline=1 and removed pointer-events:none so it can be clicked if autoplay is blocked
            echo '<iframe style="width:100%;height:100%;display:block;border:none;" src="https://www.youtube.com/embed/'.$ytId.'?autoplay=1&mute=1&loop=1&playlist='.$ytId.'&controls=1&showinfo=0&rel=0&playsinline=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
        } elseif (strpos($videoUrl, 'instagram.com') !== false) {
            $embedUrl = rtrim($videoUrl, '/') . '/embed';
            echo '<iframe style="width:100%;height:100%;display:block;border:none;" src="'.$embedUrl.'" frameborder="0" allowtransparency="true"></iframe>';
        } else {
            echo '<video src="'.htmlspecialchars(imgUrl($videoUrl)).'" autoplay loop muted playsinline style="width:100%;height:100%;object-fit:cover;display:block;"></video>';
        }
        ?>
    <?php else: ?>
        <img src="<?= htmlspecialchars($coverImg) ?>" alt="Cover">
    <?php endif; ?>
    <div class="banner-overlay"></div>
    <div class="banner-badge"><?= htmlspecialchars($vcard['occupation'] ?? 'Real Estate') ?></div>
    <div class="banner-stats">
      <div class="bstat"><span class="bstat-num">—</span><span class="bstat-lbl">Sold</span></div>
      <div class="bstat"><span class="bstat-num">—</span><span class="bstat-lbl">Experience</span></div>
      <div class="bstat"><span class="bstat-num">—</span><span class="bstat-lbl">Volume</span></div>
    </div>
  </div>
  <div class="profile-area">
    <div class="profile-top">
      <div class="av-wrap"><img src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($fullName) ?>"></div>
      <?php if (!empty($vcard['phone'])): ?><a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="call-btn"><i class="fas fa-phone"></i> Call</a><?php endif; ?>
    </div>
    <div class="agent-name"><?= htmlspecialchars($fullName) ?></div>
    <div class="ornament">✦ ✦ ✦</div>
    <?php if (!empty($vcard['occupation'])): ?><div class="agent-title"><?= htmlspecialchars($vcard['occupation']) ?></div><?php endif; ?>
    <?php if (!empty($vcard['company'])): ?><div class="agent-company"><?= htmlspecialchars($vcard['company']) ?></div><?php endif; ?>
    <?php if (!empty($socialLinks)): ?>
    <div class="social-strip">
      <?php foreach ($socialLinks as $sl): $p=strtolower($sl['platform']??''); $ico=$platformIcons[$p]??'fa-globe'; ?>
      <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" class="soc"><i class="fab <?= $ico ?>"></i></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php if (!empty($vcard['description'])): ?>
  <div class="about-strip fade-in-section"><p><?= nl2br(htmlspecialchars($vcard['description'])) ?></p></div>
  <?php endif; ?>
  <div class="section fade-in-section">
    <div class="sec-h">Contact</div>
    <div class="contact-grid">
      <?php if (!empty($vcard['email'])): ?><a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="ci"><div class="ci-ico"><i class="fas fa-envelope"></i></div><div><div class="ci-lbl">Email</div><div class="ci-val"><?= htmlspecialchars($vcard['email']) ?></div></div></a><?php endif; ?>
      <?php if (!empty($vcard['phone'])): ?><a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="ci"><div class="ci-ico"><i class="fas fa-phone"></i></div><div><div class="ci-lbl">Phone</div><div class="ci-val"><?= htmlspecialchars($vcard['phone']) ?></div></div></a><?php endif; ?>
      <?php if (!empty($waPhone)): ?><a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="ci"><div class="ci-ico"><i class="fab fa-whatsapp"></i></div><div><div class="ci-lbl">WhatsApp</div><div class="ci-val"><?= htmlspecialchars($vcard['phone']) ?></div></div></a><?php endif; ?>
      <?php if (!empty($vcard['location'])): ?><a href="<?= htmlspecialchars($locationUrl) ?>" target="_blank" class="ci"><div class="ci-ico"><i class="fas fa-map-marker-alt"></i></div><div><div class="ci-lbl">Location</div><div class="ci-val"><?= htmlspecialchars($vcard['location']) ?></div></div></a><?php endif; ?>
    </div>
  </div>
  <?php if (!empty($services)): ?>
  <div class="section fade-in-section">
    <div class="sec-h">Listings &amp; Services</div>
    <div class="services-scroll">
      <?php foreach ($services as $srv): ?>
      <div class="srv-card">
        <?php if (!empty($srv['image'])): ?><img src="<?= htmlspecialchars(imgUrl($srv['image'])) ?>" alt=""><?php endif; ?>
        <div class="srv-body"><div class="srv-title"><?= htmlspecialchars($srv['title']??'') ?></div><?php if (!empty($srv['description'])): ?><div class="srv-desc"><?= htmlspecialchars($srv['description']) ?></div><?php endif; ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
<?php include __DIR__ . '/_features.php'; ?>
  <div class="qr-section fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code" width="72" height="72"></div>
    <div class="qr-txt"><h4>Scan &amp; Connect</h4><p>Scan to save contact or explore listings.</p></div>
  </div>
  <div class="save-btn-wrap fade-in-section"><button class="save-btn" onclick="saveContact()"><i class="fas fa-address-card"></i> Save Contact</button></div>
  <div class="share-area fade-in-section">
    <div class="sh-row">
      <?php if (!empty($waPhone)): ?><a href="https://wa.me/?text=<?= urlencode($cardUrl) ?>" target="_blank" class="sh"><i class="fab fa-whatsapp"></i> Share</a><?php endif; ?>
      <button class="sh" onclick="shareCard()"><i class="fas fa-share-alt"></i> Share</button>
    </div>
    <div class="copy-row"><input class="copy-inp" id="cardUrlInput" value="<?= htmlspecialchars($cardUrl) ?>" readonly><button class="cp-btn" onclick="navigator.clipboard.writeText(document.getElementById('cardUrlInput').value);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)">Copy</button></div>
  </div>
  <?php if (empty($vcard['remove_branding'])): ?><div class="footer">Powered by <a href="https://tapify.in" target="_blank">Tapify</a></div><?php endif; ?>
  <div style="text-align:center;font-size:0.7rem;padding:0 16px 14px;color:inherit;opacity:0.6;">An innovative Product From : <strong>Mr Print World Pvt Ltd.</strong></div>
</div>
<script>
const _obs=new IntersectionObserver(e=>{e.forEach(el=>{if(el.isIntersecting)el.target.classList.add('visible');});},{threshold:0.1});
document.querySelectorAll('.fade-in-section').forEach(el=>_obs.observe(el));
</script>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
