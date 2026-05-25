<?php
/**
 * Tapify vCard Template: vcard11
 * Photographer Portfolio — Dark/Gold theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://'.$_SERVER['HTTP_HOST'].'/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1452421822248-d4c2b47f0c81?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['job_title'] ?? 'Photography') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#0a0a0a;--card:#111;--gold:#d4a853;--gold2:#f0c878;--white:#f5f0e8;--text:#999;--border:#222;}
body{background:var(--bg);font-family:'Jost',sans-serif;color:var(--white);overflow-x:hidden;}
.wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--bg);position:relative;overflow:hidden;}

/* ── DESKTOP PARTICLES ── */
@media(min-width:600px){
.particles{position:fixed;inset:0;pointer-events:none;z-index:0;}
.p{position:absolute;border-radius:50%;animation:pfloat ease-in-out infinite alternate;}
.p1{width:300px;height:300px;background:radial-gradient(circle,rgba(212,168,83,.12),transparent);top:-80px;right:-80px;animation-duration:8s;}
.p2{width:200px;height:200px;background:radial-gradient(circle,rgba(212,168,83,.08),transparent);bottom:200px;left:-60px;animation-duration:11s;animation-delay:3s;}
.p3{width:150px;height:150px;background:radial-gradient(circle,rgba(255,255,255,.03),transparent);top:40%;left:50%;animation-duration:9s;animation-delay:1.5s;}
@keyframes pfloat{0%{transform:translate(0,0) scale(1);}100%{transform:translate(20px,-30px) scale(1.1);}}
.geo{position:fixed;pointer-events:none;z-index:0;opacity:.04;border:1px solid var(--gold);animation:geoSpin linear infinite;}
.geo1{width:200px;height:200px;top:10%;right:5%;animation-duration:30s;}
.geo2{width:120px;height:120px;bottom:15%;left:5%;animation-duration:20s;animation-delay:5s;}
@keyframes geoSpin{from{transform:rotate(0deg);}to{transform:rotate(360deg);}}
}

/* BANNER */
.banner{height:260px;position:relative;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;animation:zoomIn 15s ease-in-out infinite alternate;}
@keyframes zoomIn{from{transform:scale(1);}to{transform:scale(1.08);}
}
.b-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(10,10,10,.1),rgba(10,10,10,.92));}
.b-tag{position:absolute;top:18px;left:18px;font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--gold);border:1px solid rgba(212,168,83,.4);padding:5px 14px;backdrop-filter:blur(6px);background:rgba(10,10,10,.4);}
.shutter{position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent);}

/* PROFILE */
.prof{padding:0 22px 20px;text-align:center;position:relative;z-index:5;}
.av{width:100px;height:100px;margin:-50px auto 14px;position:relative;}
.av img{width:100%;height:100%;border-radius:50%;object-fit:cover;border:3px solid var(--gold);}
.av::after{content:'';position:absolute;inset:-5px;border-radius:50%;border:1px dashed rgba(212,168,83,.4);animation:spinRing 12s linear infinite;}
@keyframes spinRing{to{transform:rotate(360deg);}}
.pname{font-family:'Cormorant',serif;font-size:30px;font-weight:300;letter-spacing:3px;margin-bottom:4px;}
.ptag{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:4px;}
.pco{font-size:13px;color:var(--text);margin-bottom:4px;}
.tagline{font-family:'Cormorant',serif;font-style:italic;font-size:15px;color:var(--text);margin-bottom:14px;}
.soc-row{display:flex;justify-content:center;gap:10px;margin-bottom:16px;}
.soc{width:38px;height:38px;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--text);font-size:15px;text-decoration:none;transition:all .3s;border-radius:50%;}
.soc:hover{border-color:var(--gold);color:var(--gold);transform:translateY(-3px);}
.website-link{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--gold);text-decoration:none;border-bottom:1px solid rgba(212,168,83,.3);padding-bottom:2px;margin-bottom:16px;transition:all .3s;}
.website-link:hover{border-color:var(--gold);}

/* BIO */
.bio{margin:0 22px 20px;padding:18px;border:1px solid var(--border);border-left:3px solid var(--gold);background:rgba(255,255,255,.02);}
.bio p{font-size:13px;line-height:1.9;color:var(--text);}

/* SECTION */
.sec{padding:0 22px;margin-bottom:20px;}
.sec-h{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:14px;display:flex;align-items:center;gap:10px;}
.sec-h::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--border),transparent);}

/* CONTACT */
.cl{display:flex;flex-direction:column;gap:8px;}
.ci{display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--card);border:1px solid var(--border);text-decoration:none;color:var(--white);transition:all .3s;}
.ci:hover{border-color:var(--gold);padding-left:20px;}
.ci-ic{width:34px;height:34px;background:rgba(212,168,83,.1);border:1px solid rgba(212,168,83,.2);display:flex;align-items:center;justify-content:center;color:var(--gold);font-size:13px;flex-shrink:0;}
.ci-l{font-size:10px;color:var(--text);letter-spacing:1px;margin-bottom:1px;}
.ci-v{font-size:12px;font-weight:500;word-break:break-word;}

