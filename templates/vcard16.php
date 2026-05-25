<?php
/**
 * Tapify vCard Template: vcard16
 * Interior Designer — Warm Terracotta theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://'.$_SERVER['HTTP_HOST'].'/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['job_title'] ?? 'Interior Designer') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#f9f6f1;--white:#fff;--terr:#c17f5c;--terr2:#e8a07a;--dark:#2a1f14;--warm:#f2ece4;--text:#4a3728;--sub:#9a8070;--border:#e8ddd5;}
body{background:var(--bg);font-family:'DM Sans',sans-serif;color:var(--text);}
.wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--white);overflow:hidden;box-shadow:0 0 60px rgba(42,31,20,.08);}
@media(min-width:600px){
.desk{position:fixed;inset:0;pointer-events:none;z-index:0;}
.bgshape{position:absolute;border-radius:60% 40% 70% 30%/50% 60% 40% 50%;border:1px solid rgba(193,127,92,.08);animation:morphAnim 12s ease-in-out infinite alternate;}
.bs1{width:400px;height:400px;top:-120px;right:-120px;animation-delay:0s;}
.bs2{width:250px;height:250px;bottom:-60px;left:-60px;animation-delay:6s;}
@keyframes morphAnim{0%{border-radius:60% 40% 70% 30%/50% 60% 40% 50%;}100%{border-radius:30% 70% 40% 60%/60% 40% 50% 50%;}}
}
.banner{height:260px;position:relative;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;transition:transform 10s ease;}
.wrap:hover .banner img{transform:scale(1.05);}
.b-over{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(42,31,20,.1),rgba(42,31,20,.75));}
.b-content{position:absolute;bottom:0;left:0;right:0;padding:22px;display:flex;align-items:flex-end;justify-content:space-between;}
.b-left h2{font-family:'Fraunces',serif;font-size:26px;font-weight:300;color:#fff;letter-spacing:2px;margin-bottom:4px;}
.b-left p{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--terr2);}
.b-right{width:50px;height:50px;border:1px solid rgba(193,127,92,.5);display:flex;align-items:center;justify-content:center;color:var(--terr2);font-size:20px;}
.b-bar{position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--terr),var(--terr2),transparent);}
.prof{padding:0 22px 20px;background:var(--white);position:relative;z-index:5;}
.ptop{display:flex;align-items:flex-end;justify-content:space-between;margin-top:-44px;margin-bottom:14px;}
.av img{width:88px;height:88px;object-fit:cover;border:4px solid var(--white);box-shadow:0 4px 16px rgba(193,127,92,.2);}
.cta{display:flex;align-items:center;gap:8px;background:var(--dark);color:var(--terr2);font-size:11px;font-weight:600;padding:11px 16px;text-decoration:none;letter-spacing:1px;border-bottom:2px solid var(--terr);transition:all .3s;}
.cta:hover{background:var(--terr);}
.name{font-family:'Fraunces',serif;font-size:26px;font-weight:300;color:var(--dark);margin:12px 0 4px;}
.title{font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--terr);margin-bottom:3px;}
.co{font-size:13px;color:var(--sub);margin-bottom:4px;}
.tagline{font-family:'Fraunces',serif;font-style:italic;font-size:15px;color:var(--sub);margin-bottom:12px;}
.soc-row{display:flex;gap:8px;margin-bottom:10px;}
.soc{width:36px;height:36px;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--sub);font-size:14px;text-decoration:none;transition:all .3s;}
.soc:hover{background:var(--dark);border-color:var(--dark);color:var(--terr2);}
.web{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--terr);text-decoration:none;}
.bio{background:var(--warm);padding:18px;margin:0 22px 20px;border-left:3px solid var(--terr);}
.bio p{font-size:13px;line-height:1.9;color:var(--text);}
.sec{padding:0 22px;margin-bottom:20px;}
.sec-h{font-family:'Fraunces',serif;font-size:18px;font-weight:300;color:var(--dark);margin-bottom:14px;display:flex;align-items:center;gap:12px;}
.sec-h::after{content:'';flex:1;height:1px;background:var(--border);}
.cl{display:flex;flex-direction:column;gap:8px;}
.ci{display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--warm);border-left:3px solid transparent;text-decoration:none;color:var(--text);transition:all .3s;}
.ci:hover{border-left-color:var(--terr);background:var(--white);transform:translateX(4px);}
.ci-ic{width:34px;height:34px;background:rgba(193,127,92,.12);border:1px solid rgba(193,127,92,.2);display:flex;align-items:center;justify-content:center;color:var(--terr);font-size:13px;flex-shrink:0;}
.ci-l{font-size:10px;color:var(--sub);margin-bottom:1px;}.ci-v{font-size:12px;font-weight:500;word-break:break-word;}
.car-wrap{position:relative;overflow:hidden;margin-bottom:20px;}
.car-track{display:flex;gap:12px;padding:4px 22px;transition:transform .5s cubic-bezier(.25,.46,.45,.94);}
.c-card{min-width:160px;flex-shrink:0;transition:all .3s;}
.c-card:hover{transform:translateY(-4px);}
.c-card img{width:100%;height:110px;object-fit:cover;display:block;}
.c-body{padding:10px 12px;background:var(--warm);}
.c-title{font-family:'Fraunces',serif;font-size:14px;font-weight:300;color:var(--dark);margin-bottom:3px;}
.c-desc{font-size:11px;color:var(--sub);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.c-dots{display:flex;justify-content:center;gap:6px;padding:8px 0;}
.dot{width:6px;height:6px;border-radius:50%;background:var(--border);transition:all .3s;cursor:pointer;}
.dot.active{background:var(--terr);width:18px;border-radius:3px;}
.c-arrows{position:absolute;top:42%;width:100%;display:flex;justify-content:space-between;padding:0 8px;pointer-events:none;}
.c-arr{width:28px;height:28px;background:rgba(255,255,255,.9);border:1px solid var(--border);color:var(--terr);display:flex;align-items:center;justify-content:center;cursor:pointer;pointer-events:all;font-size:11px;transition:all .3s;}
.c-arr:hover{background:var(--terr);color:#fff;border-color:var(--terr);}
.qr-blk{margin:0 22px 20px;background:var(--dark);padding:22px;display:flex;align-items:center;gap:20px;}
.qr-box{background:#fff;padding:6px;width:88px;height:88px;flex-shrink:0;}
.qr-box img{width:100%;height:100%;display:block;}
.qr-info h4{font-family:'Fraunces',serif;font-size:18px;font-weight:300;color:var(--terr2);margin-bottom:4px;}
.qr-info p{font-size:12px;color:rgba(255,255,255,.7);line-height:1.6;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 44px);margin:0 22px 20px;padding:14px;background:var(--terr);color:#fff;font-size:12px;font-weight:600;letter-spacing:2px;text-transform:uppercase;cursor:pointer;border:none;transition:all .3s;}
.save-btn:hover{background:var(--dark);}
.share{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border:1px solid var(--border);color:var(--sub);font-size:12px;text-decoration:none;transition:all .3s;cursor:pointer;background:transparent;}
.sh:hover{background:var(--dark);border-color:var(--dark);color:var(--terr2);}
.copy-row{display:flex;border:1px solid var(--border);}
.copy-inp{flex:1;background:var(--warm);border:none;color:var(--sub);font-size:11px;padding:10px 12px;font-family:'DM Sans',sans-serif;outline:none;}
.cp-btn{background:var(--terr);color:#fff;border:none;padding:10px 16px;cursor:pointer;font-weight:600;font-size:11px;}
.footer{text-align:center;padding:14px;font-size:10px;letter-spacing:2px;color:var(--sub);background:var(--warm);}
.footer span{color:var(--terr);}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}.fade-in-section.visible{opacity:1;transform:none;}
</style>
</head>
<body>
<div class="desk"><div class="bgshape bs1"></div><div class="bgshape bs2"></div></div>
<div class="wrap">
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
    <div class="b-over"></div>
    <div class="b-content">
      <div class="b-left">
        <h2><?= htmlspecialchars($vcard['company'] ?? $fullName) ?></h2>
        <p><?= htmlspecialchars($vcard['job_title'] ?? 'Interior Design Studio') ?></p>
      </div>
      <div class="b-right"><i class="fas fa-couch"></i></div>
    </div>
    <div class="b-bar"></div>
  </div>
  <div class="prof">
    <div class="ptop">
      <div class="av"><img src="<?= $profileImg ?>" alt="<?= htmlspecialchars($fullName) ?>"></div>
      <?php if (!empty($vcard['phone'])): ?>
      <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="cta"><i class="fas fa-calendar-check"></i> Consultation</a>
      <?php endif; ?>
    </div>
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
      <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="ci"><div class="ci-ic"><i class="fas fa-phone"></i></div><div><div class="ci-l">Studio</div><div class="ci-v"><?= htmlspecialchars($vcard['phone']) ?></div></div></a>
      <?php endif; ?>
      <?php if (!empty($waPhone)): ?>
      <a href="https://wa.me/<?= $waPhone ?>" class="ci" target="_blank" rel="noopener"><div class="ci-ic"><i class="fab fa-whatsapp"></i></div><div><div class="ci-l">WhatsApp</div><div class="ci-v"><?= htmlspecialchars($vcard['phone']) ?></div></div></a>
      <?php endif; ?>
      <?php if (!empty($vcard['location'])): ?>
      <a href="<?= $locationUrl ?>" class="ci" target="_blank" rel="noopener"><div class="ci-ic"><i class="fas fa-map-marker-alt"></i></div><div><div class="ci-l">Studio</div><div class="ci-v"><?= htmlspecialchars($vcard['location']) ?></div></div></a>
      <?php endif; ?>
      <?php if (!empty($vcard['website'])): ?>
      <a href="<?= htmlspecialchars($vcard['website']) ?>" class="ci" target="_blank" rel="noopener"><div class="ci-ic"><i class="fas fa-globe"></i></div><div><div class="ci-l">Website</div><div class="ci-v"><?= htmlspecialchars(preg_replace('#^https?://#','',$vcard['website'])) ?></div></div></a>
      <?php endif; ?>
    </div>
  </div>
  <?php if (!empty($services)): ?>
  <div class="sec fade-in-section"><div class="sec-h">Portfolio</div></div>
  <div class="car-wrap fade-in-section" id="carousel16">
    <div class="car-track" id="carousel16-track">
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
    <div class="c-arrows"><div class="c-arr" id="carousel16-prev"><i class="fas fa-chevron-left"></i></div><div class="c-arr" id="carousel16-next"><i class="fas fa-chevron-right"></i></div></div>
    <div class="c-dots" id="carousel16-dots"></div>
  </div>
  <?php endif; ?>
<?php include __DIR__ . '/_features.php'; ?>
  <div class="qr-blk fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code"></div>
    <div class="qr-info"><h4>View Projects</h4><p>Scan to explore our full portfolio and book a consultation.</p></div>
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
  <div class="footer">Powered by <span>Tapify</span> · Interior Design Edition</div>
  <?php endif; ?>
</div>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
<script>
const _obs=new IntersectionObserver(e=>{e.forEach(el=>{if(el.isIntersecting)el.target.classList.add('visible');});},{threshold:0.1});
document.querySelectorAll('.fade-in-section').forEach(el=>_obs.observe(el));
function initCarousel(id){
  const wrap=document.getElementById(id);if(!wrap)return;
  const track=document.getElementById(id+'-track');const dotsEl=document.getElementById(id+'-dots');
  if(!track)return;const cards=track.querySelectorAll('.c-card');const total=cards.length;if(total===0)return;
  let idx=0;const cardW=160,gap=12;
  if(dotsEl){dotsEl.innerHTML='';cards.forEach((_,i)=>{const d=document.createElement('div');d.className='dot'+(i===0?' active':'');d.onclick=()=>goTo(i);dotsEl.appendChild(d);});}
  function goTo(n){idx=(n+total)%total;track.style.transform=`translateX(calc(-${idx}*(${cardW}px + ${gap}px)))`;if(dotsEl)dotsEl.querySelectorAll('.dot').forEach((d,i)=>d.classList.toggle('active',i===idx));}
  const prev=document.getElementById(id+'-prev');const next=document.getElementById(id+'-next');
  if(prev)prev.onclick=()=>goTo(idx-1);if(next)next.onclick=()=>goTo(idx+1);
  setInterval(()=>goTo(idx+1),4000);
}
initCarousel('carousel16');
</script>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
