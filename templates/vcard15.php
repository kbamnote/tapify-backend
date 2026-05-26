<?php
/**
 * Tapify vCard Template: vcard15
 * Digital Marketing Agency — Dark Violet/Pink/Cyan theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://tapify-backend-production.up.railway.app/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['job_title'] ?? 'Digital Marketing') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#060614;--card:#0d0d28;--violet:#6c47ff;--pink:#ff47b8;--cyan:#47e8ff;--white:#f0f0ff;--text:#8888bb;--border:#1a1a3e;}
body{background:var(--bg);font-family:'Plus Jakarta Sans',sans-serif;color:var(--white);overflow-x:hidden;}
.wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--bg);position:relative;overflow:hidden;}
@media(min-width:600px){
.desk{position:fixed;inset:0;pointer-events:none;z-index:0;}
.grd{position:absolute;width:100%;height:100%;background-image:linear-gradient(rgba(108,71,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(108,71,255,.03) 1px,transparent 1px);background-size:30px 30px;}
.orb{position:absolute;border-radius:50%;filter:blur(70px);animation:orbm ease-in-out infinite alternate;}
.o1{width:350px;height:350px;background:rgba(108,71,255,.18);top:-100px;right:-100px;animation-duration:9s;}
.o2{width:250px;height:250px;background:rgba(255,71,184,.14);bottom:100px;left:-80px;animation-duration:12s;animation-delay:4s;}
.o3{width:150px;height:150px;background:rgba(71,232,255,.1);top:50%;left:40%;animation-duration:7s;animation-delay:2s;}
@keyframes orbm{0%{transform:translate(0,0);}100%{transform:translate(20px,-25px);}}}
.banner{height:230px;position:relative;overflow:hidden;background:linear-gradient(135deg,#0d0028,#0a001a);}
.banner-anim{position:absolute;inset:0;}
.banner-anim span{position:absolute;display:block;border-radius:50%;border:1px solid rgba(108,71,255,.2);animation:ripple 4s linear infinite;}
.banner-anim span:nth-child(1){width:100px;height:100px;top:30%;left:20%;animation-delay:0s;}
.banner-anim span:nth-child(2){width:150px;height:150px;top:20%;right:15%;animation-delay:1.5s;}
.banner-anim span:nth-child(3){width:80px;height:80px;bottom:25%;left:55%;animation-delay:3s;}
@keyframes ripple{0%{transform:scale(1);opacity:.6;}100%{transform:scale(2.5);opacity:0;}}
.b-content{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:20px;}
.b-agency{font-size:30px;font-weight:800;background:linear-gradient(135deg,var(--violet),var(--pink),var(--cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:-1px;margin-bottom:6px;}
.b-tag{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--text);}
.b-bar{position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--violet),var(--pink),var(--cyan));}
.prof{padding:0 22px 20px;position:relative;z-index:5;}
.ptop{display:flex;align-items:flex-end;justify-content:space-between;margin-top:-44px;margin-bottom:16px;}
.av{width:88px;height:88px;position:relative;}
.av img{width:100%;height:100%;border-radius:16px;object-fit:cover;border:3px solid var(--violet);}
.hire{display:flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--violet),var(--pink));color:#fff;font-size:11px;font-weight:700;padding:11px 18px;border-radius:25px;text-decoration:none;letter-spacing:1px;transition:all .3s;}
.hire:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(108,71,255,.4);}
.name{font-size:22px;font-weight:800;margin-bottom:4px;}
.title{font-size:11px;color:var(--violet);letter-spacing:2px;text-transform:uppercase;margin-bottom:3px;font-weight:600;}
.co{font-size:13px;color:var(--text);margin-bottom:4px;}
.tagline{font-size:13px;color:var(--text);font-style:italic;margin-bottom:12px;}
.metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin:12px 0;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:12px 8px;}
.metric{text-align:center;}
.m-n{font-size:17px;font-weight:800;background:linear-gradient(135deg,var(--violet),var(--cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;display:block;}
.m-l{font-size:9px;color:var(--text);letter-spacing:.5px;}
.soc-row{display:flex;gap:8px;margin-bottom:10px;}
.soc{width:38px;height:38px;border-radius:10px;border:1px solid var(--border);background:var(--card);display:flex;align-items:center;justify-content:center;color:var(--text);font-size:15px;text-decoration:none;transition:all .3s;}
.soc:hover{border-color:var(--violet);color:var(--violet);transform:translateY(-3px);}
.web{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--cyan);text-decoration:none;}
.bio{margin:0 0 20px;padding:16px 18px;background:var(--card);border:1px solid var(--border);border-radius:12px;border-left:3px solid var(--violet);}
.bio p{font-size:13px;line-height:1.9;color:var(--text);}
.sec{margin-bottom:20px;}
.sec-h{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--violet);margin-bottom:14px;display:flex;align-items:center;gap:8px;font-weight:700;}
.sec-h::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--border),transparent);}
.cl{display:flex;flex-direction:column;gap:8px;}
.ci{display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--card);border:1px solid var(--border);border-radius:12px;text-decoration:none;color:var(--white);transition:all .3s;}
.ci:hover{border-color:var(--violet);background:rgba(108,71,255,.08);}
.ci-ic{width:34px;height:34px;background:rgba(108,71,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--violet);font-size:14px;flex-shrink:0;}
.ci-l{font-size:10px;color:var(--text);margin-bottom:1px;}
.ci-v{font-size:12px;font-weight:600;word-break:break-word;}
.car-wrap{position:relative;overflow:hidden;margin-bottom:20px;}
.car-track{display:flex;gap:12px;padding:4px 0;transition:transform .5s cubic-bezier(.25,.46,.45,.94);}
.c-card{min-width:155px;flex-shrink:0;background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:all .3s;}
.c-card:hover{border-color:var(--violet);transform:translateY(-4px);box-shadow:0 12px 30px rgba(108,71,255,.2);}
.c-card img{width:100%;height:95px;object-fit:cover;display:block;filter:saturate(.7);}
.c-card:hover img{filter:saturate(1);}
.c-body{padding:10px 12px;}
.c-title{font-size:12px;font-weight:700;margin-bottom:3px;}
.c-desc{font-size:11px;color:var(--text);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.c-dots{display:flex;justify-content:center;gap:6px;padding:8px 0;}
.dot{width:6px;height:6px;border-radius:50%;background:var(--border);transition:all .3s;cursor:pointer;}
.dot.active{background:var(--violet);width:18px;border-radius:3px;}
.c-arrows{position:absolute;top:42%;width:100%;display:flex;justify-content:space-between;padding:0 4px;pointer-events:none;}
.c-arr{width:28px;height:28px;background:rgba(13,13,40,.8);border:1px solid var(--border);color:var(--violet);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;pointer-events:all;font-size:11px;transition:all .3s;}
.c-arr:hover{background:var(--violet);color:#fff;}
.qr-blk{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:22px;display:flex;align-items:center;gap:20px;margin-bottom:20px;position:relative;overflow:hidden;}
.qr-blk::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--violet),var(--pink),var(--cyan));}
.qr-box{background:#fff;padding:6px;width:88px;height:88px;flex-shrink:0;border-radius:8px;}
.qr-box img{width:100%;height:100%;display:block;}
.qr-info h4{font-size:15px;font-weight:800;background:linear-gradient(135deg,var(--violet),var(--pink));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:4px;}
.qr-info p{font-size:12px;color:var(--text);line-height:1.6;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:14px;background:linear-gradient(135deg,var(--violet),var(--pink));color:#fff;font-size:12px;font-weight:800;border-radius:14px;cursor:pointer;border:none;transition:all .3s;margin-bottom:20px;letter-spacing:1px;}
.save-btn:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(108,71,255,.4);}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border:1px solid var(--border);border-radius:12px;color:var(--text);font-size:12px;text-decoration:none;transition:all .3s;cursor:pointer;background:transparent;}
.sh:hover{border-color:var(--violet);color:var(--violet);}
.copy-row{display:flex;border:1px solid var(--border);border-radius:12px;overflow:hidden;background:var(--card);}
.copy-inp{flex:1;background:transparent;border:none;color:var(--text);font-size:11px;padding:10px 12px;font-family:'Plus Jakarta Sans',sans-serif;outline:none;}
.cp-btn{background:linear-gradient(135deg,var(--violet),var(--pink));color:#fff;border:none;padding:10px 16px;cursor:pointer;font-weight:700;font-size:11px;}
.footer{text-align:center;padding:14px;font-size:10px;letter-spacing:2px;color:var(--text);}
.footer span{color:var(--violet);}
.inner{padding:0 22px;}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}.fade-in-section.visible{opacity:1;transform:none;}
</style>
</head>
<body>
<div class="desk"><div class="grd"></div><div class="orb o1"></div><div class="orb o2"></div><div class="orb o3"></div></div>
<div class="wrap">
  <div class="banner">
    <div class="banner-anim"><span></span><span></span><span></span></div>
    <div class="b-content">
      <div class="b-agency"><?= htmlspecialchars($vcard['company'] ?? $fullName) ?></div>
      <div class="b-tag"><?= htmlspecialchars($vcard['job_title'] ?? 'Digital Marketing & Growth') ?></div>
    </div>
    <div class="b-bar"></div>
  </div>
  <div class="inner">
    <div class="prof">
      <div class="ptop">
        <div class="av"><img src="<?= $profileImg ?>" alt="<?= htmlspecialchars($fullName) ?>"></div>
        <?php if (!empty($vcard['email'])): ?>
        <a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="hire"><i class="fas fa-rocket"></i> Let's Grow</a>
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
      <div class="metrics">
        <div class="metric"><span class="m-n">—</span><span class="m-l">Brands</span></div>
        <div class="metric"><span class="m-n">—</span><span class="m-l">Ad Spend</span></div>
        <div class="metric"><span class="m-n">—</span><span class="m-l">Years</span></div>
        <div class="metric"><span class="m-n">5★</span><span class="m-l">Rating</span></div>
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
      <div class="sec-h">Contact</div>
      <div class="cl">
        <?php if (!empty($vcard['email'])): ?>
        <a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="ci"><div class="ci-ic"><i class="fas fa-envelope"></i></div><div><div class="ci-l">Email</div><div class="ci-v"><?= htmlspecialchars($vcard['email']) ?></div></div></a>
        <?php endif; ?>
        <?php if (!empty($vcard['phone'])): ?>
        <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="ci"><div class="ci-ic"><i class="fas fa-phone"></i></div><div><div class="ci-l">Office</div><div class="ci-v"><?= htmlspecialchars($vcard['phone']) ?></div></div></a>
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
    <div class="car-wrap fade-in-section" id="carousel15">
      <div class="car-track" id="carousel15-track">
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
      <div class="c-arrows"><div class="c-arr" id="carousel15-prev"><i class="fas fa-chevron-left"></i></div><div class="c-arr" id="carousel15-next"><i class="fas fa-chevron-right"></i></div></div>
      <div class="c-dots" id="carousel15-dots"></div>
    </div>
    <?php endif; ?>
<?php include __DIR__ . '/_features.php'; ?>
    <div class="qr-blk fade-in-section">
      <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code"></div>
      <div class="qr-info"><h4>Free Audit</h4><p>Scan for a free digital marketing audit of your brand.</p></div>
    </div>
    <button class="save-btn fade-in-section" onclick="saveContact()"><i class="fas fa-address-card"></i>&nbsp; Save Contact</button>
    <div class="sh-row fade-in-section">
      <button class="sh" onclick="shareCard('whatsapp')"><i class="fab fa-whatsapp"></i> Share</button>
      <button class="sh" onclick="shareCard('linkedin')"><i class="fab fa-linkedin-in"></i> Share</button>
      <button class="sh" onclick="shareCard('native')"><i class="fas fa-share-alt"></i> Share</button>
    </div>
    <div class="copy-row fade-in-section"><input class="copy-inp" id="cardUrlInput" value="<?= htmlspecialchars($cardUrl) ?>" readonly><button class="cp-btn" onclick="navigator.clipboard.writeText(document.getElementById('cardUrlInput').value);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)">Copy</button></div>
  </div>
  <?php if (empty($vcard['remove_branding'])): ?>
  <div class="footer">Powered by <span>Tapify</span> · Agency Edition</div>
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
  setInterval(()=>goTo(idx+1),3600);
}
initCarousel('carousel15');
</script>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