/* CAROUSEL */
.carousel-wrap{position:relative;overflow:hidden;margin-bottom:20px;}
.carousel-track{display:flex;gap:12px;padding:4px 22px 4px;transition:transform .5s cubic-bezier(.25,.46,.45,.94);will-change:transform;}
.c-card{min-width:160px;flex-shrink:0;background:var(--card);border:1px solid var(--border);transition:all .3s;}
.c-card:hover{border-color:var(--gold);transform:translateY(-4px);}
.c-card img{width:100%;height:110px;object-fit:cover;display:block;filter:grayscale(.3);}
.c-card:hover img{filter:grayscale(0);}
.c-body{padding:10px 12px;}
.c-title{font-size:12px;font-weight:600;margin-bottom:3px;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;}
.c-desc{font-size:11px;color:var(--text);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.c-dots{display:flex;justify-content:center;gap:6px;padding:8px 0;}
.dot{width:6px;height:6px;border-radius:50%;background:var(--border);transition:all .3s;cursor:pointer;}
.dot.active{background:var(--gold);width:18px;border-radius:3px;}
.c-arrows{position:absolute;top:50%;transform:translateY(-50%);width:100%;display:flex;justify-content:space-between;padding:0 4px;pointer-events:none;}
.c-arr{width:30px;height:30px;background:rgba(10,10,10,.8);border:1px solid var(--border);color:var(--gold);display:flex;align-items:center;justify-content:center;cursor:pointer;pointer-events:all;transition:all .3s;font-size:11px;}
.c-arr:hover{background:var(--gold);color:var(--bg);}

/* QR */
.qr-blk{margin:0 22px 20px;background:var(--card);border:1px solid var(--border);padding:22px;display:flex;align-items:center;gap:20px;}
.qr-box{background:#fff;padding:6px;width:88px;height:88px;flex-shrink:0;}
.qr-box img{width:100%;height:100%;display:block;}
.qr-info h4{font-family:'Cormorant',serif;font-size:18px;color:var(--gold);margin-bottom:4px;}
.qr-info p{font-size:12px;color:var(--text);line-height:1.6;}

/* SAVE */
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 44px);margin:0 22px 20px;padding:14px;background:linear-gradient(135deg,var(--gold),var(--gold2));color:var(--bg);font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;text-decoration:none;transition:all .3s;cursor:pointer;border:none;}
.save-btn:hover{opacity:.9;transform:translateY(-2px);box-shadow:0 8px 25px rgba(212,168,83,.3);}

/* SHARE */
.share{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border:1px solid var(--border);color:var(--text);font-size:12px;text-decoration:none;transition:all .3s;cursor:pointer;background:transparent;}
.sh:hover{border-color:var(--gold);color:var(--gold);}
.copy-row{display:flex;border:1px solid var(--border);}
.copy-inp{flex:1;background:var(--card);border:none;color:var(--text);font-size:11px;padding:10px 12px;font-family:'Jost',sans-serif;outline:none;}
.cp-btn{background:var(--gold);color:var(--bg);border:none;padding:10px 16px;cursor:pointer;font-weight:700;font-size:11px;transition:opacity .3s;}
.cp-btn:hover{opacity:.85;}

.footer{text-align:center;padding:14px;font-size:10px;letter-spacing:2px;color:var(--text);border-top:1px solid var(--border);}
.footer span{color:var(--gold);}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}
.fade-in-section.visible{opacity:1;transform:none;}
</style>
</head>
<body>
<div class="particles">
  <div class="p p1"></div><div class="p p2"></div><div class="p p3"></div>
  <div class="geo geo1"></div><div class="geo geo2"></div>
