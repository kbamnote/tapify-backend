<?php
/**
 * Tapify vCard Template: vcard17
 * Wedding Planner — Rose/Gold/Cream theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://tapify-backend-production.up.railway.app/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1519741497674-611481863552?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['job_title'] ?? 'Wedding Planner') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Lato:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#fdf8fc;--white:#fff;--rose:#c9607a;--rose2:#e8849a;--gold:#c9a84c;--cream:#fef0f4;--text:#4a2535;--sub:#a07080;--border:#f0d8e0;}
body{background:var(--bg);font-family:'Lato',sans-serif;color:var(--text);}
.wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--white);overflow:hidden;box-shadow:0 0 60px rgba(201,96,122,.08);}
@media(min-width:600px){
.desk{position:fixed;inset:0;pointer-events:none;z-index:0;}
.heart{position:fixed;opacity:.04;animation:hFloat ease-in-out infinite alternate;color:var(--rose);}
.h1{font-size:80px;top:5%;right:5%;animation-duration:8s;}
.h2{font-size:50px;bottom:20%;left:3%;animation-duration:11s;animation-delay:3s;}
.h3{font-size:30px;top:40%;left:80%;animation-duration:9s;animation-delay:5s;}
@keyframes hFloat{0%{transform:translateY(0) rotate(-10deg);}100%{transform:translateY(-20px) rotate(10deg);}}
.petals-desk{position:fixed;inset:0;}
.petal{position:absolute;width:8px;height:10px;background:var(--rose2);border-radius:50% 0 50% 0;opacity:.06;animation:petalFall linear infinite;}
.petal:nth-child(1){left:10%;animation-duration:10s;top:-20px;}
.petal:nth-child(2){left:30%;animation-duration:13s;top:-20px;animation-delay:3s;}
.petal:nth-child(3){left:60%;animation-duration:9s;top:-20px;animation-delay:6s;}
.petal:nth-child(4){left:80%;animation-duration:11s;top:-20px;animation-delay:1.5s;}
@keyframes petalFall{0%{transform:translateY(0) rotate(0deg);opacity:0;}10%{opacity:.08;}90%{opacity:.06;}100%{transform:translateY(100vh) rotate(360deg);opacity:0;}}}
.banner{height:250px;position:relative;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;filter:brightness(.75) saturate(1.15);}
.b-over{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(74,37,53,.1),rgba(74,37,53,.75));}
.b-content{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;padding:24px;text-align:center;}
.b-brand{font-family:'Great Vibes',cursive;font-size:44px;color:#fff;text-shadow:0 2px 20px rgba(201,96,122,.5);margin-bottom:4px;}
.b-tag{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--rose2);}
.b-bar{position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--rose),var(--gold),var(--rose),transparent);}
.prof{padding:0 22px 20px;text-align:center;background:var(--white);}
.av{width:100px;height:100px;margin:-50px auto 14px;position:relative;}
.av img{width:100%;height:100%;border-radius:50%;object-fit:cover;border:4px solid var(--white);box-shadow:0 4px 20px rgba(201,96,122,.3);}
.av::after{content:'💍';position:absolute;bottom:0;right:-4px;font-size:20px;}
.name{font-family:'Great Vibes',cursive;font-size:34px;color:var(--rose);margin-bottom:6px;}
.title{font-size:11px;letter-spacing:2.5px;text-transform:uppercase;color:var(--sub);margin-bottom:3px;}
.co{font-size:13px;color:var(--text);margin-bottom:4px;}
.tagline{font-style:italic;font-size:14px;color:var(--sub);margin-bottom:14px;}
.soc-row{display:flex;justify-content:center;gap:10px;margin-bottom:10px;}
.soc{width:38px;height:38px;border-radius:50%;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--sub);font-size:15px;text-decoration:none;transition:all .3s;}
.soc:hover{background:var(--rose);border-color:var(--rose);color:#fff;}
.web{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--rose);text-decoration:none;}
.bio{margin:0 22px 20px;padding:18px;background:var(--cream);border-radius:16px;border:1px solid var(--border);text-align:center;}
.bio p{font-size:13px;line-height:1.9;color:var(--text);}
.sec{padding:0 22px;margin-bottom:20px;}
.sec-h{font-family:'Great Vibes',cursive;font-size:24px;color:var(--rose);margin-bottom:14px;display:flex;align-items:center;gap:10px;}
.sec-h::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(201,96,122,.3),transparent);}
.cl{display:flex;flex-direction:column;gap:8px;}
.ci{display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--cream);border-radius:12px;border:1px solid var(--border);text-decoration:none;color:var(--text);transition:all .3s;}
.ci:hover{background:var(--white);border-color:var(--rose2);transform:translateX(4px);}
.ci-ic{width:34px;height:34px;border-radius:50%;background:rgba(201,96,122,.12);display:flex;align-items:center;justify-content:center;color:var(--rose);font-size:13px;flex-shrink:0;}
.ci-l{font-size:10px;color:var(--sub);margin-bottom:1px;}.ci-v{font-size:12px;font-weight:600;word-break:break-word;}
.car-wrap{position:relative;overflow:hidden;margin-bottom:20px;}
.car-track{display:flex;gap:12px;padding:4px 22px;transition:transform .5s cubic-bezier(.25,.46,.45,.94);}
.c-card{min-width:155px;flex-shrink:0;border-radius:16px;overflow:hidden;background:var(--cream);border:1px solid var(--border);transition:all .3s;}
.c-card:hover{transform:translateY(-4px);box-shadow:0 10px 24px rgba(201,96,122,.15);}
.c-card img{width:100%;height:100px;object-fit:cover;}
.c-body{padding:10px 12px;}
.c-title{font-size:12px;font-weight:700;color:var(--rose);margin-bottom:3px;}
.c-desc{font-size:11px;color:var(--sub);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.c-dots{display:flex;justify-content:center;gap:6px;padding:8px 0;}
.dot{width:6px;height:6px;border-radius:50%;background:var(--border);transition:all .3s;cursor:pointer;}
.dot.active{background:var(--rose);width:18px;border-radius:3px;}
.c-arrows{position:absolute;top:42%;width:100%;display:flex;justify-content:space-between;padding:0 8px;pointer-events:none;}
.c-arr{width:28px;height:28px;background:rgba(255,255,255,.9);border:1px solid var(--border);color:var(--rose);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;pointer-events:all;font-size:11px;transition:all .3s;}
.c-arr:hover{background:var(--rose);color:#fff;border-color:var(--rose);}
.qr-blk{margin:0 22px 20px;background:linear-gradient(135deg,var(--rose),#a0405a);border-radius:20px;padding:22px;display:flex;align-items:center;gap:20px;}
.qr-box{background:#fff;padding:6px;width:88px;height:88px;flex-shrink:0;border-radius:10px;}
.qr-box img{width:100%;height:100%;display:block;}
.qr-info h4{font-family:'Great Vibes',cursive;font-size:22px;color:#fff;margin-bottom:4px;}
.qr-info p{font-size:12px;color:rgba(255,255,255,.85);line-height:1.6;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 44px);margin:0 22px 20px;padding:14px;background:linear-gradient(135deg,var(--rose),#a0405a);color:#fff;font-size:12px;font-weight:700;border-radius:30px;cursor:pointer;border:none;transition:all .3s;}
.save-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(201,96,122,.4);}
.share{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border-radius:12px;border:1px solid var(--border);color:var(--sub);font-size:12px;text-decoration:none;transition:all .3s;cursor:pointer;background:transparent;}
.sh:hover{background:var(--cream);border-color:var(--rose2);color:var(--rose);}
.copy-row{display:flex;border:1px solid var(--border);border-radius:12px;overflow:hidden;background:var(--cream);}
.copy-inp{flex:1;background:transparent;border:none;color:var(--sub);font-size:11px;padding:10px 12px;outline:none;}
.cp-btn{background:var(--rose);color:#fff;border:none;padding:10px 16px;cursor:pointer;font-weight:700;font-size:11px;}
.footer{text-align:center;padding:14px;font-size:10px;letter-spacing:2px;color:var(--sub);}
.footer span{color:var(--rose);}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}.fade-in-section.visible{opacity:1;transform:none;}
.gal-grid{display:grid;grid-template-columns:repeat(auto-fill, minmax(120px, 1fr));gap:10px;padding:0 22px 20px;}
.gal-item{width:100%;height:120px;border-radius:12px;overflow:hidden;border:1px solid var(--border);}
.gal-item img{width:100%;height:100%;object-fit:cover;transition:transform 0.3s;}
.gal-item:hover img{transform:scale(1.05);}
/* ── Inquiry & Appointment Forms ── */
.v17-form-sec{padding:0 22px 22px;}
.v17-form-title{font-size:10px;letter-spacing:2.5px;text-transform:uppercase;font-weight:700;color:var(--rose);margin-bottom:14px;display:flex;align-items:center;gap:10px;}
.v17-form-title::after{content:'';flex:1;height:1px;background:var(--border);}
.v17-form-title i{font-size:12px;}
.v17-form{display:flex;flex-direction:column;gap:12px;}
.v17-fg{display:flex;flex-direction:column;gap:5px;}
.v17-fg label{font-size:11px;font-weight:600;color:var(--sub);}
.v17-fg input,.v17-fg textarea,.v17-fg select{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:inherit;font-size:13px;background:var(--cream);color:var(--text);outline:none;transition:border-color .2s;}
.v17-fg input:focus,.v17-fg textarea:focus,.v17-fg select:focus{border-color:var(--rose2);}
.v17-submit-btn{width:100%;padding:13px;border:none;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;background:linear-gradient(135deg,var(--rose),var(--rose2));color:#fff;letter-spacing:.5px;transition:opacity .2s;}
.v17-submit-btn:hover{opacity:.88;}
.v17-submit-btn:disabled{opacity:.5;cursor:not-allowed;}
</style>
</head>
<body>
<div class="desk"><div class="heart h1"><i class="fas fa-heart"></i></div><div class="heart h2"><i class="fas fa-heart"></i></div><div class="heart h3"><i class="fas fa-heart"></i></div><div class="petals-desk"><div class="petal"></div><div class="petal"></div><div class="petal"></div><div class="petal"></div></div></div>
<div class="wrap">
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
    <div class="b-over"></div>
    <div class="b-content">
      <div class="b-brand"><?= htmlspecialchars($vcard['company'] ?? $fullName) ?></div>
      <div class="b-tag"><?= htmlspecialchars($vcard['job_title'] ?? 'Luxury Wedding Planning') ?></div>
    </div>
    <div class="b-bar"></div>
  </div>
  <div class="prof">
    <div class="av"><img src="<?= $profileImg ?>" alt="<?= htmlspecialchars($fullName) ?>"></div>
    <div class="name"><?= htmlspecialchars($fullName) ?></div>
    <div class="title"><?= htmlspecialchars($vcard['job_title'] ?? '') ?></div>
    <?php if (!empty($vcard['company']) || !empty($vcard['location'])): ?>
    <div class="co"><?= htmlspecialchars(implode(' · ', array_filter([$vcard['company'] ?? '', $vcard['location'] ?? '']))) ?></div>
    <?php endif; ?>
    <?php if (!empty($vcard['tagline'])): ?>
    <div class="tagline">"<?= htmlspecialchars($vcard['tagline']) ?>"</div>
    <?php endif; ?>
    <?php if (!empty($socialLinks)): ?>
    <div class="soc-row">
      <?php foreach ($socialLinks as $s): $icon = $platformIcons[$s['platform']] ?? 'fa-globe'; ?>
      <a href="<?= htmlspecialchars($s['url']) ?>" class="soc" target="_blank" rel="noopener"><i class="fab <?= $icon ?>"></i></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($vcard['website'])): ?>
    <a href="<?= htmlspecialchars($vcard['website']) ?>" class="web" target="_blank" rel="noopener"><i class="fas fa-globe"></i> <?= htmlspecialchars(preg_replace('#^https?://#','',$vcard['website'])) ?></a>
    <?php endif; ?>
  </div>
  <?php if (!empty($vcard['bio'])): ?>
  <div class="bio fade-in-section"><p><?= nl2br(htmlspecialchars($vcard['bio'])) ?></p></div>
  <?php endif; ?>
  <div class="sec fade-in-section">
    <div class="sec-h">Contact</div>
    <div class="cl">
      <?php if (!empty($vcard['email'])): ?>
      <a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="ci"><div class="ci-ic"><i class="fas fa-envelope"></i></div><div><div class="ci-l">Email</div><div class="ci-v"><?= htmlspecialchars($vcard['email']) ?></div></div></a>
      <?php endif; ?>
      <?php if (!empty($vcard['phone'])): ?>
      <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="ci"><div class="ci-ic"><i class="fas fa-phone"></i></div><div><div class="ci-l">Phone</div><div class="ci-v"><?= htmlspecialchars($vcard['phone']) ?></div></div></a>
      <?php endif; ?>
      <?php if (!empty($waPhone)): ?>
      <a href="https://wa.me/<?= $waPhone ?>" class="ci" target="_blank" rel="noopener"><div class="ci-ic"><i class="fab fa-whatsapp"></i></div><div><div class="ci-l">WhatsApp</div><div class="ci-v"><?= htmlspecialchars($vcard['phone']) ?></div></div></a>
      <?php endif; ?>
      <?php if (!empty($vcard['location'])): ?>
      <a href="<?= $locationUrl ?>" class="ci" target="_blank" rel="noopener"><div class="ci-ic"><i class="fas fa-map-marker-alt"></i></div><div><div class="ci-l">Office</div><div class="ci-v"><?= htmlspecialchars($vcard['location']) ?></div></div></a>
      <?php endif; ?>
      <?php if (!empty($vcard['website'])): ?>
      <a href="<?= htmlspecialchars($vcard['website']) ?>" class="ci" target="_blank" rel="noopener"><div class="ci-ic"><i class="fas fa-globe"></i></div><div><div class="ci-l">Website</div><div class="ci-v"><?= htmlspecialchars(preg_replace('#^https?://#','',$vcard['website'])) ?></div></div></a>
      <?php endif; ?>
    </div>
  </div>
  <?php if (!empty($services)): ?>
  <div class="sec fade-in-section"><div class="sec-h">Services</div></div>
  <div class="car-wrap fade-in-section" id="carousel17">
    <div class="car-track" id="carousel17-track">
      <?php foreach ($services as $srv): ?>
      <div class="c-card">
        <?php if (!empty($srv['image'])): ?><img src="<?= imgUrl($srv['image']) ?>" alt="<?= htmlspecialchars($srv['name']) ?>"><?php endif; ?>
        <div class="c-body">
          <div class="c-title"><?= htmlspecialchars($srv['name']) ?></div>
          <?php if (!empty($srv['description'])): ?><div class="c-desc"><?= htmlspecialchars($srv['description']) ?></div><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="c-arrows"><div class="c-arr" id="carousel17-prev"><i class="fas fa-chevron-left"></i></div><div class="c-arr" id="carousel17-next"><i class="fas fa-chevron-right"></i></div></div>
    <div class="c-dots" id="carousel17-dots"></div>
  </div>
  <?php endif; ?>
  <?php if (!empty($products)): ?>
  <div class="sec fade-in-section"><div class="sec-h">Products</div></div>
  <div class="car-wrap fade-in-section" id="carousel17_prod">
    <div class="car-track" id="carousel17_prod-track">
      <?php foreach ($products as $prd): ?>
      <div class="c-card">
        <?php if (!empty($prd['image'])): ?><img src="<?= imgUrl($prd['image']) ?>" alt="<?= htmlspecialchars($prd['name']) ?>"><?php endif; ?>
        <div class="c-body">
          <div class="c-title"><?= htmlspecialchars($prd['name']) ?></div>
          <?php if ($prd['price'] !== null): ?><div style="font-size:11px;font-weight:700;color:var(--rose2);margin-bottom:2px;"><?= htmlspecialchars($prd['currency'] ?: 'INR') ?> <?= number_format((float)$prd['price'], 2) ?></div><?php endif; ?>
          <?php if (!empty($prd['description'])): ?><div class="c-desc"><?= htmlspecialchars($prd['description']) ?></div><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="c-arrows"><div class="c-arr" id="carousel17_prod-prev"><i class="fas fa-chevron-left"></i></div><div class="c-arr" id="carousel17_prod-next"><i class="fas fa-chevron-right"></i></div></div>
    <div class="c-dots" id="carousel17_prod-dots"></div>
  </div>
  <?php if (!empty($storeUrl)): ?>
  <div style="padding:0 22px 18px;">
    <a href="<?= htmlspecialchars($storeUrl) ?>" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 20px;border-radius:12px;background:var(--cream);border:1.5px solid var(--border);text-decoration:none;color:var(--rose);font-size:13px;font-weight:700;transition:background .2s;">
      <i class="fas fa-store"></i> View More Products
    </a>
  </div>
  <?php endif; ?>
  <?php endif; ?>
  <?php if (!empty($galleries)): ?>
  <div class="sec fade-in-section"><div class="sec-h">Gallery</div></div>
  <div class="gal-wrap fade-in-section">
    <?php foreach ($galleries as $g): ?>
      <?php if (!empty($g['images'])): ?>
      <div class="gal-grid">
        <?php foreach ($g['images'] as $img): ?>
        <div class="gal-item"><a href="<?= imgUrl($img['image_url']) ?>" target="_blank"><img src="<?= imgUrl($img['image_url']) ?>" alt="Gallery Image" loading="lazy"></a></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <?php if (!empty($testimonials)): ?>
  <div class="sec fade-in-section"><div class="sec-h">Testimonials</div></div>
  <div class="car-wrap fade-in-section" id="carousel17_test">
    <div class="car-track" id="carousel17_test-track">
      <?php foreach ($testimonials as $t): ?>
      <div class="c-card" style="padding:16px;background:var(--cream);border-color:var(--rose2);">
        <div style="color:#f59e0b;font-size:12px;margin-bottom:8px;"><?= str_repeat('★', $t['rating']) . str_repeat('☆', 5 - $t['rating']) ?></div>
        <div style="font-size:13px;font-style:italic;color:var(--text);margin-bottom:12px;line-height:1.5;">"<?= htmlspecialchars($t['message']) ?>"</div>
        <div style="font-size:11px;font-weight:700;color:var(--rose);"><?= htmlspecialchars($t['name']) ?></div>
        <?php if (!empty($t['designation'])): ?><div style="font-size:9px;color:var(--sub);"><?= htmlspecialchars($t['designation']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="c-arrows"><div class="c-arr" id="carousel17_test-prev"><i class="fas fa-chevron-left"></i></div><div class="c-arr" id="carousel17_test-next"><i class="fas fa-chevron-right"></i></div></div>
    <div class="c-dots" id="carousel17_test-dots"></div>
  </div>
  <?php endif; ?>
  <?php
  $v17_insta = [];
  foreach ($insta_feed as $_i) {
      if (!empty($_i['embed_url']) && preg_match('#^https?://#i', $_i['embed_url'])) {
          $v17_insta[] = ['type' => 'iframe', 'src' => $_i['embed_url']];
      } elseif (!empty($_i['tag'])) {
          if (preg_match('#https?://(?:www\.)?instagram\.com/(p|reel|tv)/([A-Za-z0-9_-]+)#', $_i['tag'], $_m)) {
              $v17_insta[] = ['type' => 'iframe', 'src' => 'https://www.instagram.com/' . $_m[1] . '/' . $_m[2] . '/embed/'];
          } elseif (strlen($_i['tag']) > 20) {
              $v17_insta[] = ['type' => 'html', 'html' => $_i['tag']];
          }
      }
  }
  if (!empty($v17_insta)):
  ?>
  <div class="sec fade-in-section"><div class="sec-h">Instagram Feed</div></div>
  <div class="fade-in-section" style="padding:0 22px;">
    <?php foreach ($v17_insta as $_item): ?>
      <?php if ($_item['type'] === 'iframe'): ?>
      <div style="border-radius:12px;overflow:hidden;margin-bottom:10px;height:300px;border:1px solid var(--border);">
          <iframe src="<?= htmlspecialchars($_item['src']) ?>" width="100%" height="300" frameborder="0" scrolling="no" allowtransparency="true" loading="lazy" style="display:block;"></iframe>
      </div>
      <?php else: ?>
      <div style="margin-bottom:10px;"><?= $_item['html'] ?></div>
      <?php endif; ?>
    <?php endforeach; ?>
    <?php if (!empty(array_filter($v17_insta, fn($i) => $i['type'] === 'html'))): ?>
    <script async src="https://www.instagram.com/embed.js"></script>
    <?php endif; ?>
  </div>
  <?php endif; ?>
  <?php
  $v17_iframes = array_filter($iframes, fn($fr) => !empty($fr['url']) && preg_match('#^https?://#i', $fr['url']));
  if (!empty($v17_iframes)):
  ?>
  <div class="sec fade-in-section"><div class="sec-h">Embedded Content</div></div>
  <div class="fade-in-section" style="padding:0 22px;">
    <?php foreach ($v17_iframes as $fr): ?>
    <div style="border-radius:12px;overflow:hidden;margin-bottom:12px;background:var(--cream);border:1px solid var(--border);">
      <iframe src="<?= htmlspecialchars($fr['url']) ?>" width="100%" height="320" frameborder="0" allowfullscreen loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" style="display:block;"></iframe>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <?php if (!empty($socialLinks)): ?>
  <div class="v17-form-sec fade-in-section">
    <div class="v17-form-title"><i class="fas fa-share-nodes"></i> Connect</div>
    <div style="display:flex;flex-wrap:wrap;gap:10px;">
      <?php
      $v17IconMap=['Facebook'=>'fa-facebook-f','Instagram'=>'fa-instagram','Twitter'=>'fa-twitter','X'=>'fa-x-twitter','LinkedIn'=>'fa-linkedin-in','WhatsApp'=>'fa-whatsapp','YouTube'=>'fa-youtube','TikTok'=>'fa-tiktok','Pinterest'=>'fa-pinterest-p','Snapchat'=>'fa-snapchat-ghost','GitHub'=>'fa-github','Spotify'=>'fa-spotify','Behance'=>'fa-behance','Dribbble'=>'fa-dribbble'];
      $v17BgMap=['Facebook'=>'#1877F2','Instagram'=>'radial-gradient(circle at 30% 107%,#fdf497,#fd5949 45%,#d6249f 60%,#285AEB 90%)','Twitter'=>'#000','X'=>'#000','LinkedIn'=>'#0A66C2','WhatsApp'=>'#25D366','YouTube'=>'#FF0000','TikTok'=>'#010101','Pinterest'=>'#E60023','Snapchat'=>'#FFFC00','GitHub'=>'#333','Spotify'=>'#1DB954','Behance'=>'#1769FF','Dribbble'=>'#EA4C89'];
      foreach ($socialLinks as $sl):
        $v17Ic = $v17IconMap[$sl['platform']] ?? 'fa-globe';
        $v17Bg = $v17BgMap[$sl['platform']] ?? 'var(--rose)';
        $v17Color = $sl['platform'] === 'Snapchat' ? '#000' : '#fff';
      ?>
      <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" title="<?= htmlspecialchars($sl['platform']) ?>" style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:17px;text-decoration:none;color:<?= $v17Color ?>;background:<?= $v17Bg ?>;transition:transform .25s;">
        <i class="fab <?= $v17Ic ?>"></i>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if (($vcard['display_inquiry_form'] ?? 1)): ?>
  <div class="v17-form-sec fade-in-section">
    <div class="v17-form-title"><i class="fas fa-paper-plane"></i> Get in Touch</div>
    <form class="v17-form" onsubmit="submitInquiry(event)">
      <input type="hidden" name="vcard_id" value="<?= $vcardId ?>">
      <div class="v17-fg"><label>Name *</label><input type="text" name="name" required placeholder="Your name"></div>
      <div class="v17-fg"><label>Email *</label><input type="email" name="email" required placeholder="you@example.com"></div>
      <div class="v17-fg"><label>Phone</label><input type="tel" name="phone" placeholder="+91 9876543210"></div>
      <div class="v17-fg"><label>Message *</label><textarea name="message" rows="3" required placeholder="Your message..."></textarea></div>
      <button type="submit" class="v17-submit-btn"><i class="fas fa-paper-plane"></i> Send Message</button>
    </form>
  </div>
  <?php endif; ?>

  <?php if (($vcard['show_appointments'] ?? 1)): ?>
  <div class="v17-form-sec fade-in-section">
    <div class="v17-form-title"><i class="fas fa-calendar-check"></i> Book Appointment</div>
    <form class="v17-form" onsubmit="submitAppointment(event)">
      <input type="hidden" name="vcard_id" value="<?= $vcardId ?>">
      <div class="v17-fg"><label>Your Name *</label><input type="text" name="name" required placeholder="Your name"></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div class="v17-fg"><label>Phone *</label><input type="tel" name="phone" required placeholder="9876543210"></div>
        <div class="v17-fg"><label>Email</label><input type="email" name="email" placeholder="optional"></div>
      </div>
      <?php if (!empty($services)): ?>
      <div class="v17-fg">
        <label>Service</label>
        <select name="service">
          <option value="">Choose a service</option>
          <?php foreach ($services as $s): ?>
            <option value="<?= htmlspecialchars($s['name']) ?>"><?= htmlspecialchars($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php else: ?>
      <div class="v17-fg"><label>Service / Reason</label><input type="text" name="service" placeholder="What service do you need?"></div>
      <?php endif; ?>
      <div class="v17-fg"><label>Date *</label><input type="date" name="date" id="appointment-date" required min="<?= date('Y-m-d') ?>" onchange="fetchAvailableSlots(this.value, <?= $vcardId ?>)"></div>
      <div class="v17-fg" id="time-container" style="display:none;">
        <label id="time-label">Available Times *</label>
        <select name="time" id="appointment-time" required disabled><option value="">Select date first</option></select>
      </div>
      <div class="v17-fg"><label>Notes</label><textarea name="notes" rows="2" placeholder="Any specific requirements..."></textarea></div>
      <button type="submit" class="v17-submit-btn"><i class="fas fa-calendar-plus"></i> Book Appointment</button>
    </form>
  </div>
  <?php endif; ?>

  <div class="qr-blk fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code"></div>
    <div class="qr-info"><h4>Plan Your Day</h4><p>Scan to book a free wedding consultation.</p></div>
  </div>
  <button class="save-btn fade-in-section" onclick="saveContact()"><i class="fas fa-heart"></i>&nbsp; Save Contact</button>
  <div class="share fade-in-section">
    <div class="sh-row">
      <button class="sh" onclick="shareCard('whatsapp')"><i class="fab fa-whatsapp"></i> Share</button>
      <button class="sh" onclick="shareCard('facebook')"><i class="fab fa-facebook-f"></i> Share</button>
      <button class="sh" onclick="shareCard('native')"><i class="fas fa-share-alt"></i> Share</button>
    </div>
    <div class="copy-row"><input class="copy-inp" id="cardUrlInput" value="<?= htmlspecialchars($cardUrl) ?>" readonly><button class="cp-btn" onclick="navigator.clipboard.writeText(document.getElementById('cardUrlInput').value);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)">Copy</button></div>
  </div>
  <?php if (empty($vcard['remove_branding'])): ?>
  <div class="footer">Powered by <span>Tapify</span> · Wedding Edition</div>
  <?php endif; ?>
  <div style="text-align:center;font-size:0.7rem;padding:0 16px 14px;color:inherit;opacity:0.6;">A unit of <strong>Mr Print World</strong></div>
</div>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
<script>
const _obs=new IntersectionObserver(e=>{e.forEach(el=>{if(el.isIntersecting)el.target.classList.add('visible');});},{threshold:0.1});
document.querySelectorAll('.fade-in-section').forEach(el=>_obs.observe(el));
function initCarousel(id){
  const wrap=document.getElementById(id);if(!wrap)return;
  const track=document.getElementById(id+'-track');const dotsEl=document.getElementById(id+'-dots');
  if(!track)return;const cards=track.querySelectorAll('.c-card');const total=cards.length;if(total===0)return;
  let idx=0;const cardW=155,gap=12;
  if(dotsEl){dotsEl.innerHTML='';cards.forEach((_,i)=>{const d=document.createElement('div');d.className='dot'+(i===0?' active':'');d.onclick=()=>goTo(i);dotsEl.appendChild(d);});}
  function goTo(n){idx=(n+total)%total;track.style.transform=`translateX(calc(-${idx}*(${cardW}px + ${gap}px)))`;if(dotsEl)dotsEl.querySelectorAll('.dot').forEach((d,i)=>d.classList.toggle('active',i===idx));}
  const prev=document.getElementById(id+'-prev');const next=document.getElementById(id+'-next');
  if(prev)prev.onclick=()=>goTo(idx-1);if(next)next.onclick=()=>goTo(idx+1);
  setInterval(()=>goTo(idx+1),4000);
}
initCarousel('carousel17');
initCarousel('carousel17_prod');
initCarousel('carousel17_test');
</script>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
