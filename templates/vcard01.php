<?php
/**
 * Tapify vCard Template: vcard01
 * Corporate Executive — Navy/Gold theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = PUBLIC_URL.'/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=500&q=80';
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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--navy:#0d1b2a;--gold:#c9a84c;--gold2:#e8c96d;--gold3:#f5e6a3;--white:#f8f5ee;--text:#c4bfb0;}
body{background:var(--navy);font-family:'Montserrat',sans-serif;color:var(--white);overflow-x:hidden;}
.card-wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--navy);position:relative;overflow:hidden;}
.bg-particles{position:fixed;inset:0;pointer-events:none;z-index:0;}
.particle{position:absolute;border-radius:50%;animation:particleFloat linear infinite;}
.particle:nth-child(1){width:300px;height:300px;background:radial-gradient(circle,rgba(201,168,76,.05),transparent);top:-100px;right:-100px;animation-duration:20s;}
.particle:nth-child(2){width:200px;height:200px;background:radial-gradient(circle,rgba(201,168,76,.04),transparent);bottom:200px;left:-80px;animation-duration:25s;animation-delay:5s;}
.particle:nth-child(3){width:150px;height:150px;background:radial-gradient(circle,rgba(201,168,76,.06),transparent);top:40%;right:10%;animation-duration:18s;animation-delay:3s;}
.particle:nth-child(4){width:100px;height:100px;background:radial-gradient(circle,rgba(232,201,109,.05),transparent);top:70%;left:5%;animation-duration:22s;animation-delay:8s;}
.particle:nth-child(5){width:250px;height:250px;background:radial-gradient(circle,rgba(201,168,76,.03),transparent);bottom:0;right:0;animation-duration:30s;animation-delay:2s;}
.particle:nth-child(6){width:80px;height:80px;background:radial-gradient(circle,rgba(245,230,163,.06),transparent);top:20%;left:15%;animation-duration:15s;animation-delay:6s;}
@keyframes particleFloat{0%,100%{transform:translateY(0) scale(1);}50%{transform:translateY(-30px) scale(1.05);}}
.deco-line{position:absolute;background:linear-gradient(90deg,transparent,rgba(201,168,76,.2),transparent);height:1px;left:0;right:0;pointer-events:none;}
.deco-line:nth-child(1){top:25%;}
.deco-line:nth-child(2){top:60%;}
.deco-line:nth-child(3){top:80%;}
.banner{position:relative;height:240px;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;transform:scale(1.05);animation:bannerZoom 20s ease-in-out infinite alternate;}
@keyframes bannerZoom{from{transform:scale(1.05);}to{transform:scale(1.12);}}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(13,27,42,.4),rgba(13,27,42,.85));}
.gold-bar{position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--gold),var(--gold2),var(--gold),transparent);}
.profile-section{padding:0 22px 20px;position:relative;z-index:5;}
.avatar-wrap{margin:-52px 0 14px;position:relative;display:inline-block;}
.avatar-wrap img{width:104px;height:104px;border-radius:50%;object-fit:cover;border:3px solid var(--gold);display:block;}
.avatar-ring{position:absolute;inset:-5px;border-radius:50%;background:conic-gradient(var(--gold),var(--gold2),var(--gold3),var(--gold));animation:ringRotate 6s linear infinite;z-index:-1;}
@keyframes ringRotate{to{transform:rotate(360deg);}}
.profile-name{font-family:'Playfair Display',serif;font-size:26px;font-weight:700;color:var(--white);margin-bottom:6px;line-height:1.2;}
.gold-divider{width:50px;height:2px;background:linear-gradient(90deg,var(--gold),var(--gold2));margin:8px 0;}
.profile-title{font-size:11px;letter-spacing:2.5px;text-transform:uppercase;color:var(--gold);margin-bottom:4px;}
.profile-company{font-size:13px;color:var(--text);margin-bottom:14px;}
.social-row{display:flex;gap:10px;flex-wrap:wrap;padding:4px 0 10px;}
.soc-btn{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:15px;text-decoration:none;background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.25);color:var(--gold);transition:all .3s;}
.soc-btn:hover{background:var(--gold);color:var(--navy);}
.bio-section{margin:0 22px 18px;padding:18px 20px;background:rgba(201,168,76,.06);border-left:3px solid var(--gold);border-radius:0 12px 12px 0;}
.bio-section p{font-size:13px;line-height:1.9;color:var(--text);}
.section{padding:0 22px 18px;}
.section-title{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:14px;display:flex;align-items:center;gap:10px;}
.section-title::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(201,168,76,.3),transparent);}
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.contact-card{display:flex;align-items:center;gap:10px;padding:13px 14px;background:rgba(255,255,255,.04);border:1px solid rgba(201,168,76,.15);border-radius:12px;text-decoration:none;color:var(--white);transition:all .3s;}
.contact-card:hover{background:rgba(201,168,76,.1);border-color:rgba(201,168,76,.4);transform:translateY(-2px);}
.contact-card-icon{width:34px;height:34px;border-radius:8px;background:rgba(201,168,76,.15);display:flex;align-items:center;justify-content:center;color:var(--gold);font-size:13px;flex-shrink:0;}
.contact-card-label{font-size:9px;color:var(--text);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;}
.contact-card-value{font-size:12px;font-weight:600;word-break:break-word;}
.services-scroll{display:flex;gap:12px;overflow-x:auto;padding:4px 2px 14px;scrollbar-width:none;}
.services-scroll::-webkit-scrollbar{display:none;}
.service-card{min-width:148px;background:rgba(255,255,255,.04);border:1px solid rgba(201,168,76,.15);border-radius:14px;overflow:hidden;flex-shrink:0;transition:all .3s;}
.service-card:hover{border-color:var(--gold);transform:translateY(-4px);}
.service-card img{width:100%;height:90px;object-fit:cover;filter:brightness(.7);}
.service-card-body{padding:10px 12px;}
.service-card-title{font-size:12px;font-weight:600;color:var(--white);margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.service-card-desc{font-size:11px;color:var(--text);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.qr-section{margin:0 22px 18px;padding:20px;background:rgba(201,168,76,.06);border:1px solid rgba(201,168,76,.2);border-radius:18px;display:flex;align-items:center;gap:18px;}
.qr-section img{width:88px;height:88px;border-radius:10px;background:#fff;padding:6px;flex-shrink:0;}
.qr-section h4{font-family:'Playfair Display',serif;font-size:16px;color:var(--gold);margin-bottom:5px;}
.qr-section p{font-size:12px;color:var(--text);line-height:1.6;}
.save-btn-wrap{padding:0 22px 18px;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:15px;background:linear-gradient(135deg,var(--gold),#a07830);color:var(--navy);font-size:13px;font-weight:700;border-radius:10px;text-decoration:none;letter-spacing:1px;transition:all .3s;cursor:pointer;border:none;}
.save-btn:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(201,168,76,.35);}
.share-section{padding:0 22px 20px;}
.share-row{display:flex;gap:8px;margin-bottom:10px;}
.share-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border-radius:10px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(201,168,76,.2);color:var(--text);background:rgba(255,255,255,.04);transition:all .3s;cursor:pointer;}
.share-btn:hover{border-color:var(--gold);color:var(--gold);}
.copy-row{display:flex;border:1px solid rgba(201,168,76,.2);border-radius:10px;overflow:hidden;background:rgba(255,255,255,.03);}
.copy-input{flex:1;background:transparent;border:none;color:var(--text);font-size:11px;padding:12px 14px;font-family:'Montserrat',sans-serif;outline:none;}
.copy-btn{background:var(--gold);color:var(--navy);border:none;padding:12px 18px;cursor:pointer;font-weight:700;font-size:12px;}
.card-footer{text-align:center;padding:16px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--text);border-top:1px solid rgba(201,168,76,.1);}
.card-footer a{color:var(--gold);text-decoration:none;font-weight:700;}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}
.fade-in-section.visible{opacity:1;transform:translateY(0);}
/* Services: big SQUARE carousel cards */
.svc-card{position:relative;display:block;border-radius:18px;overflow:hidden;text-decoration:none;color:var(--white);background:rgba(201,168,76,.06);border:1px solid rgba(201,168,76,.2);box-shadow:0 8px 24px rgba(0,0,0,.3);}
.svc-card img{width:100%;aspect-ratio:1/1;height:auto;object-fit:cover;display:block;transition:transform 6s ease;}
.tf-slide:hover .svc-card img{transform:scale(1.07);}
.svc-noimg{width:100%;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;background:rgba(201,168,76,.1);font-size:3rem;color:rgba(201,168,76,.4);}
.svc-info{position:absolute;left:0;right:0;bottom:0;padding:34px 16px 16px;background:linear-gradient(to top,rgba(13,27,42,.92),rgba(13,27,42,.4) 55%,transparent);}
.svc-name{font-size:16px;font-weight:700;line-height:1.25;color:var(--white);font-family:'Playfair Display',serif;}
</style>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
</head>
<body>
<div class="bg-particles">
  <div class="particle"></div><div class="particle"></div><div class="particle"></div>
  <div class="particle"></div><div class="particle"></div><div class="particle"></div>
