<?php
/**
 * Tapify vCard Template: vcard10
 * Musician/Artist — Dark Purple/Aurora theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://'.$_SERVER['HTTP_HOST'].'/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['occupation'] ?? 'Digital Card') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Exo+2:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#04050e;--card:#0c0d1e;--purple:#7c3aed;--pink:#ec4899;--cyan:#06b6d4;--white:#f0f0ff;--text:#6b7280;--border:#1a1b2e;}
body{background:var(--bg);font-family:'Exo 2',sans-serif;color:var(--white);overflow-x:hidden;}
.aurora{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.aurora-band{position:absolute;height:300px;border-radius:50%;filter:blur(60px);animation:auroraPulse 8s ease-in-out infinite;}
.aurora-band:nth-child(1){width:400px;background:rgba(124,58,237,.15);top:0;left:-100px;animation-delay:0s;}
.aurora-band:nth-child(2){width:300px;background:rgba(236,72,153,.1);top:-50px;right:-80px;animation-delay:3s;}
.aurora-band:nth-child(3){width:350px;background:rgba(6,182,212,.08);bottom:200px;left:50%;animation-delay:6s;}
@keyframes auroraPulse{0%,100%{opacity:.6;transform:scaleX(1);}50%{opacity:1;transform:scaleX(1.1);}}
.sound-bars{position:fixed;bottom:0;left:0;right:0;height:40px;display:flex;align-items:flex-end;justify-content:center;gap:3px;pointer-events:none;z-index:0;}
.bar{width:3px;background:var(--purple);border-radius:2px 2px 0 0;opacity:.2;animation:barBounce ease-in-out infinite;}
.bar:nth-child(1){animation-duration:0.8s;height:20px;}.bar:nth-child(2){animation-duration:1.1s;height:28px;}.bar:nth-child(3){animation-duration:0.9s;height:35px;}.bar:nth-child(4){animation-duration:1.3s;height:22px;}.bar:nth-child(5){animation-duration:0.7s;height:30px;}.bar:nth-child(6){animation-duration:1.2s;height:25px;background:var(--pink);}.bar:nth-child(7){animation-duration:0.85s;height:38px;background:var(--cyan);}.bar:nth-child(8){animation-duration:1.0s;height:18px;}.bar:nth-child(9){animation-duration:0.95s;height:32px;}.bar:nth-child(10){animation-duration:1.4s;height:26px;background:var(--pink);}.bar:nth-child(11){animation-duration:0.75s;height:29px;}.bar:nth-child(12){animation-duration:1.15s;height:22px;background:var(--cyan);}
@keyframes barBounce{0%,100%{transform:scaleY(1);}50%{transform:scaleY(.4);}}
.card-wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--bg);position:relative;z-index:1;overflow:hidden;}
.banner{position:relative;height:240px;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;filter:brightness(.35) saturate(1.4);}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(160deg,rgba(4,5,14,.4),rgba(124,58,237,.15),rgba(4,5,14,.9));}
.live-badge{position:absolute;top:16px;left:16px;display:flex;align-items:center;gap:6px;background:rgba(236,72,153,.9);color:#fff;font-size:10px;font-weight:700;padding:5px 12px;border-radius:20px;letter-spacing:1px;}
.live-dot{width:8px;height:8px;border-radius:50%;background:#fff;animation:livePulse 1s infinite;}
@keyframes livePulse{0%,100%{opacity:1;}50%{opacity:.4;}}
.banner-artist{position:absolute;bottom:22px;left:22px;right:22px;}
.artist-name-banner{font-family:'Orbitron',sans-serif;font-size:26px;font-weight:900;color:var(--white);letter-spacing:3px;margin-bottom:4px;}
.profile-area{padding:0 22px 18px;position:relative;z-index:5;}
.profile-top{display:flex;align-items:flex-end;justify-content:space-between;margin-top:-46px;margin-bottom:14px;}
.av-wrap{position:relative;width:92px;height:92px;}
.av-wrap img{width:100%;height:100%;border-radius:50%;object-fit:cover;border:3px solid var(--purple);}
.av-ring{position:absolute;inset:-4px;border-radius:50%;background:conic-gradient(var(--purple),var(--pink),var(--cyan),var(--purple));animation:avSpin 5s linear infinite;z-index:-1;}
@keyframes avSpin{to{transform:rotate(360deg);}}
.follow-btn{display:flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--purple),var(--pink));color:#fff;font-size:11px;font-weight:700;padding:11px 18px;border-radius:30px;text-decoration:none;letter-spacing:1px;transition:all .3s;}
.follow-btn:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(124,58,237,.5);}
.artist-name{font-family:'Orbitron',sans-serif;font-size:20px;font-weight:700;color:var(--white);margin-bottom:4px;}
.genre-row{display:flex;gap:6px;flex-wrap:wrap;margin:8px 0;}
.genre{font-size:10px;padding:3px 10px;border-radius:20px;background:rgba(124,58,237,.2);color:var(--purple);border:1px solid rgba(124,58,237,.3);}
.artist-title{font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--pink);margin-bottom:3px;}
.artist-label{font-size:12px;color:var(--text);}
.social-row{display:flex;gap:8px;padding:10px 0;}
.soc{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;text-decoration:none;background:var(--card);border:1px solid var(--border);color:var(--text);transition:all .3s;}
.soc:hover{background:var(--purple);border-color:var(--purple);color:#fff;}
.bio{margin:0 22px 18px;padding:16px 18px;background:var(--card);border:1px solid var(--border);border-top:2px solid var(--purple);}
.bio p{font-size:13px;line-height:1.9;color:var(--text);}
.section{padding:0 22px 18px;}
.sec-h{font-family:'Orbitron',sans-serif;font-size:11px;letter-spacing:3px;text-transform:uppercase;color:var(--purple);margin-bottom:12px;}
.contact-list{display:flex;flex-direction:column;gap:10px;}
.ci{display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--card);border:1px solid var(--border);border-radius:10px;text-decoration:none;color:var(--white);transition:all .3s;}
.ci:hover{border-color:var(--purple);background:rgba(124,58,237,.08);}
.ci-ico{width:34px;height:34px;background:rgba(124,58,237,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--purple);font-size:13px;flex-shrink:0;}
.ci-lbl{font-size:9px;color:var(--text);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;}
.ci-val{font-size:12px;font-weight:600;word-break:break-word;}
.releases-scroll{display:flex;gap:12px;overflow-x:auto;padding:4px 2px 14px;scrollbar-width:none;}
.releases-scroll::-webkit-scrollbar{display:none;}
.release-card{min-width:140px;background:var(--card);border:1px solid var(--border);border-radius:12px;overflow:hidden;flex-shrink:0;position:relative;transition:all .3s;}
.release-card:hover{border-color:var(--purple);transform:translateY(-4px);}
.release-card img{width:100%;height:100px;object-fit:cover;filter:brightness(.7);}
.play-btn{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:32px;height:32px;background:rgba(124,58,237,.8);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;margin-top:-16px;}
.release-body{padding:10px 12px;}
.release-title{font-size:12px;font-weight:700;color:var(--white);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:3px;}
.release-desc{font-size:10px;color:var(--text);line-height:1.5;}
.qr-block{margin:0 22px 18px;background:var(--card);border:1px solid rgba(124,58,237,.3);border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px;}
.qr-box{background:#fff;padding:6px;border-radius:8px;width:84px;height:84px;flex-shrink:0;}
.qr-box img{width:72px;height:72px;}
.qr-txt h4{font-family:'Orbitron',sans-serif;font-size:13px;color:var(--purple);margin-bottom:4px;letter-spacing:1px;}
.qr-txt p{font-size:11px;color:var(--text);line-height:1.6;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 44px);margin:0 22px 18px;padding:14px;background:linear-gradient(135deg,var(--purple),var(--pink));color:#fff;font-size:12px;font-weight:700;border-radius:10px;border:none;cursor:pointer;letter-spacing:2px;text-transform:uppercase;transition:all .3s;}
.save-btn:hover{box-shadow:0 8px 24px rgba(124,58,237,.5);}
.share-area{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border-radius:10px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid var(--border);color:var(--text);background:var(--card);transition:all .3s;cursor:pointer;}
.sh:hover{border-color:var(--purple);color:var(--purple);}
.copy-row{display:flex;border:1px solid var(--border);border-radius:10px;overflow:hidden;background:var(--card);}
.copy-inp{flex:1;background:transparent;border:none;color:var(--text);font-size:11px;padding:12px 14px;outline:none;}
.cp-btn{background:var(--purple);color:#fff;border:none;padding:12px 18px;cursor:pointer;font-weight:700;font-size:12px;}
.footer{text-align:center;padding:16px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--text);border-top:1px solid var(--border);}
.footer a{color:var(--purple);text-decoration:none;}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}
.fade-in-section.visible{opacity:1;transform:translateY(0);}
</style>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
</head>
<body>
<div class="aurora">
  <div class="aurora-band"></div><div class="aurora-band"></div><div class="aurora-band"></div>
</div>
<div class="sound-bars">
  <div class="bar"></div><div class="bar"></div><div class="bar"></div><div class="bar"></div>
  <div class="bar"></div><div class="bar"></div><div class="bar"></div><div class="bar"></div>
  <div class="bar"></div><div class="bar"></div><div class="bar"></div><div class="bar"></div>
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
    <div class="live-badge"><span class="live-dot"></span> LIVE</div>
    <div class="banner-artist">
      <div class="artist-name-banner"><?= htmlspecialchars($fullName) ?></div>
    </div>
  </div>
  <div class="profile-area">
    <div class="profile-top">
      <div class="av-wrap">
        <div class="av-ring"></div>
        <img src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($fullName) ?>">
      </div>
      <?php if (!empty($vcard['phone'])): ?><a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="follow-btn"><i class="fas fa-music"></i> Book</a><?php endif; ?>
    </div>
    <div class="artist-name"><?= htmlspecialchars($fullName) ?></div>
    <?php if (!empty($vcard['occupation'])): ?><div class="artist-title"><?= htmlspecialchars($vcard['occupation']) ?></div><?php endif; ?>
    <?php if (!empty($vcard['company'])): ?><div class="artist-label"><?= htmlspecialchars($vcard['company']) ?></div><?php endif; ?>
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
    <div class="sec-h">Latest Releases</div>
    <div class="releases-scroll">
      <?php foreach ($services as $srv): ?>
      <div class="release-card">
        <?php if (!empty($srv['image'])): ?>
        <img src="<?= htmlspecialchars(imgUrl($srv['image'])) ?>" alt="">
        <div class="play-btn"><i class="fas fa-play"></i></div>
        <?php endif; ?>
        <div class="release-body">
          <div class="release-title"><?= htmlspecialchars($srv['title']??'') ?></div>
          <?php if (!empty($srv['description'])): ?><div class="release-desc"><?= htmlspecialchars($srv['description']) ?></div><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
<?php include __DIR__ . '/_features.php'; ?>
  <div class="qr-block fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code" width="72" height="72"></div>
    <div class="qr-txt"><h4>Scan &amp; Listen</h4><p>Scan to save contact or stream music.</p></div>
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