</div>
<div class="wrap">
  <div class="banner">
    <?php if (($vcard['cover_type'] ?? 'image') === 'video' && !empty($vcard['cover_image'])): ?>
        <?php
        $videoUrl = $vcard['cover_image'];
        if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $videoUrl, $match);
            $ytId = $match[1] ?? '';
            echo '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/'.$ytId.'?autoplay=1&mute=1&loop=1&playlist='.$ytId.'&controls=0&showinfo=0&rel=0" frameborder="0" allow="autoplay; encrypted-media" style="object-fit:cover;pointer-events:none;"></iframe>';
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
    <div class="b-overlay"></div>
    <div class="b-tag"><i class="fas fa-camera"></i> &nbsp;<?= htmlspecialchars($vcard['job_title'] ?? 'Visual Storyteller') ?></div>
    <div class="shutter"></div>
  </div>
  <div class="prof">
    <div class="av"><img src="<?= $profileImg ?>" alt="<?= htmlspecialchars($fullName) ?>"></div>
    <div class="pname"><?= htmlspecialchars($fullName) ?></div>
    <div class="ptag"><?= htmlspecialchars($vcard['job_title'] ?? '') ?></div>
    <?php if (!empty($vcard['company']) || !empty($vcard['location'])): ?>
    <div class="pco"><?= htmlspecialchars(implode(' · ', array_filter([$vcard['company'] ?? '', $vcard['location'] ?? '']))) ?></div>
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
    <a href="<?= htmlspecialchars($vcard['website']) ?>" class="website-link" target="_blank" rel="noopener"><i class="fas fa-globe"></i> <?= htmlspecialchars(preg_replace('#^https?://#','',$vcard['website'])) ?></a>
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
      <a href="<?= $locationUrl ?>" class="ci" target="_blank" rel="noopener"><div class="ci-ic"><i class="fas fa-map-marker-alt"></i></div><div><div class="ci-l">Location</div><div class="ci-v"><?= htmlspecialchars($vcard['location']) ?></div></div></a>
      <?php endif; ?>
      <?php if (!empty($vcard['website'])): ?>
      <a href="<?= htmlspecialchars($vcard['website']) ?>" class="ci" target="_blank" rel="noopener"><div class="ci-ic"><i class="fas fa-globe"></i></div><div><div class="ci-l">Website</div><div class="ci-v"><?= htmlspecialchars(preg_replace('#^https?://#','',$vcard['website'])) ?></div></div></a>
      <?php endif; ?>
    </div>
  </div>
  <?php if (!empty($services)): ?>
  <div class="sec fade-in-section"><div class="sec-h">Portfolio</div></div>
  <div class="carousel-wrap fade-in-section" id="carousel11">
    <div class="carousel-track" id="carousel11-track">
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
    <div class="c-arrows"><div class="c-arr" id="carousel11-prev"><i class="fas fa-chevron-left"></i></div><div class="c-arr" id="carousel11-next"><i class="fas fa-chevron-right"></i></div></div>
    <div class="c-dots" id="carousel11-dots"></div>
  </div>
  <?php endif; ?>
  <div class="qr-blk fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code"></div>
    <div class="qr-info"><h4>View Portfolio</h4><p>Scan to browse my full gallery and book a session.</p></div>
  </div>
  <button class="save-btn fade-in-section" onclick="saveContact()"><i class="fas fa-address-card"></i>&nbsp; Save Contact</button>
  <div class="share fade-in-section">
    <div class="sh-row">
      <button class="sh" onclick="shareCard('whatsapp')"><i class="fab fa-whatsapp"></i> Share</button>
      <button class="sh" onclick="shareCard('facebook')"><i class="fab fa-facebook-f"></i> Share</button>
      <button class="sh" onclick="shareCard('native')"><i class="fas fa-share-alt"></i> Share</button>
    </div>
    <div class="copy-row"><input class="copy-inp" id="cardUrlInput" value="<?= htmlspecialchars($cardUrl) ?>" readonly><button class="cp-btn" onclick="navigator.clipboard.writeText(document.getElementById('cardUrlInput').value);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)">Copy</button></div>
  </div>
  <?php if (empty($vcard['remove_branding'])): ?>
  <div class="footer">Powered by <span>Tapify</span> · Photography Edition</div>
  <?php endif; ?>
</div>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
<script>
const _obs=new IntersectionObserver(e=>{e.forEach(el=>{if(el.isIntersecting)el.target.classList.add('visible');});},{threshold:0.1});
document.querySelectorAll('.fade-in-section').forEach(el=>_obs.observe(el));
// Carousel
function initCarousel(id){
  const wrap=document.getElementById(id);
  if(!wrap)return;
  const track=document.getElementById(id+'-track');
  const dotsEl=document.getElementById(id+'-dots');
  if(!track)return;
  const cards=track.querySelectorAll('.c-card');
  const total=cards.length;
  if(total===0)return;
  let idx=0;
  const cardW=160,gap=12;
  // build dots
  if(dotsEl){dotsEl.innerHTML='';cards.forEach((_,i)=>{const d=document.createElement('div');d.className='dot'+(i===0?' active':'');d.onclick=()=>goTo(i);dotsEl.appendChild(d);});}
  function goTo(n){idx=(n+total)%total;track.style.transform=`translateX(calc(-${idx}*(${cardW}px + ${gap}px)))`;if(dotsEl)dotsEl.querySelectorAll('.dot').forEach((d,i)=>d.classList.toggle('active',i===idx));}
  const prev=document.getElementById(id+'-prev');
  const next=document.getElementById(id+'-next');
  if(prev)prev.onclick=()=>goTo(idx-1);
  if(next)next.onclick=()=>goTo(idx+1);
  setInterval(()=>goTo(idx+1),3500);
}
initCarousel('carousel11');
</script>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