</div>
<div class="card-wrap">
  <div class="deco-line"></div><div class="deco-line"></div><div class="deco-line"></div>

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
    <div class="gold-bar"></div>
  </div>

  <div class="profile-section">
    <div class="avatar-wrap">
      <div class="avatar-ring"></div>
      <img src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($fullName) ?>">
    </div>
    <div class="profile-name"><?= htmlspecialchars($fullName) ?></div>
    <div class="gold-divider"></div>
    <?php if (!empty($vcard['occupation'])): ?>
    <div class="profile-title"><?= htmlspecialchars($vcard['occupation']) ?></div>
    <?php endif; ?>
    <?php if (!empty($vcard['company'])): ?>
    <div class="profile-company"><?= htmlspecialchars($vcard['company']) ?></div>
    <?php endif; ?>
    <?php if (!empty($socialLinks)): ?>
    <div class="social-row">
      <?php foreach ($socialLinks as $sl):
        $platform = strtolower($sl['platform'] ?? '');
        $icon = $platformIcons[$platform] ?? 'fa-globe';
      ?>
      <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" class="soc-btn" title="<?= htmlspecialchars($sl['platform']) ?>">
        <i class="fab <?= $icon ?>"></i>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($vcard['description'])): ?>
  <div class="bio-section fade-in-section">
    <p><?= renderDescription($vcard['description']) ?></p>
  </div>
  <?php endif; ?>

  <div class="section fade-in-section">
    <div class="section-title">Contact</div>
    <div class="contact-grid">
      <?php if (!empty($vcard['email'])): ?>
      <a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="contact-card">
        <div class="contact-card-icon"><i class="fas fa-envelope"></i></div>
        <div><div class="contact-card-label">Email</div><div class="contact-card-value"><?= htmlspecialchars($vcard['email']) ?></div></div>
      </a>
      <?php endif; ?>
      <?php if (!empty($vcard['phone'])): ?>
      <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="contact-card">
        <div class="contact-card-icon"><i class="fas fa-phone"></i></div>
        <div><div class="contact-card-label">Phone</div><div class="contact-card-value"><?= htmlspecialchars($vcard['phone']) ?></div></div>
      </a>
      <?php endif; ?>
      <?php if (!empty($waPhone)): ?>
      <a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="contact-card">
        <div class="contact-card-icon"><i class="fab fa-whatsapp"></i></div>
        <div><div class="contact-card-label">WhatsApp</div><div class="contact-card-value"><?= htmlspecialchars($vcard['phone']) ?></div></div>
      </a>
      <?php endif; ?>
      <?php if (!empty($vcard['location'])): ?>
      <a href="<?= htmlspecialchars($locationUrl) ?>" target="_blank" class="contact-card">
        <div class="contact-card-icon"><i class="fas fa-map-marker-alt"></i></div>
        <div><div class="contact-card-label">Location</div><div class="contact-card-value"><?= htmlspecialchars($vcard['location']) ?></div></div>
      </a>
      <?php endif; ?>
    </div>
  </div>

  <?php if (!empty($services)): ?>
  <div class="section fade-in-section">
    <div class="section-title">Services</div>
    <div class="tf-carousel" data-carousel>
      <?php if (count($services) > 1): ?>
      <button class="tf-arrow tf-arrow-prev" type="button" data-carousel-prev aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
      <button class="tf-arrow tf-arrow-next" type="button" data-carousel-next aria-label="Next"><i class="fas fa-chevron-right"></i></button>
      <?php endif; ?>
      <div class="tf-track" data-carousel-track>
        <?php foreach ($services as $srv): ?>
        <div class="tf-slide">
          <?php if (!empty($srv['service_url'])): ?>
          <a href="<?= htmlspecialchars($srv['service_url']) ?>" target="_blank" class="svc-card">
          <?php else: ?>
          <div class="svc-card"<?= !empty($srv['image']) ? ' data-lightbox="'.htmlspecialchars(imgUrl($srv['image'])).'"' : '' ?> style="cursor:<?= !empty($srv['image']) ? 'zoom-in' : 'default' ?>;">
          <?php endif; ?>
            <?php if (!empty($srv['image'])): ?>
              <img src="<?= htmlspecialchars(imgUrl($srv['image'])) ?>" alt="<?= htmlspecialchars($srv['name'] ?? '') ?>" loading="lazy">
            <?php else: ?>
              <div class="svc-noimg"><i class="fas fa-briefcase"></i></div>
            <?php endif; ?>
            <div class="svc-info"><div class="svc-name"><?= htmlspecialchars($srv['name'] ?? '') ?></div></div>
            <?php if (empty($srv['service_url']) && !empty($srv['image'])): ?><div class="tf-gal-zoom"><i class="fas fa-expand"></i></div><?php endif; ?>
          <?php if (!empty($srv['service_url'])): ?></a><?php else: ?></div><?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (count($services) > 1): ?><div class="tf-dots" data-carousel-dots></div><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

