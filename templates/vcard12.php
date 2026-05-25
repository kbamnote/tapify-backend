<?php
/**
 * Tapify vCard Template: vcard12
 * Financial Advisor — Navy/Gold light theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://'.$_SERVER['HTTP_HOST'].'/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['job_title'] ?? 'Financial Advisor') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#f0f4f8;--white:#fff;--navy:#0f2b52;--navy2:#1a3f73;--silver:#8a9bb5;--accent:#2563eb;--gold:#c8922a;--text:#2d3748;--sub:#64748b;--card:#fff;--border:#dde4ef;}
body{background:var(--bg);font-family:'Source Sans 3',sans-serif;color:var(--text);}
.wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--white);overflow:hidden;box-shadow:0 0 60px rgba(15,43,82,.1);}

/* DESKTOP BG */
@media(min-width:600px){
.desk-bg{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.floatcircle{position:absolute;border-radius:50%;border:1px solid var(--navy);opacity:.06;animation:fcAnim ease-in-out infinite alternate;}
.fc1{width:400px;height:400px;top:-100px;right:-100px;animation-duration:10s;}
.fc2{width:250px;height:250px;bottom:50px;left:-80px;animation-duration:13s;animation-delay:3s;}
@keyframes fcAnim{0%{transform:scale(1) rotate(0deg);}100%{transform:scale(1.05) rotate(5deg);}}
}

/* BANNER */
.banner{height:200px;position:relative;overflow:hidden;background:linear-gradient(135deg,var(--navy),var(--navy2));}
.banner-pattern{position:absolute;inset:0;background-image:repeating-linear-gradient(45deg,rgba(255,255,255,.02) 0,rgba(255,255,255,.02) 1px,transparent 0,transparent 50%);background-size:20px 20px;}
.ticker{position:absolute;top:0;left:0;right:0;background:rgba(200,146,42,.9);padding:5px 0;overflow:hidden;white-space:nowrap;}
.ticker-inner{display:inline-block;animation:ticker 18s linear infinite;font-size:10px;font-weight:700;letter-spacing:1px;color:#fff;}
@keyframes ticker{0%{transform:translateX(100%);}100%{transform:translateX(-100%);}}
.banner-content{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;padding-top:24px;}
.firm-logo{font-family:'EB Garamond',serif;font-size:28px;color:var(--white);letter-spacing:4px;text-align:center;}
.firm-tag{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:rgba(255,255,255,.5);margin-top:4px;}
.banner-bar{position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--gold),#f0c060,var(--gold));}

/* PROFILE */
.prof{background:var(--white);padding:0 22px 20px;position:relative;z-index:5;}
.prof-top{display:flex;align-items:flex-end;justify-content:space-between;margin-top:-48px;margin-bottom:14px;}
.av{position:relative;}
.av img{width:96px;height:96px;border-radius:4px;object-fit:cover;border:4px solid var(--white);box-shadow:0 4px 16px rgba(15,43,82,.2);}
.av-cert{position:absolute;bottom:-8px;left:50%;transform:translateX(-50%);background:var(--gold);color:#fff;font-size:9px;font-weight:700;letter-spacing:1px;padding:2px 10px;white-space:nowrap;}
.appt-btn{display:flex;align-items:center;gap:8px;background:var(--navy);color:#fff;font-size:11px;font-weight:600;padding:11px 16px;text-decoration:none;letter-spacing:1px;transition:all .3s;border-bottom:2px solid var(--gold);}
.appt-btn:hover{background:var(--navy2);}
.name{font-family:'EB Garamond',serif;font-size:26px;color:var(--navy);margin:14px 0 4px;}
.title{font-size:11px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--sub);margin-bottom:3px;}
.co{font-size:13px;color:var(--text);margin-bottom:3px;}
.tagline{font-family:'EB Garamond',serif;font-style:italic;font-size:15px;color:var(--gold);margin-bottom:12px;}
.stats{display:flex;gap:0;border:1px solid var(--border);margin:12px 0;}
.stat{flex:1;text-align:center;padding:12px 6px;border-right:1px solid var(--border);}
.stat:last-child{border:none;}
.stat-n{font-family:'EB Garamond',serif;font-size:22px;color:var(--navy);display:block;}
.stat-l{font-size:9px;text-transform:uppercase;letter-spacing:1px;color:var(--sub);}
.soc-row{display:flex;gap:8px;padding:10px 0;}
.soc{width:36px;height:36px;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--sub);font-size:14px;text-decoration:none;transition:all .3s;}
.soc:hover{background:var(--navy);border-color:var(--navy);color:#fff;}
.web-link{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--accent);text-decoration:none;margin-bottom:6px;}

/* BIO */
.bio{background:#f8fafc;border-left:3px solid var(--gold);padding:16px 18px;margin:0 22px 20px;}
.bio p{font-size:13px;line-height:1.9;color:var(--text);}

/* SECTION */
.sec{padding:0 22px;margin-bottom:20px;}
.sec-h{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
.sec-h span{font-family:'EB Garamond',serif;font-size:17px;color:var(--navy);}
.sec-h::after{content:'';flex:1;height:1px;background:var(--border);}

/* CONTACT */
.cl{display:flex;flex-direction:column;gap:8px;}
.ci{display:flex;align-items:center;gap:12px;padding:12px 14px;background:#f8fafc;border-left:3px solid transparent;text-decoration:none;color:var(--text);transition:all .3s;}
.ci:hover{border-left-color:var(--navy);background:var(--white);transform:translateX(4px);}
.ci-ic{width:34px;height:34px;background:var(--navy);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;flex-shrink:0;}
.ci-l{font-size:10px;color:var(--sub);letter-spacing:.5px;margin-bottom:1px;}
.ci-v{font-size:12px;font-weight:600;word-break:break-word;}

/* CAROUSEL */
.car-wrap{position:relative;overflow:hidden;margin-bottom:20px;}
.car-track{display:flex;gap:12px;padding:4px 22px;transition:transform .5s cubic-bezier(.25,.46,.45,.94);}
.c-card{min-width:155px;flex-shrink:0;background:var(--white);border:1px solid var(--border);border-bottom:3px solid var(--navy);transition:all .3s;}
.c-card:hover{box-shadow:0 6px 20px rgba(15,43,82,.1);transform:translateY(-4px);}
.c-card img{width:100%;height:90px;object-fit:cover;display:block;filter:saturate(.7);}
.c-body{padding:10px 12px;}
.c-title{font-family:'EB Garamond',serif;font-size:14px;color:var(--navy);margin-bottom:3px;}
.c-desc{font-size:11px;color:var(--sub);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.c-dots{display:flex;justify-content:center;gap:6px;padding:8px 0;}
.dot{width:6px;height:6px;border-radius:50%;background:var(--border);transition:all .3s;cursor:pointer;}
.dot.active{background:var(--navy);width:18px;border-radius:3px;}
.c-arrows{position:absolute;top:45%;transform:translateY(-50%);width:100%;display:flex;justify-content:space-between;padding:0 6px;pointer-events:none;}
.c-arr{width:28px;height:28px;background:var(--white);border:1px solid var(--border);color:var(--navy);display:flex;align-items:center;justify-content:center;cursor:pointer;pointer-events:all;font-size:11px;transition:all .3s;}
.c-arr:hover{background:var(--navy);color:#fff;}

/* QR */
.qr-blk{margin:0 22px 20px;background:var(--navy);padding:22px;display:flex;align-items:center;gap:20px;}
.qr-box{background:#fff;padding:6px;width:88px;height:88px;flex-shrink:0;}
.qr-box img{width:100%;height:100%;display:block;}
.qr-info h4{font-family:'EB Garamond',serif;font-size:18px;color:var(--gold);margin-bottom:4px;}
.qr-info p{font-size:12px;color:rgba(255,255,255,.7);line-height:1.6;}

/* SAVE */
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 44px);margin:0 22px 20px;padding:14px;background:linear-gradient(135deg,var(--navy),var(--navy2));color:#fff;font-size:12px;font-weight:600;letter-spacing:2px;text-transform:uppercase;cursor:pointer;border:none;transition:all .3s;border-bottom:2px solid var(--gold);}
.save-btn:hover{opacity:.9;transform:translateY(-2px);}

/* SHARE */
.share{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border:1px solid var(--border);color:var(--sub);font-size:12px;text-decoration:none;transition:all .3s;cursor:pointer;background:transparent;}
.sh:hover{background:var(--navy);border-color:var(--navy);color:#fff;}
.copy-row{display:flex;border:1px solid var(--border);}
.copy-inp{flex:1;background:#f8fafc;border:none;color:var(--sub);font-size:11px;padding:10px 12px;font-family:'Source Sans 3',sans-serif;outline:none;}
.cp-btn{background:var(--navy);color:#fff;border:none;padding:10px 16px;cursor:pointer;font-weight:600;font-size:11px;}
.cp-btn:hover{background:var(--gold);}
.footer{text-align:center;padding:14px;font-size:10px;letter-spacing:2px;color:var(--sub);background:#f8fafc;}
.footer span{color:var(--navy);}
.fade-in-section{opacity:0;transform:translateY(20px);transition:all .6s ease;}.fade-in-section.visible{opacity:1;transform:none;}
</style>
</head>
<body>
<div class="desk-bg">
  <div class="floatcircle fc1"></div><div class="floatcircle fc2"></div>
  <svg style="position:fixed;inset:0;width:100%;height:100%;opacity:.03;pointer-events:none;" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="hex" x="0" y="0" width="40" height="46" patternUnits="userSpaceOnUse"><polygon points="20,2 38,12 38,34 20,44 2,34 2,12" fill="none" stroke="#0f2b52" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(#hex)"/></svg>
</div>
<div class="wrap">
  <div class="banner">
    <div class="banner-pattern"></div>
    <div class="ticker"><span class="ticker-inner">SENSEX: ▲ 72,415 &nbsp;·&nbsp; NIFTY: ▲ 21,928 &nbsp;·&nbsp; GOLD: ▲ ₹71,200 &nbsp;·&nbsp; <?= htmlspecialchars($vcard['company'] ?? 'Wealth Management') ?> &nbsp;·&nbsp; Financial Excellence &nbsp;&nbsp;&nbsp;&nbsp;</span></div>
    <div class="banner-content">
      <div class="firm-logo"><?= htmlspecialchars($vcard['company'] ?? $fullName) ?></div>
      <div class="firm-tag"><?= htmlspecialchars($vcard['job_title'] ?? 'Wealth Management') ?></div>
    </div>
    <div class="banner-bar"></div>
  </div>
  <div class="prof">
    <div class="prof-top">
      <div class="av">
        <img src="<?= $profileImg ?>" alt="<?= htmlspecialchars($fullName) ?>">
        <?php if (!empty($vcard['suffix'])): ?><div class="av-cert"><?= htmlspecialchars($vcard['suffix']) ?></div><?php endif; ?>
      </div>
      <?php if (!empty($vcard['phone'])): ?>
      <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="appt-btn"><i class="fas fa-calendar-check"></i> Book Meet</a>
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
    <div class="stats">
      <div class="stat"><span class="stat-n">—</span><span class="stat-l">AUM</span></div>
      <div class="stat"><span class="stat-n">—</span><span class="stat-l">Experience</span></div>
      <div class="stat"><span class="stat-n">—</span><span class="stat-l">Clients</span></div>
    </div>
    <?php if (!empty($socialLinks)): ?>
    <div class="soc-row">
      <?php foreach ($socialLinks as $s): $icon = $platformIcons[$s['platform']] ?? 'fa-globe'; ?>
      <a href="<?= htmlspecialchars($s['url']) ?>" class="soc" target="_blank" rel="noopener"><i class="fab <?= $icon ?>"></i></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($vcard['website'])): ?>
    <a href="<?= htmlspecialchars($vcard['website']) ?>" class="web-link" target="_blank" rel="noopener"><i class="fas fa-globe"></i> <?= htmlspecialchars(preg_replace('#^https?://#','',$vcard['website'])) ?></a>
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
  <div class="sec fade-in-section"><div class="sec-h"><span>Services</span></div></div>
  <div class="car-wrap fade-in-section" id="carousel12">
    <div class="car-track" id="carousel12-track">
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
    <div class="c-arrows"><div class="c-arr" id="carousel12-prev"><i class="fas fa-chevron-left"></i></div><div class="c-arr" id="carousel12-next"><i class="fas fa-chevron-right"></i></div></div>
    <div class="c-dots" id="carousel12-dots"></div>
  </div>
  <?php endif; ?>
  <div class="qr-blk fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code"></div>
    <div class="qr-info"><h4>Book Consultation</h4><p>Scan to schedule a free financial review session.</p></div>
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
  <div class="footer">Powered by <span>Tapify</span> · Finance Edition</div>
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
  let idx=0;const cardW=155,gap=12;
  if(dotsEl){dotsEl.innerHTML='';cards.forEach((_,i)=>{const d=document.createElement('div');d.className='dot'+(i===0?' active':'');d.onclick=()=>goTo(i);dotsEl.appendChild(d);});}
  function goTo(n){idx=(n+total)%total;track.style.transform=`translateX(calc(-${idx}*(${cardW}px + ${gap}px)))`;if(dotsEl)dotsEl.querySelectorAll('.dot').forEach((d,i)=>d.classList.toggle('active',i===idx));}
  const prev=document.getElementById(id+'-prev');const next=document.getElementById(id+'-next');
  if(prev)prev.onclick=()=>goTo(idx-1);if(next)next.onclick=()=>goTo(idx+1);
  setInterval(()=>goTo(idx+1),3800);
}
initCarousel('carousel12');
</script>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
