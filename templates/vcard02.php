<?php
/**
 * Tapify vCard Template: vcard02
 * Medical Doctor — Teal/Blue theme (light background)
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://'.$_SERVER['HTTP_HOST'].'/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);

$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['occupation'] ?? 'Digital Card') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--teal:#0d9488;--teal2:#14b8a6;--blue:#0369a1;--white:#ffffff;--bg:#f0fdfa;--text:#134e4a;--sub:#6b7280;--border:#99f6e4;--card:#f8fffe;}
body{background:var(--bg);font-family:'DM Sans',sans-serif;color:var(--text);overflow-x:hidden;}
.card-wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--white);box-shadow:0 0 80px rgba(13,148,136,.12);overflow:hidden;position:relative;}
@media(min-width:768px){.med-crosses{display:block!important;}}
.med-crosses{display:none;position:fixed;inset:0;pointer-events:none;z-index:0;}
.med-cross-item{position:absolute;color:var(--teal);opacity:.04;animation:crossFloat 10s infinite ease-in-out;}
.med-cross-item:nth-child(1){font-size:24px;top:8%;left:3%;}
.med-cross-item:nth-child(2){font-size:16px;top:30%;right:4%;animation-delay:3s;}
.med-cross-item:nth-child(3){font-size:32px;top:55%;left:4%;animation-delay:6s;}
.med-cross-item:nth-child(4){font-size:20px;top:72%;right:5%;animation-delay:2s;}
.med-cross-item:nth-child(5){font-size:28px;top:88%;left:7%;animation-delay:8s;}
@keyframes crossFloat{0%,100%{transform:translateY(0) rotate(0);}50%{transform:translateY(-18px) rotate(15deg);}}
.banner{position:relative;height:230px;overflow:hidden;z-index:1;}
.banner img{width:100%;height:100%;object-fit:cover;filter:brightness(.5) saturate(.8);}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(160deg,rgba(13,148,136,.7),rgba(3,105,161,.6));}
.teal-bar{position:absolute;bottom:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--teal),var(--teal2),var(--blue),var(--teal2),var(--teal));background-size:300%;animation:barSlide 4s linear infinite;}
@keyframes barSlide{0%{background-position:0%;}100%{background-position:300%;}}
.profile-area{padding:0 22px 18px;position:relative;z-index:5;background:var(--white);}
.avatar-container{margin-top:-52px;display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:14px;}
.av-wrap{position:relative;width:104px;height:104px;flex-shrink:0;}
.av-wrap img{width:100%;height:100%;border-radius:50%;object-fit:cover;border:4px solid var(--white);box-shadow:0 4px 20px rgba(13,148,136,.25);}
.online-dot{position:absolute;bottom:6px;right:6px;width:16px;height:16px;background:#22c55e;border-radius:50%;border:3px solid var(--white);}
.consult-btn{display:flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--teal),var(--blue));color:#fff;font-size:11px;font-weight:700;padding:11px 18px;border-radius:30px;text-decoration:none;letter-spacing:1px;transition:all .3s;}
.consult-btn:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(13,148,136,.4);}
.doctor-name{font-family:'DM Serif Display',serif;font-size:25px;color:var(--text);margin-bottom:5px;}
.specialty-badge{display:inline-block;background:rgba(13,148,136,.1);border:1px solid rgba(13,148,136,.3);color:var(--teal);font-size:11px;font-weight:600;padding:4px 14px;border-radius:20px;margin-bottom:4px;}
.doctor-hospital{font-size:13px;color:var(--sub);margin-bottom:10px;}
.stats-row{display:flex;gap:0;border:1px solid var(--border);border-radius:14px;overflow:hidden;margin:12px 0;}
.stat{flex:1;text-align:center;padding:12px 4px;border-right:1px solid var(--border);}
.stat:last-child{border:none;}
.stat-num{font-family:'DM Serif Display',serif;font-size:20px;color:var(--teal);display:block;}
.stat-lbl{font-size:9px;text-transform:uppercase;letter-spacing:1px;color:var(--sub);}
.social-strip{display:flex;gap:8px;padding:8px 0;}
.soc{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;text-decoration:none;background:rgba(13,148,136,.08);color:var(--teal);transition:all .3s;}
.soc:hover{background:var(--teal);color:#fff;}
.about-card{margin:0 22px 18px;padding:18px 20px;background:linear-gradient(135deg,#f0fdfa,#ccfbf1);border-radius:18px;border-left:4px solid var(--teal);}
.about-card p{font-size:13px;line-height:1.9;color:var(--text);}
.section{padding:0 22px 18px;}
.sec-h{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
.sec-ico{width:30px;height:30px;border-radius:10px;background:linear-gradient(135deg,var(--teal),var(--blue));display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;flex-shrink:0;}
.sec-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--text);}
.sec-h::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--border),transparent);}
.contact-list{display:flex;flex-direction:column;gap:10px;}
.ci{display:flex;align-items:center;gap:12px;padding:13px 16px;background:var(--card);border:1px solid var(--border);border-radius:14px;text-decoration:none;color:var(--text);transition:all .3s;}
.ci:hover{background:#ccfbf1;border-color:var(--teal);transform:translateX(4px);}
.ci-ico{width:38px;height:38px;border-radius:10px;background:var(--teal);display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;flex-shrink:0;}
.ci-lbl{font-size:9px;color:var(--sub);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;}
.ci-val{font-size:13px;font-weight:600;}
.services-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.srv{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:16px;transition:all .3s;}
.srv:hover{border-color:var(--teal);transform:translateY(-3px);box-shadow:0 8px 20px rgba(13,148,136,.12);}
.srv-icon{width:38px;height:38px;border-radius:10px;background:rgba(13,148,136,.1);display:flex;align-items:center;justify-content:center;color:var(--teal);font-size:16px;margin-bottom:10px;}
.srv-title{font-size:12px;font-weight:700;color:var(--text);margin-bottom:4px;}
.srv-desc{font-size:11px;color:var(--sub);line-height:1.5;}
.qr-block{margin:0 22px 18px;background:linear-gradient(135deg,var(--teal),var(--blue));border-radius:20px;padding:22px;display:flex;align-items:center;gap:18px;}
.qr-box{background:#fff;padding:8px;border-radius:12px;width:88px;height:88px;flex-shrink:0;}
.qr-box img{width:72px;height:72px;}
.qr-txt{color:#fff;}
.qr-txt h4{font-family:'DM Serif Display',serif;font-size:17px;margin-bottom:5px;}
.qr-txt p{font-size:12px;opacity:.9;line-height:1.6;}
.save-area{padding:0 22px 18px;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:15px;background:linear-gradient(135deg,var(--teal),var(--blue));color:#fff;font-size:13px;font-weight:700;border-radius:14px;border:none;cursor:pointer;letter-spacing:1px;transition:all .3s;}
.save-btn:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(13,148,136,.4);}
.share-area{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border-radius:12px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid var(--border);color:var(--sub);background:var(--card);transition:all .3s;cursor:pointer;}
.sh:hover{background:#ccfbf1;border-color:var(--teal);color:var(--teal);}
.copy-row{display:flex;border:1px solid var(--border);border-radius:12px;overflow:hidden;background:var(--card);}
.copy-inp{flex:1;background:transparent;border:none;color:var(--sub);font-size:11px;padding:12px 14px;font-family:'DM Sans',sans-serif;outline:none;}
.cp-btn{background:var(--teal);color:#fff;border:none;padding:12px 18px;cursor:pointer;font-weight:700;font-size:12px;}
.footer{text-align:center;padding:16px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--sub);}
.footer a{color:var(--teal);text-decoration:none;font-weight:700;}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}
.fade-in-section.visible{opacity:1;transform:translateY(0);}
</style>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
</head>
<body>
<div class="med-crosses">
  <i class="fas fa-plus med-cross-item"></i><i class="fas fa-heartbeat med-cross-item"></i>
  <i class="fas fa-plus med-cross-item"></i><i class="fas fa-stethoscope med-cross-item"></i>
  <i class="fas fa-plus med-cross-item"></i>
</div>
<div class="card-wrap">
  <div class="banner">
    <img src="<?= htmlspecialchars($coverImg) ?>" alt="Cover">
    <div class="banner-overlay"></div>
    <div class="teal-bar"></div>
  </div>

  <div class="profile-area">
    <div class="avatar-container">
      <div class="av-wrap">
        <img src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($fullName) ?>">
        <div class="online-dot"></div>
      </div>
      <?php if (!empty($vcard['phone'])): ?>
      <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="consult-btn"><i class="fas fa-calendar-check"></i> Consult</a>
      <?php endif; ?>
    </div>
    <div class="doctor-name"><?= htmlspecialchars($fullName) ?></div>
    <?php if (!empty($vcard['occupation'])): ?>
    <div class="specialty-badge"><?= htmlspecialchars($vcard['occupation']) ?></div>
    <?php endif; ?>
    <?php if (!empty($vcard['company'])): ?>
    <div class="doctor-hospital"><?= htmlspecialchars($vcard['company']) ?></div>
    <?php endif; ?>
    <div class="stats-row">
      <div class="stat"><span class="stat-num">—</span><span class="stat-lbl">Yrs Exp.</span></div>
      <div class="stat"><span class="stat-num">—</span><span class="stat-lbl">Patients</span></div>
      <div class="stat"><span class="stat-num">—</span><span class="stat-lbl">Rating</span></div>
    </div>
    <?php if (!empty($socialLinks)): ?>
    <div class="social-strip">
      <?php foreach ($socialLinks as $sl):
        $platform = strtolower($sl['platform'] ?? '');
        $icon = $platformIcons[$platform] ?? 'fa-globe';
      ?>
      <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" class="soc"><i class="fab <?= $icon ?>"></i></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($vcard['description'])): ?>
  <div class="about-card fade-in-section">
    <p><?= nl2br(htmlspecialchars($vcard['description'])) ?></p>
  </div>
  <?php endif; ?>

  <div class="section fade-in-section">
    <div class="sec-h"><div class="sec-ico"><i class="fas fa-address-book"></i></div><span class="sec-title">Contact</span></div>
    <div class="contact-list">
      <?php if (!empty($vcard['email'])): ?>
      <a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="ci">
        <div class="ci-ico"><i class="fas fa-envelope"></i></div>
        <div><div class="ci-lbl">Email</div><div class="ci-val"><?= htmlspecialchars($vcard['email']) ?></div></div>
      </a>
      <?php endif; ?>
      <?php if (!empty($vcard['phone'])): ?>
      <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="ci">
        <div class="ci-ico"><i class="fas fa-phone"></i></div>
        <div><div class="ci-lbl">Phone</div><div class="ci-val"><?= htmlspecialchars($vcard['phone']) ?></div></div>
      </a>
      <?php endif; ?>
      <?php if (!empty($waPhone)): ?>
      <a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="ci">
        <div class="ci-ico"><i class="fab fa-whatsapp"></i></div>
        <div><div class="ci-lbl">WhatsApp</div><div class="ci-val"><?= htmlspecialchars($vcard['phone']) ?></div></div>
      </a>
      <?php endif; ?>
      <?php if (!empty($vcard['location'])): ?>
      <a href="<?= htmlspecialchars($locationUrl) ?>" target="_blank" class="ci">
        <div class="ci-ico"><i class="fas fa-map-marker-alt"></i></div>
        <div><div class="ci-lbl">Location</div><div class="ci-val"><?= htmlspecialchars($vcard['location']) ?></div></div>
      </a>
      <?php endif; ?>
    </div>
  </div>

  <?php if (!empty($services)): ?>
  <div class="section fade-in-section">
    <div class="sec-h"><div class="sec-ico"><i class="fas fa-stethoscope"></i></div><span class="sec-title">Services</span></div>
    <div class="services-grid">
      <?php foreach ($services as $srv): ?>
      <div class="srv">
        <div class="srv-icon"><i class="fas fa-check-circle"></i></div>
        <div class="srv-title"><?= htmlspecialchars($srv['title'] ?? '') ?></div>
        <?php if (!empty($srv['description'])): ?>
        <div class="srv-desc"><?= htmlspecialchars($srv['description']) ?></div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="qr-block fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code" width="72" height="72"></div>
    <div class="qr-txt"><h4>Scan &amp; Connect</h4><p>Scan to save contact or book a consultation.</p></div>
  </div>

  <div class="save-area fade-in-section">
    <button class="save-btn" onclick="saveContact()"><i class="fas fa-address-card"></i> Save Contact</button>
  </div>

  <div class="share-area fade-in-section">
    <div class="sh-row">
      <?php if (!empty($waPhone)): ?>
      <a href="https://wa.me/?text=<?= urlencode($cardUrl) ?>" target="_blank" class="sh"><i class="fab fa-whatsapp"></i> Share</a>
      <?php endif; ?>
      <button class="sh" onclick="shareCard()"><i class="fas fa-share-alt"></i> Share</button>
    </div>
    <div class="copy-row">
      <input class="copy-inp" id="cardUrlInput" value="<?= htmlspecialchars($cardUrl) ?>" readonly>
      <button class="cp-btn" onclick="navigator.clipboard.writeText(document.getElementById('cardUrlInput').value);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)">Copy</button>
    </div>
  </div>

  <?php if (empty($vcard['remove_branding'])): ?>
  <div class="footer">Powered by <a href="https://tapify.in" target="_blank">Tapify</a></div>
  <?php endif; ?>
</div>

<script>
const _obs=new IntersectionObserver(e=>{e.forEach(el=>{if(el.isIntersecting)el.target.classList.add('visible');});},{threshold:0.1});
document.querySelectorAll('.fade-in-section').forEach(el=>_obs.observe(el));
</script>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
