<?php
/**
 * Tapify vCard Template: vcard13
 * Architect — Warm Charcoal/Orange theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = PUBLIC_URL.'/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1486325212027-8081e485255e?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['job_title'] ?? 'Architect') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@300;400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#f7f5f0;--white:#fff;--charcoal:#1c1c1c;--warm:#e8e0d0;--accent:#c45c26;--text:#333;--sub:#888;--border:#ddd;}
body{background:var(--bg);font-family:'DM Sans',sans-serif;color:var(--text);}
.wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--white);overflow:hidden;box-shadow:0 0 60px rgba(0,0,0,.08);}
@media(min-width:600px){
.bg-arch{position:fixed;inset:0;pointer-events:none;z-index:0;}
.bp{position:absolute;border:1px solid rgba(28,28,28,.06);animation:bpanim ease-in-out infinite alternate;}
.bp1{width:350px;height:350px;top:-100px;right:-100px;animation-duration:12s;}
.bp2{width:200px;height:200px;bottom:100px;left:-60px;animation-duration:9s;animation-delay:4s;}
.bp3{width:500px;height:1px;background:rgba(28,28,28,.04);top:40%;left:0;animation:none;}
@keyframes bpanim{0%{transform:rotate(0deg);}100%{transform:rotate(15deg);}}}
.banner{height:240px;position:relative;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;filter:brightness(.7);}
.b-over{position:absolute;inset:0;background:linear-gradient(to bottom,transparent 30%,rgba(28,28,28,.9));}
.b-content{position:absolute;bottom:0;left:0;right:0;padding:22px;display:flex;align-items:flex-end;justify-content:space-between;}
.b-firm{font-family:'Unbounded',sans-serif;font-size:20px;font-weight:300;color:#fff;letter-spacing:2px;}
.b-label{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--accent);}
.b-bar{position:absolute;top:0;left:0;bottom:0;width:4px;background:var(--accent);}
.prof{padding:0 22px 20px;background:var(--white);position:relative;z-index:5;}
.ptop{display:flex;gap:16px;align-items:flex-end;margin-top:-40px;margin-bottom:16px;}
.av{width:80px;height:80px;position:relative;flex-shrink:0;}
.av img{width:100%;height:100%;object-fit:cover;border:4px solid var(--white);}
.av::before{content:'';position:absolute;inset:-3px;background:var(--accent);z-index:-1;}
.pname{font-family:'Unbounded',sans-serif;font-size:20px;font-weight:600;color:var(--charcoal);margin-bottom:4px;}
.ptitle{font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--accent);margin-bottom:3px;}
.pco{font-size:12px;color:var(--sub);}
.tagline{font-size:13px;color:var(--sub);font-style:italic;margin:8px 0 12px;}
.awards{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;}
.aw{font-size:10px;padding:3px 10px;border:1px solid var(--border);color:var(--sub);font-weight:600;letter-spacing:.5px;}
.soc-row{display:flex;gap:8px;margin-bottom:10px;}
.soc{width:36px;height:36px;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--sub);font-size:14px;text-decoration:none;transition:all .3s;}
.soc:hover{background:var(--charcoal);border-color:var(--charcoal);color:#fff;}
.web{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--accent);text-decoration:none;}
.bio{background:var(--warm);padding:18px;margin:0 22px 20px;border-left:3px solid var(--accent);}
.bio p{font-size:13px;line-height:1.9;color:var(--text);}
.sec{padding:0 22px;margin-bottom:20px;}
.sec-h{font-family:'Unbounded',sans-serif;font-size:11px;font-weight:400;letter-spacing:3px;text-transform:uppercase;color:var(--charcoal);margin-bottom:14px;display:flex;align-items:center;gap:10px;}
.sec-h::after{content:'';flex:1;height:1px;background:var(--border);}
.cl{display:flex;flex-direction:column;gap:8px;}
.ci{display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--bg);border-left:3px solid transparent;text-decoration:none;color:var(--text);transition:all .3s;}
.ci:hover{border-left-color:var(--accent);background:var(--white);transform:translateX(4px);}
.ci-ic{width:34px;height:34px;background:var(--charcoal);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;flex-shrink:0;}
.ci-l{font-size:10px;color:var(--sub);margin-bottom:1px;}
.ci-v{font-size:12px;font-weight:500;word-break:break-word;}
.car-wrap{position:relative;overflow:hidden;margin-bottom:20px;}
.car-track{display:flex;gap:12px;padding:4px 22px;transition:transform .5s cubic-bezier(.25,.46,.45,.94);}
.c-card{min-width:160px;flex-shrink:0;transition:all .3s;}
.c-card img{width:100%;height:110px;object-fit:cover;display:block;filter:grayscale(.2);}
.c-card:hover img{filter:grayscale(0);}
.c-card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(0,0,0,.15);}
.c-body{padding:10px 12px;background:var(--charcoal);color:#fff;}
.c-title{font-family:'Unbounded',sans-serif;font-size:11px;font-weight:400;letter-spacing:1px;margin-bottom:3px;}
.c-desc{font-size:10px;color:rgba(255,255,255,.6);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.c-dots{display:flex;justify-content:center;gap:6px;padding:8px 0;}
.dot{width:6px;height:6px;border-radius:50%;background:var(--border);transition:all .3s;cursor:pointer;}
.dot.active{background:var(--accent);width:18px;border-radius:3px;}
.c-arrows{position:absolute;top:40%;width:100%;display:flex;justify-content:space-between;padding:0 6px;pointer-events:none;}
.c-arr{width:28px;height:28px;background:rgba(255,255,255,.9);border:1px solid var(--border);color:var(--charcoal);display:flex;align-items:center;justify-content:center;cursor:pointer;pointer-events:all;font-size:11px;transition:all .3s;}
.c-arr:hover{background:var(--accent);color:#fff;border-color:var(--accent);}
.qr-blk{margin:0 22px 20px;background:var(--charcoal);padding:22px;display:flex;align-items:center;gap:20px;}
.qr-box{background:#fff;padding:6px;width:88px;height:88px;flex-shrink:0;}
.qr-box img{width:100%;height:100%;display:block;}
.qr-info h4{font-family:'Unbounded',sans-serif;font-size:14px;font-weight:400;color:var(--accent);margin-bottom:4px;}
.qr-info p{font-size:12px;color:rgba(255,255,255,.7);line-height:1.6;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 44px);margin:0 22px 20px;padding:14px;background:var(--accent);color:#fff;font-size:12px;font-weight:600;letter-spacing:2px;text-transform:uppercase;cursor:pointer;border:none;transition:all .3s;}
.save-btn:hover{opacity:.9;transform:translateY(-2px);}
.share{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border:1px solid var(--border);color:var(--sub);font-size:12px;text-decoration:none;transition:all .3s;cursor:pointer;background:transparent;}
.sh:hover{background:var(--charcoal);border-color:var(--charcoal);color:#fff;}
.copy-row{display:flex;border:1px solid var(--border);}
.copy-inp{flex:1;background:var(--bg);border:none;color:var(--sub);font-size:11px;padding:10px 12px;font-family:'DM Sans',sans-serif;outline:none;}
.cp-btn{background:var(--accent);color:#fff;border:none;padding:10px 16px;cursor:pointer;font-weight:600;font-size:11px;}
.footer{text-align:center;padding:14px;font-size:10px;letter-spacing:2px;color:var(--sub);background:var(--bg);}
.footer span{color:var(--accent);}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}.fade-in-section.visible{opacity:1;transform:none;}
</style>
</head>
<body>
<div class="bg-arch"><div class="bp bp1"></div><div class="bp bp2"></div><div class="bp bp3"></div></div>
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
    <div class="b-bar"></div>
    <div class="b-content">
      <div>
        <div class="b-label"><?= htmlspecialchars($vcard['job_title'] ?? 'Architecture Studio') ?></div>
        <div class="b-firm"><?= htmlspecialchars($vcard['company'] ?? $fullName) ?></div>
      </div>
    </div>
  </div>
  <div class="prof">
    <div class="ptop">
      <div class="av"><img src="<?= $profileImg ?>" alt="<?= htmlspecialchars($fullName) ?>"></div>
      <div>
        <div class="pname"><?= htmlspecialchars($fullName) ?></div>
        <div class="ptitle"><?= htmlspecialchars($vcard['job_title'] ?? '') ?></div>
        <?php if (!empty($vcard['company']) || !empty($vcard['location'])): ?>
        <div class="pco"><?= htmlspecialchars(implode(' · ', array_filter([$vcard['company'] ?? '', $vcard['location'] ?? '']))) ?></div>
        <?php endif; ?>
      </div>
    </div>
    <?php if (!empty($vcard['occupation'])): ?>
    <div class="tagline">"<?= htmlspecialchars($vcard['occupation']) ?>"</div>
    <?php endif; ?>
    <div class="awards">
      <span class="aw">COA Registered</span>
      <span class="aw">LEED AP</span>
      <span class="aw">IIA Member</span>
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
  <?php if (!empty($vcard['description'])): ?>
  <div class="bio fade-in-section"><p><?= renderDescription($vcard['description']) ?></p></div>
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
  <div class="sec fade-in-section"><div class="sec-h">Projects</div></div>
  <div class="car-wrap fade-in-section" id="carousel13">
    <div class="car-track" id="carousel13-track">
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
    <div class="c-arrows"><div class="c-arr" id="carousel13-prev"><i class="fas fa-chevron-left"></i></div><div class="c-arr" id="carousel13-next"><i class="fas fa-chevron-right"></i></div></div>
    <div class="c-dots" id="carousel13-dots"></div>
  </div>
  <?php endif; ?>
<?php include __DIR__ . '/_features.php'; ?>
  <div class="qr-blk fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code"></div>
    <div class="qr-info"><h4>View Portfolio</h4><p>Scan to explore our full project portfolio and services.</p></div>
  </div>
  <button class="save-btn fade-in-section" onclick="saveContact()"><i class="fas fa-address-card"></i>&nbsp; Save Contact</button>
  <div class="share fade-in-section">
    <div class="sh-row">
      <button class="sh" onclick="shareCard('whatsapp')"><i class="fab fa-whatsapp"></i> Share</button>
      <button class="sh" onclick="shareCard('linkedin')"><i class="fab fa-linkedin-in"></i> Share</button>
      <button class="sh" onclick="shareCard('native')"><i class="fas fa-share-alt"></i> Share</button>
    </div>
    <div class="copy-row"><input class="copy-inp" id="cardUrlInput" value="<?= htmlspecialchars($cardUrl) ?>" readonly><button class="cp-btn" onclick="navigator.clipboard.writeText(document.getElementById('cardUrlInput').value);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)">Copy</button></div>
  </div>
  <?php if (empty($vcard['remove_branding'])): ?>
  <div class="footer">Powered by <span>Tapify</span> · Architecture Edition</div>
  <?php endif; ?>
  <div style="text-align:center;font-size:0.7rem;padding:0 16px 14px;color:inherit;opacity:0.6;">An innovative Product From : <strong>Mr Print World Pvt Ltd.</strong></div>
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
initCarousel('carousel13');
</script>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
