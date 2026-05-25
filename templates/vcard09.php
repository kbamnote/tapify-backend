<?php
/**
 * Tapify vCard Template: vcard09
 * Beauty Salon — Pink/Rose Gold theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://'.$_SERVER['HTTP_HOST'].'/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['occupation'] ?? 'Digital Card') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Gilda+Display&family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#fff0f6;--white:#fff;--pink:#e8a0c0;--rose:#c97088;--blush:#fce4ec;--dark:#4a1a2c;--text:#7a4060;--sub:#b08090;--border:#f4c0d8;--card:#fff8fc;}
body{background:var(--bg);font-family:'Nunito',sans-serif;color:var(--text);overflow-x:hidden;}
.petals{position:fixed;inset:0;pointer-events:none;z-index:0;}
.petal{position:absolute;font-size:18px;opacity:.15;animation:petalFall linear infinite;}
.petal:nth-child(1){left:10%;animation-duration:8s;animation-delay:0s;}
.petal:nth-child(2){left:30%;animation-duration:11s;animation-delay:3s;font-size:14px;}
.petal:nth-child(3){left:55%;animation-duration:9s;animation-delay:6s;}
.petal:nth-child(4){left:75%;animation-duration:12s;animation-delay:2s;font-size:16px;}
.petal:nth-child(5){left:90%;animation-duration:10s;animation-delay:5s;font-size:13px;}
@keyframes petalFall{0%{transform:translateY(-50px) rotate(0deg);opacity:0;}10%{opacity:.2;}90%{opacity:.1;}100%{transform:translateY(100vh) rotate(360deg);opacity:0;}}
.card-wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--white);box-shadow:0 0 80px rgba(232,160,192,.2);overflow:hidden;position:relative;z-index:1;}
.banner{position:relative;height:230px;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;filter:brightness(.6) saturate(1.2);}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(160deg,rgba(201,112,136,.5),rgba(74,26,44,.8));}
.banner-content{position:absolute;bottom:20px;left:0;right:0;text-align:center;}
.studio-name{font-family:'Gilda Display',serif;font-size:28px;color:#fff;margin-bottom:5px;}
.studio-tag{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--pink);opacity:.9;}
.pink-bar{position:absolute;bottom:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--rose),var(--pink),#f8c4d4,var(--pink),var(--rose));background-size:300%;animation:pBar 4s linear infinite;}
@keyframes pBar{0%{background-position:0%;}100%{background-position:300%;}}
.profile-area{padding:0 22px 18px;text-align:center;position:relative;z-index:5;background:var(--white);}
.av-wrap{margin:-52px auto 14px;width:104px;height:104px;position:relative;display:inline-block;}
.av-wrap img{width:100%;height:100%;border-radius:50%;object-fit:cover;border:4px solid var(--white);box-shadow:0 4px 20px rgba(201,112,136,.3);}
.av-ring{position:absolute;inset:-4px;border-radius:50%;background:conic-gradient(var(--rose),var(--pink),#fce4ec,var(--rose));animation:avSpin 6s linear infinite;z-index:-1;}
@keyframes avSpin{to{transform:rotate(360deg);}}
.book-btn{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--rose),var(--dark));color:#fff;font-size:11px;font-weight:700;padding:10px 22px;border-radius:30px;text-decoration:none;letter-spacing:1px;transition:all .3s;margin-bottom:14px;}
.book-btn:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(201,112,136,.4);}
.stylist-name{font-family:'Gilda Display',serif;font-size:25px;color:var(--dark);margin-bottom:5px;}
.spec-row{display:flex;justify-content:center;gap:8px;flex-wrap:wrap;margin:10px 0;}
.spec{font-size:10px;padding:4px 12px;border-radius:20px;background:var(--blush);color:var(--rose);font-weight:700;border:1px solid var(--border);}
.stylist-title{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--sub);margin-bottom:3px;}
.stylist-salon{font-size:13px;color:var(--text);margin-bottom:12px;}
.social-strip{display:flex;justify-content:center;gap:10px;padding:8px 0;}
.soc{width:38px;height:38px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:14px;text-decoration:none;background:var(--blush);color:var(--rose);transition:all .3s;}
.soc:hover{background:var(--rose);color:#fff;}
.bio{margin:0 22px 18px;padding:18px 20px;background:linear-gradient(135deg,#fff0f6,#fce4ec);border-radius:18px;border-left:4px solid var(--rose);}
.bio p{font-size:13px;line-height:1.9;color:var(--text);}
.section{padding:0 22px 18px;}
.sec-h{font-family:'Gilda Display',serif;font-size:18px;color:var(--dark);margin-bottom:14px;display:flex;align-items:center;gap:10px;}
.sec-h::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--border),transparent);}
.contact-list{display:flex;flex-direction:column;gap:10px;}
.ci{display:flex;align-items:center;gap:12px;padding:13px 16px;background:var(--card);border:1px solid var(--border);border-radius:14px;text-decoration:none;color:var(--text);transition:all .3s;}
.ci:hover{background:var(--blush);border-color:var(--rose);}
.ci-ico{width:36px;height:36px;border-radius:10px;background:var(--rose);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;flex-shrink:0;}
.ci-lbl{font-size:9px;color:var(--sub);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;}
.ci-val{font-size:13px;font-weight:600;}
.services-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.srv{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:16px;transition:all .3s;text-align:center;}
.srv:hover{border-color:var(--rose);transform:translateY(-3px);box-shadow:0 8px 20px rgba(201,112,136,.15);}
.srv-icon{font-size:24px;margin-bottom:8px;}
.srv-title{font-family:'Gilda Display',serif;font-size:14px;color:var(--dark);margin-bottom:4px;}
.srv-desc{font-size:11px;color:var(--sub);line-height:1.5;}
.srv-price{font-size:12px;color:var(--rose);font-weight:700;margin-top:6px;}
.qr-block{margin:0 22px 18px;background:linear-gradient(135deg,var(--dark),var(--rose));border-radius:20px;padding:22px;display:flex;align-items:center;gap:18px;}
.qr-box{background:#fff;padding:8px;border-radius:12px;width:88px;height:88px;flex-shrink:0;}
.qr-box img{width:72px;height:72px;}
.qr-txt{color:#fff;}
.qr-txt h4{font-family:'Gilda Display',serif;font-size:18px;margin-bottom:5px;}
.qr-txt p{font-size:12px;opacity:.9;line-height:1.6;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 44px);margin:0 22px 18px;padding:15px;background:linear-gradient(135deg,var(--rose),var(--dark));color:#fff;font-size:13px;font-weight:700;border-radius:30px;border:none;cursor:pointer;letter-spacing:1px;transition:all .3s;}
.save-btn:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(201,112,136,.4);}
.share-area{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border-radius:14px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid var(--border);color:var(--sub);background:var(--card);transition:all .3s;cursor:pointer;}
.sh:hover{background:var(--blush);border-color:var(--rose);color:var(--rose);}
.copy-row{display:flex;border:1px solid var(--border);border-radius:14px;overflow:hidden;background:var(--card);}
.copy-inp{flex:1;background:transparent;border:none;color:var(--sub);font-size:11px;padding:12px 14px;outline:none;}
.cp-btn{background:var(--rose);color:#fff;border:none;padding:12px 18px;cursor:pointer;font-weight:700;font-size:12px;}
.footer{text-align:center;padding:16px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--sub);background:var(--blush);}
.footer a{color:var(--rose);text-decoration:none;font-weight:700;}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}
.fade-in-section.visible{opacity:1;transform:translateY(0);}
</style>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
</head>
<body>
<div class="petals">
  <div class="petal">🌸</div><div class="petal">🌺</div><div class="petal">🌸</div>
  <div class="petal">🌺</div><div class="petal">🌸</div>
</div>
<div class="card-wrap">
  <div class="banner">
    <img src="<?= htmlspecialchars($coverImg) ?>" alt="Cover">
    <div class="banner-overlay"></div>
    <div class="banner-content">
      <div class="studio-name"><?= htmlspecialchars($vcard['company'] ?? $fullName) ?></div>
      <div class="studio-tag"><?= htmlspecialchars($vcard['occupation'] ?? 'Beauty & Wellness') ?></div>
    </div>
    <div class="pink-bar"></div>
  </div>
  <div class="profile-area">
    <div class="av-wrap">
      <div class="av-ring"></div>
      <img src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($fullName) ?>">
    </div>
    <?php if (!empty($vcard['phone'])): ?><a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="book-btn"><i class="fas fa-calendar-alt"></i> Book Now</a><?php endif; ?>
    <div class="stylist-name"><?= htmlspecialchars($fullName) ?></div>
    <?php if (!empty($vcard['occupation'])): ?><div class="stylist-title"><?= htmlspecialchars($vcard['occupation']) ?></div><?php endif; ?>
    <?php if (!empty($vcard['company'])): ?><div class="stylist-salon"><?= htmlspecialchars($vcard['company']) ?></div><?php endif; ?>
    <?php if (!empty($socialLinks)): ?>
    <div class="social-strip">
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
    <div class="sec-h">Services</div>
    <div class="services-grid">
      <?php foreach ($services as $srv): ?>
      <div class="srv">
        <div class="srv-icon">✨</div>
        <div class="srv-title"><?= htmlspecialchars($srv['title']??'') ?></div>
        <?php if (!empty($srv['description'])): ?><div class="srv-desc"><?= htmlspecialchars($srv['description']) ?></div><?php endif; ?>
        <?php if (!empty($srv['price'])): ?><div class="srv-price"><?= htmlspecialchars($srv['price']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
  <div class="qr-block fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code" width="72" height="72"></div>
    <div class="qr-txt"><h4>Book Your Glow</h4><p>Scan to save contact or book an appointment.</p></div>
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
