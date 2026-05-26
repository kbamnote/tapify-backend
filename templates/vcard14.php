<?php
/**
 * Tapify vCard Template: vcard14
 * Yoga & Wellness — Sage/Earth/Cream theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://tapify-backend-production.up.railway.app/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['job_title'] ?? 'Yoga & Wellness') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Philosopher:ital,wght@0,400;0,700;1,400&family=Nunito:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#f5f0eb;--white:#fff;--sage:#7a9e7e;--sage2:#a8c5a0;--earth:#8b6f47;--cream:#fef9f4;--text:#3d2b1f;--sub:#967b6a;--border:#e8ddd5;}
body{background:var(--bg);font-family:'Nunito',sans-serif;color:var(--text);}
.wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--white);overflow:hidden;}
@media(min-width:600px){
.desk{position:fixed;inset:0;pointer-events:none;z-index:0;}
.mandala{position:absolute;border-radius:50%;border:1px solid rgba(122,158,126,.1);animation:mandalaSpin linear infinite;}
.m1{width:500px;height:500px;top:-150px;right:-150px;animation-duration:40s;}
.m2{width:300px;height:300px;bottom:0;left:-100px;animation-duration:25s;animation-delay:10s;}
@keyframes mandalaSpin{to{transform:rotate(360deg);}}
.leaf{position:fixed;font-size:20px;opacity:.05;animation:leafFloat ease-in-out infinite alternate;}
.l1{top:15%;left:5%;animation-duration:8s;}
.l2{top:50%;right:3%;animation-duration:11s;animation-delay:3s;}
.l3{bottom:20%;left:8%;animation-duration:9s;animation-delay:5s;}
@keyframes leafFloat{0%{transform:translateY(0) rotate(0deg);}100%{transform:translateY(-20px) rotate(15deg);}}}
.banner{height:230px;position:relative;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;filter:brightness(.75) saturate(1.2);}
.b-over{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(122,158,126,.3),rgba(61,43,31,.8));}
.b-content{position:absolute;bottom:0;left:0;right:0;text-align:center;padding:22px;}
.b-studio{font-family:'Philosopher',serif;font-size:28px;color:#fff;letter-spacing:3px;margin-bottom:4px;}
.b-tag{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--sage2);}
.b-bar{position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--sage),transparent);}
.prof{padding:0 22px 20px;text-align:center;background:var(--white);}
.av{width:100px;height:100px;margin:-50px auto 14px;position:relative;}
.av img{width:100%;height:100%;border-radius:50%;object-fit:cover;border:4px solid var(--white);box-shadow:0 4px 20px rgba(122,158,126,.3);}
.av::after{content:'🌿';position:absolute;bottom:0;right:-2px;font-size:18px;}
.name{font-family:'Philosopher',serif;font-size:26px;color:var(--text);margin-bottom:4px;}
.title{font-size:11px;letter-spacing:3px;text-transform:uppercase;color:var(--sage);margin-bottom:4px;}
.co{font-size:13px;color:var(--sub);margin-bottom:4px;}
.tagline{font-family:'Philosopher',serif;font-style:italic;font-size:15px;color:var(--earth);margin-bottom:14px;}
.certs{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-bottom:12px;}
.cert{font-size:10px;padding:4px 12px;border-radius:20px;background:rgba(122,158,126,.12);border:1px solid rgba(122,158,126,.3);color:var(--sage);font-weight:600;}
.soc-row{display:flex;justify-content:center;gap:10px;margin-bottom:10px;}
.soc{width:38px;height:38px;border-radius:50%;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--sub);font-size:15px;text-decoration:none;transition:all .3s;}
.soc:hover{background:var(--sage);border-color:var(--sage);color:#fff;}
.web{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--sage);text-decoration:none;}
.bio{margin:0 22px 20px;padding:18px;background:rgba(122,158,126,.08);border-radius:16px;border:1px solid rgba(122,158,126,.2);}
.bio p{font-size:13px;line-height:1.9;color:var(--text);text-align:center;}
.sec{padding:0 22px;margin-bottom:20px;}
.sec-h{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
.sec-h span{font-family:'Philosopher',serif;font-size:18px;color:var(--text);}
.sec-h::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,rgba(122,158,126,.4),transparent);}
.cl{display:flex;flex-direction:column;gap:8px;}
.ci{display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--cream);border-radius:12px;border:1px solid var(--border);text-decoration:none;color:var(--text);transition:all .3s;}
.ci:hover{background:rgba(122,158,126,.1);border-color:rgba(122,158,126,.4);transform:translateX(4px);}
.ci-ic{width:34px;height:34px;border-radius:50%;background:rgba(122,158,126,.15);display:flex;align-items:center;justify-content:center;color:var(--sage);font-size:13px;flex-shrink:0;}
.ci-l{font-size:10px;color:var(--sub);margin-bottom:1px;}
.ci-v{font-size:12px;font-weight:600;word-break:break-word;}
.car-wrap{position:relative;overflow:hidden;margin-bottom:20px;}
.car-track{display:flex;gap:12px;padding:4px 22px;transition:transform .5s cubic-bezier(.25,.46,.45,.94);}
.c-card{min-width:155px;flex-shrink:0;background:var(--white);border-radius:16px;border:1px solid var(--border);overflow:hidden;transition:all .3s;}
.c-card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(122,158,126,.2);}
.c-card img{width:100%;height:100px;object-fit:cover;}
.c-body{padding:10px 12px;}
.c-title{font-family:'Philosopher',serif;font-size:14px;color:var(--text);margin-bottom:3px;}
.c-desc{font-size:11px;color:var(--sub);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.c-dots{display:flex;justify-content:center;gap:6px;padding:8px 0;}
.dot{width:6px;height:6px;border-radius:50%;background:var(--border);transition:all .3s;cursor:pointer;}
.dot.active{background:var(--sage);width:18px;border-radius:3px;}
.c-arrows{position:absolute;top:42%;width:100%;display:flex;justify-content:space-between;padding:0 6px;pointer-events:none;}
.c-arr{width:28px;height:28px;background:rgba(255,255,255,.9);border:1px solid var(--border);color:var(--sage);display:flex;align-items:center;justify-content:center;cursor:pointer;pointer-events:all;font-size:11px;border-radius:50%;transition:all .3s;}
.c-arr:hover{background:var(--sage);color:#fff;}
.qr-blk{margin:0 22px 20px;background:linear-gradient(135deg,var(--sage),#5a8660);border-radius:20px;padding:22px;display:flex;align-items:center;gap:20px;}
.qr-box{background:#fff;padding:6px;width:88px;height:88px;flex-shrink:0;border-radius:10px;}
.qr-box img{width:100%;height:100%;display:block;}
.qr-info h4{font-family:'Philosopher',serif;font-size:18px;color:#fff;margin-bottom:4px;}
.qr-info p{font-size:12px;color:rgba(255,255,255,.85);line-height:1.6;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 44px);margin:0 22px 20px;padding:14px;background:linear-gradient(135deg,var(--sage),#5a8660);color:#fff;font-size:12px;font-weight:700;border-radius:30px;cursor:pointer;border:none;transition:all .3s;}
.save-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(122,158,126,.4);}
.share{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border-radius:12px;border:1px solid var(--border);color:var(--sub);font-size:12px;text-decoration:none;transition:all .3s;cursor:pointer;background:transparent;}
.sh:hover{background:rgba(122,158,126,.1);border-color:var(--sage);color:var(--sage);}
.copy-row{display:flex;border:1px solid var(--border);border-radius:12px;overflow:hidden;background:var(--cream);}
.copy-inp{flex:1;background:transparent;border:none;color:var(--sub);font-size:11px;padding:10px 12px;font-family:'Nunito',sans-serif;outline:none;}
.cp-btn{background:var(--sage);color:#fff;border:none;padding:10px 16px;cursor:pointer;font-weight:700;font-size:11px;}
.footer{text-align:center;padding:14px;font-size:10px;letter-spacing:2px;color:var(--sub);}
.footer span{color:var(--sage);}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}.fade-in-section.visible{opacity:1;transform:none;}
</style>
</head>
<body>
<div class="desk"><div class="mandala m1"></div><div class="mandala m2"></div><div class="leaf l1">🌿</div><div class="leaf l2">🍃</div><div class="leaf l3">☘️</div></div>
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
      <div class="b-studio"><?= htmlspecialchars($vcard['company'] ?? $fullName) ?></div>
      <div class="b-tag"><?= htmlspecialchars($vcard['job_title'] ?? 'Yoga · Meditation · Healing') ?></div>
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
    <div class="certs">
      <span class="cert">RYT 500</span>
      <span class="cert">Meditation</span>
      <span class="cert">Wellness</span>
    </div>
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
    <div class="sec-h"><span>Contact</span></div>
    <div class="cl">
      <?php if (!empty($vcard['email'])): ?>
      <a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="ci"><div class="ci-ic"><i class="fas fa-envelope"></i></div><div><div class="ci-l">Email</div><div class="ci-v"><?= htmlspecialchars($vcard['email']) ?></div></div></a>
      <?php endif; ?>
      <?php if (!empty($vcard['phone'])): ?>
      <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="ci"><div class="ci-ic"><i class="fas fa-phone"></i></div><div><div class="ci-l">Studio Phone</div><div class="ci-v"><?= htmlspecialchars($vcard['phone']) ?></div></div></a>
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
  <div class="sec fade-in-section"><div class="sec-h"><span>Classes &amp; Services</span></div></div>
  <div class="car-wrap fade-in-section" id="carousel14">
    <div class="car-track" id="carousel14-track">
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
    <div class="c-arrows"><div class="c-arr" id="carousel14-prev"><i class="fas fa-chevron-left"></i></div><div class="c-arr" id="carousel14-next"><i class="fas fa-chevron-right"></i></div></div>
    <div class="c-dots" id="carousel14-dots"></div>
  </div>
  <?php endif; ?>
<?php include __DIR__ . '/_features.php'; ?>
  <div class="qr-blk fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code"></div>
    <div class="qr-info"><h4>Begin Your Journey</h4><p>Scan to book a class or free discovery session.</p></div>
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
  <div class="footer">Powered by <span>Tapify</span> · Wellness Edition</div>
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
initCarousel('carousel14');
</script>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