<?php include __DIR__ . '/_features.php'; ?>

  <div class="qr-section fade-in-section">
    <img src="<?= $qrUrl ?>" alt="QR Code">
    <div><h4>Scan &amp; Connect</h4><p>Scan to save contact or share this card instantly.</p></div>
  </div>

  <div class="save-btn-wrap fade-in-section">
    <button class="save-btn" onclick="saveContact()"><i class="fas fa-address-card"></i> Save Contact</button>
  </div>

  <div class="share-section fade-in-section">
    <div class="share-row">
      <?php if (!empty($waPhone)): ?>
      <a href="https://wa.me/?text=<?= urlencode($cardUrl) ?>" target="_blank" class="share-btn"><i class="fab fa-whatsapp"></i> Share</a>
      <?php endif; ?>
      <button class="share-btn" onclick="shareCard()"><i class="fas fa-share-alt"></i> Share</button>
    </div>
    <div class="copy-row">
      <input class="copy-input" id="cardUrlInput" value="<?= htmlspecialchars($cardUrl) ?>" readonly>
      <button class="copy-btn" onclick="navigator.clipboard.writeText(document.getElementById('cardUrlInput').value);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)">Copy</button>
    </div>
  </div>

  <?php if (empty($vcard['remove_branding'])): ?>
  <div class="card-footer">Powered by <a href="https://tapify.in" target="_blank">Tapify</a></div>
  <?php endif; ?>
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
