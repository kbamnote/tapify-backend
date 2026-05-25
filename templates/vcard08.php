<?php
/**
 * Tapify vCard Template: vcard08
 * Lawyer/Legal — Navy/Silver/Gold theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://'.$_SERVER['HTTP_HOST'].'/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['occupation'] ?? 'Digital Card') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#f4f1eb;--white:#ffffff;--navy:#1a2744;--gold:#c9a84c;--silver:#8090a8;--dark:#0d1a2e;--text:#4a5568;--sub:#718096;--border:#d4cfc4;--card:#fafaf8;}
body{background:var(--bg);font-family:'Lato',sans-serif;color:var(--text);overflow-x:hidden;}
.texture{position:fixed;inset:0;background-image:url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z' fill='%231a2744' fill-opacity='0.02'/%3E%3C/svg%3E");pointer-events:none;z-index:0;}
.card-wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--white);box-shadow:0 0 60px rgba(26,39,68,.08);overflow:hidden;position:relative;z-index:1;}
.banner{position:relative;height:240px;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;filter:brightness(.4) saturate(.7);}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(26,39,68,.5),rgba(13,26,46,.9));}
.banner-seal{position:absolute;top:16px;right:16px;width:56px;height:56px;opacity:.7;animation:sealSpin 20s linear infinite;}
@keyframes sealSpin{from{transform:rotate(0);}to{transform:rotate(360deg);}}
.banner-content{position:absolute;bottom:20px;left:20px;right:20px;}
.firm-name{font-size:11px;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:6px;}
.banner-title-text{font-family:'Libre Baskerville',serif;font-size:26px;color:#fff;line-height:1.3;}
.gold-rule-banner{position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent);}
.profile-area{padding:0 22px 18px;position:relative;z-index:5;background:var(--white);}
.profile-top{display:flex;align-items:flex-end;justify-content:space-between;margin-top:-48px;margin-bottom:14px;}
.av-wrap{position:relative;width:96px;height:96px;}
.av-wrap img{width:100%;height:100%;border-radius:50%;object-fit:cover;border:3px solid var(--gold);box-shadow:0 4px 20px rgba(26,39,68,.2);}
.consult-btn{display:flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--navy),var(--dark));color:#fff;font-size:11px;font-weight:700;padding:11px 18px;border-radius:4px;text-decoration:none;letter-spacing:1px;transition:all .3s;}
.consult-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(26,39,68,.4);}
.lawyer-name{font-family:'Libre Baskerville',serif;font-size:24px;color:var(--dark);margin-bottom:4px;}
.rule{width:50px;height:1px;background:var(--gold);margin:6px 0;}
.lawyer-title{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--silver);margin-bottom:3px;}
.lawyer-firm{font-size:13px;color:var(--text);margin-bottom:10px;}
.bar-badges{display:flex;gap:8px;flex-wrap:wrap;margin:10px 0;}
.bar-badge{font-size:10px;padding:4px 12px;border:1px solid rgba(201,168,76,.4);color:var(--gold);background:rgba(201,168,76,.06);border-radius:3px;font-weight:700;letter-spacing:.5px;}
.social-strip{display:flex;gap:8px;padding:8px 0;}
.soc{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;text-decoration:none;background:rgba(26,39,68,.06);color:var(--navy);transition:all .3s;}
.soc:hover{background:var(--navy);color:#fff;}
.about-block{margin:0 22px 18px;padding:18px 20px;background:var(--card);border:1px solid var(--border);border-left:3px solid var(--gold);border-radius:0 12px 12px 0;}
.about-block p{font-size:13px;line-height:1.9;color:var(--text);}
.section{padding:0 22px 18px;}
.sec-h{font-family:'Libre Baskerville',serif;font-size:14px;color:var(--navy);margin-bottom:14px;display:flex;align-items:center;gap:10px;}
.sec-h::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--gold),transparent);}
.contact-list{display:flex;flex-direction:column;gap:10px;}
.ci{display:flex;align-items:center;gap:12px;padding:13px 16px;background:var(--card);border:1px solid var(--border);border-radius:12px;text-decoration:none;color:var(--text);transition:all .3s;}
.ci:hover{background:#f5f0e8;border-color:var(--gold);}
.ci-ico{width:36px;height:36px;border-radius:8px;background:var(--navy);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;flex-shrink:0;}
.ci-lbl{font-size:9px;color:var(--sub);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;}
.ci-val{font-size:13px;font-weight:600;}
.practice-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.prac{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px;transition:all .3s;}
.prac:hover{border-color:var(--gold);transform:translateY(-2px);}
.prac-icon{font-size:22px;margin-bottom:8px;}
.prac-title{font-family:'Libre Baskerville',serif;font-size:13px;color:var(--dark);margin-bottom:4px;}
.prac-desc{font-size:11px;color:var(--sub);line-height:1.5;}
.qr-block{margin:0 22px 18px;background:linear-gradient(135deg,var(--dark),var(--navy));border-radius:16px;padding:22px;display:flex;align-items:center;gap:18px;}
.qr-box{background:#fff;padding:8px;border-radius:8px;width:88px;height:88px;flex-shrink:0;}
.qr-box img{width:72px;height:72px;}
.qr-txt{color:#fff;}
.qr-txt h4{font-family:'Libre Baskerville',serif;font-size:16px;margin-bottom:5px;}
.qr-txt p{font-size:12px;opacity:.85;line-height:1.6;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 44px);margin:0 22px 18px;padding:15px;background:linear-gradient(135deg,var(--navy),var(--dark));color:#fff;font-size:13px;font-weight:700;border-radius:6px;border:none;cursor:pointer;letter-spacing:1px;transition:all .3s;}
.save-btn:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(26,39,68,.4);}
.share-area{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border-radius:10px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid var(--border);color:var(--sub);background:var(--card);transition:all .3s;cursor:pointer;}
.sh:hover{border-color:var(--navy);color:var(--navy);}
.copy-row{display:flex;border:1px solid var(--border);border-radius:10px;overflow:hidden;background:var(--card);}
.copy-inp{flex:1;background:transparent;border:none;color:var(--sub);font-size:11px;padding:12px 14px;outline:none;}
.cp-btn{background:var(--navy);color:#fff;border:none;padding:12px 18px;cursor:pointer;font-weight:700;font-size:12px;}
.footer{text-align:center;padding:16px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--sub);}
.footer a{color:var(--gold);text-decoration:none;font-weight:700;}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}
.fade-in-section.visible{opacity:1;transform:translateY(0);}
</style>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
</head>
<body>
<div class="texture"></div>
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
    <div class="banner-seal">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="50" cy="50" r="45" stroke="#c9a84c" stroke-width="2"/>
        <circle cx="50" cy="50" r="38" stroke="#c9a84c" stroke-width="1"/>
        <path d="M50 20L55 40H75L60 52L65 72L50 60L35 72L40 52L25 40H45Z" fill="#c9a84c" opacity=".5"/>
      </svg>
    </div>
    <div class="banner-content">
      <div class="firm-name"><?= htmlspecialchars($vcard['company'] ?? 'Law Firm') ?></div>
      <div class="banner-title-text"><?= htmlspecialchars($vcard['occupation'] ?? 'Attorney at Law') ?></div>
    </div>
    <div class="gold-rule-banner"></div>
  </div>
  <div class="profile-area">
    <div class="profile-top">
      <div class="av-wrap"><img src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($fullName) ?>"></div>
      <?php if (!empty($vcard['phone'])): ?><a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="consult-btn"><i class="fas fa-balance-scale"></i> Consult</a><?php endif; ?>
    </div>
    <div class="lawyer-name"><?= htmlspecialchars($fullName) ?></div>
    <div class="rule"></div>
    <?php if (!empty($vcard['occupation'])): ?><div class="lawyer-title"><?= htmlspecialchars($vcard['occupation']) ?></div><?php endif; ?>
    <?php if (!empty($vcard['company'])): ?><div class="lawyer-firm"><?= htmlspecialchars($vcard['company']) ?></div><?php endif; ?>
    <?php if (!empty($socialLinks)): ?>
    <div class="social-strip">
      <?php foreach ($socialLinks as $sl): $p=strtolower($sl['platform']??''); $ico=$platformIcons[$p]??'fa-globe'; ?>
      <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" class="soc"><i class="fab <?= $ico ?>"></i></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php if (!empty($vcard['description'])): ?>
  <div class="about-block fade-in-section"><p><?= nl2br(htmlspecialchars($vcard['description'])) ?></p></div>
  <?php endif; ?>
  <div class="section fade-in-section">
    <div class="sec-h">Contact</div>
    <div class="contact-list">
      <?php if (!empty($vcard['email'])): ?><a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="ci"><div class="ci-ico"><i class="fas fa-envelope"></i></div><div><div class="ci-lbl">Email</div><div class="ci-val"><?= htmlspecialchars($vcard['email']) ?></div></div></a><?php endif; ?>
      <?php if (!empty($vcard['phone'])): ?><a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="ci"><div class="ci-ico"><i class="fas fa-phone"></i></div><div><div class="ci-lbl">Phone</div><div class="ci-val"><?= htmlspecialchars($vcard['phone']) ?></div></div></a><?php endif; ?>
      <?php if (!empty($waPhone)): ?><a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="ci"><div class="ci-ico"><i class="fab fa-whatsapp"></i></div><div><div class="ci-lbl">WhatsApp</div><div class="ci-val"><?= htmlspecialchars($vcard['phone']) ?></div></div></a><?php endif; ?>
      <?php if (!empty($vcard['location'])): ?><a href="<?= htmlspecialchars($locationUrl) ?>" target="_blank" class="ci"><div class="ci-ico"><i class="fas fa-map-marker-alt"></i></div><div><div class="ci-lbl">Office</div><div class="ci-val"><?= htmlspecialchars($vcard['location']) ?></div></div></a><?php endif; ?>
    </div>
  </div>
  <?php if (!empty($services)): ?>
  <div class="section fade-in-section">
    <div class="sec-h">Practice Areas</div>
    <div class="practice-grid">
      <?php foreach ($services as $srv): ?>
      <div class="prac">
        <div class="prac-icon">⚖️</div>
        <div class="prac-title"><?= htmlspecialchars($srv['title']??'') ?></div>
        <?php if (!empty($srv['description'])): ?><div class="prac-desc"><?= htmlspecialchars($srv['description']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
<?php include __DIR__ . '/_features.php'; ?>
  <div class="qr-block fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code" width="72" height="72"></div>
    <div class="qr-txt"><h4>Scan &amp; Connect</h4><p>Scan to save contact or book a consultation.</p></div>
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
