<?php
/**
 * Tapify vCard Template: vcard18
 * Dentist — Teal/Mint light theme
 * Standalone template — variables injected by vcard.php router
 */
$cardUrl = 'https://'.$_SERVER['HTTP_HOST'].'/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=cccccc&color=333333';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($cardUrl);
$platformIcons = ['linkedin-in'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','globe'=>'fa-globe'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($fullName) ?> | <?= htmlspecialchars($vcard['job_title'] ?? 'Dentist') ?></title>
<link rel="icon" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
  --bg:#f0fffe;--white:#ffffff;--teal:#0891b2;--teal2:#06b6d4;
  --mint:#ccfbf1;--dark:#0c4a6e;--text:#1e3a5f;--sub:#64748b;
  --card:#f8ffff;--border:#a5f3fc;--gold:#f59e0b;
}
body{background:var(--bg);font-family:'Plus Jakarta Sans',sans-serif;color:var(--text);overflow-x:hidden;}
.card-wrap{max-width:480px;margin:0 auto;min-height:100vh;background:var(--white);box-shadow:0 0 80px rgba(8,145,178,.15);position:relative;overflow:hidden;}
@media(min-width:768px){
  .particles-canvas{display:block!important;}
  .card-wrap{box-shadow:0 0 120px rgba(8,145,178,.2);}
}
.particles-canvas{display:none;position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;}
@media(min-width:768px){
  .desktop-bg{display:flex!important;}
}
.desktop-bg{display:none;position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;}
.float-shape{position:absolute;border-radius:50%;opacity:.06;animation:floatUp 8s infinite ease-in-out;}
.float-shape:nth-child(1){width:120px;height:120px;background:var(--teal2);top:5%;left:3%;animation-delay:0s;}
.float-shape:nth-child(2){width:80px;height:80px;background:var(--dark);top:20%;right:5%;animation-delay:2s;}
.float-shape:nth-child(3){width:160px;height:160px;background:var(--teal);top:50%;left:-4%;animation-delay:4s;}
.float-shape:nth-child(4){width:100px;height:100px;background:var(--teal2);top:70%;right:2%;animation-delay:1s;}
.float-shape:nth-child(5){width:60px;height:60px;background:var(--dark);top:85%;left:10%;animation-delay:3s;}
.float-cross{position:absolute;color:var(--teal2);font-size:24px;opacity:.05;animation:floatUp 10s infinite ease-in-out;}
.float-cross:nth-child(6){top:15%;left:8%;animation-delay:5s;}
.float-cross:nth-child(7){top:40%;right:8%;animation-delay:7s;}
.float-cross:nth-child(8){top:65%;left:6%;animation-delay:2s;}
@keyframes floatUp{0%,100%{transform:translateY(0) rotate(0deg);}50%{transform:translateY(-30px) rotate(10deg);}}
.banner{position:relative;height:230px;overflow:hidden;}
.banner img{width:100%;height:100%;object-fit:cover;transition:transform 8s ease;}
.banner:hover img{transform:scale(1.06);}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(160deg,rgba(6,182,212,.5),rgba(12,74,110,.8));}
.banner-badge{position:absolute;top:18px;right:18px;background:rgba(255,255,255,.2);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.3);color:#fff;font-size:10px;font-weight:700;letter-spacing:2px;padding:7px 16px;border-radius:30px;}
.teal-bar{position:absolute;bottom:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--teal2),#67e8f9,var(--teal2));background-size:200%;animation:shimmer 3s linear infinite;}
@keyframes shimmer{0%{background-position:0%;}100%{background-position:200%;}}
.profile-area{padding:0 22px 18px;background:var(--white);position:relative;z-index:5;}
.profile-top{display:flex;align-items:flex-end;justify-content:space-between;margin-top:-52px;margin-bottom:14px;}
.av{position:relative;width:104px;height:104px;}
.av img{width:100%;height:100%;border-radius:50%;object-fit:cover;border:4px solid #fff;box-shadow:0 4px 24px rgba(8,145,178,.3);}
.av-ring{position:absolute;inset:-4px;border-radius:50%;background:conic-gradient(var(--teal2),#67e8f9,var(--teal),var(--teal2));animation:spin 5s linear infinite;z-index:-1;}
@keyframes spin{to{transform:rotate(360deg);}}
.appt-btn{display:flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--teal),var(--dark));color:#fff;font-size:11px;font-weight:700;padding:11px 18px;border-radius:30px;text-decoration:none;letter-spacing:1px;transition:all .3s;}
.appt-btn:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(8,145,178,.4);}
.doc-name{font-family:'DM Serif Display',serif;font-size:26px;color:var(--dark);margin-bottom:4px;}
.tagline{font-size:12px;color:var(--teal);font-style:italic;margin-bottom:4px;font-weight:500;}
.doc-title{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--sub);margin-bottom:3px;}
.doc-company{font-size:13px;color:var(--text);}
.stats-row{display:flex;gap:0;margin:14px 0;border:1px solid var(--border);border-radius:16px;overflow:hidden;}
.stat{flex:1;text-align:center;padding:12px 6px;border-right:1px solid var(--border);}
.stat:last-child{border:none;}
.stat-num{font-family:'DM Serif Display',serif;font-size:22px;color:var(--teal);display:block;}
.stat-lbl{font-size:9px;text-transform:uppercase;letter-spacing:1px;color:var(--sub);}
.social-row{display:flex;gap:10px;padding:10px 0;}
.soc{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:16px;text-decoration:none;background:var(--mint);color:var(--teal);transition:all .3s;}
.soc:hover{background:var(--teal);color:#fff;transform:translateY(-3px);}
.bio-wrap{margin:0 22px 18px;background:linear-gradient(135deg,var(--mint),#f0fdfa);border-radius:18px;padding:18px 20px;border-left:4px solid var(--teal2);}
.bio-wrap p{font-size:13px;line-height:1.9;color:var(--text);}
.section{padding:0 22px 18px;}
.sec-head{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
.sec-icon{width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,var(--teal),var(--dark));display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;flex-shrink:0;}
.sec-title{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--dark);}
.sec-head::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--border),transparent);}
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.ci{display:flex;align-items:flex-start;gap:10px;padding:13px 14px;background:var(--card);border:1px solid var(--border);border-radius:14px;text-decoration:none;color:var(--text);transition:all .3s;}
.ci:hover{background:var(--mint);border-color:var(--teal2);transform:translateY(-2px);}
.ci-ico{width:34px;height:34px;border-radius:10px;background:var(--teal);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;flex-shrink:0;}
.ci-lbl{font-size:9px;color:var(--sub);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;}
.ci-val{font-size:12px;font-weight:600;word-break:break-word;}
.carousel-wrap{position:relative;overflow:hidden;margin-bottom:4px;}
.carousel-track{display:flex;gap:12px;transition:transform .5s cubic-bezier(.4,0,.2,1);padding:4px 2px;}
.srv-card{min-width:160px;background:var(--white);border:1px solid var(--border);border-radius:16px;overflow:hidden;flex-shrink:0;transition:all .3s;box-shadow:0 2px 12px rgba(8,145,178,.08);}
.srv-card:hover{transform:translateY(-5px);box-shadow:0 12px 30px rgba(8,145,178,.18);border-color:var(--teal2);}
.srv-card img{width:100%;height:96px;object-fit:cover;display:block;}
.srv-body{padding:10px 12px;}
.srv-title{font-size:12px;font-weight:700;color:var(--dark);margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.srv-desc{font-size:11px;color:var(--sub);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.carousel-dots{display:flex;justify-content:center;gap:6px;padding:12px 0 4px;}
.dot{width:8px;height:8px;border-radius:50%;background:var(--border);transition:all .3s;cursor:pointer;}
.dot.active{width:22px;border-radius:4px;background:var(--teal2);}
.carousel-btn{position:absolute;top:50%;transform:translateY(-50%);width:32px;height:32px;border-radius:50%;background:var(--white);border:1px solid var(--border);color:var(--teal);font-size:12px;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:10;box-shadow:0 2px 8px rgba(0,0,0,.1);transition:all .3s;}
.carousel-btn:hover{background:var(--teal);color:#fff;}
.carousel-btn.prev{left:4px;}
.carousel-btn.next{right:4px;}
.website-btn{display:flex;align-items:center;gap:10px;margin:0 22px 18px;padding:14px 18px;background:linear-gradient(135deg,var(--teal),var(--dark));color:#fff;border-radius:14px;text-decoration:none;transition:all .3s;}
.website-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(8,145,178,.35);}
.website-btn span{font-size:13px;font-weight:600;}
.website-btn i{font-size:18px;}
.qr-block{margin:0 22px 18px;background:linear-gradient(135deg,var(--dark),var(--teal));border-radius:20px;padding:22px;display:flex;align-items:center;gap:18px;}
.qr-box{background:#fff;padding:6px;border-radius:12px;width:88px;height:88px;flex-shrink:0;}
.qr-box img{width:100%;height:100%;display:block;}
.qr-txt{color:#fff;}
.qr-txt h4{font-family:'DM Serif Display',serif;font-size:17px;margin-bottom:5px;}
.qr-txt p{font-size:12px;opacity:.85;line-height:1.6;}
.save-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 44px);margin:0 22px 18px;padding:15px;background:linear-gradient(135deg,var(--teal2),var(--teal));color:#fff;font-size:13px;font-weight:700;border-radius:30px;cursor:pointer;border:none;transition:all .3s;letter-spacing:1px;}
.save-btn:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(8,145,178,.4);}
.share-area{padding:0 22px 30px;}
.sh-row{display:flex;gap:8px;margin-bottom:10px;}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;border-radius:14px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid var(--border);color:var(--sub);background:var(--card);transition:all .3s;cursor:pointer;}
.sh:hover{background:var(--mint);border-color:var(--teal2);color:var(--teal);}
.copy-row{display:flex;border:1px solid var(--border);border-radius:14px;overflow:hidden;background:var(--card);}
.copy-inp{flex:1;background:transparent;border:none;color:var(--sub);font-size:11px;padding:12px 14px;font-family:'Plus Jakarta Sans',sans-serif;outline:none;}
.cp-btn{background:var(--teal);color:#fff;border:none;padding:12px 18px;cursor:pointer;font-weight:700;font-size:12px;transition:background .3s;}
.cp-btn:hover{background:var(--dark);}
.footer{text-align:center;padding:16px;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--sub);background:var(--mint);}
.footer span{color:var(--teal);font-weight:700;}
.fade-in-section{opacity:0;transform:translateY(22px);transition:all .65s ease;}
.fade-in-section.visible{opacity:1;transform:translateY(0);}
</style>
</head>
<body>
<div class="desktop-bg">
  <div class="float-shape"></div><div class="float-shape"></div><div class="float-shape"></div>
  <div class="float-shape"></div><div class="float-shape"></div>
  <i class="fas fa-plus float-cross"></i><i class="fas fa-tooth float-cross"></i><i class="fas fa-plus float-cross"></i>
</div>
<div class="card-wrap">
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
    <div class="banner-overlay"></div>
    <div class="banner-badge"><i class="fas fa-tooth"></i> &nbsp;<?= htmlspecialchars($vcard['job_title'] ?? 'Dental Care') ?></div>
    <div class="teal-bar"></div>
  </div>
  <div class="profile-area">
    <div class="profile-top">
      <div class="av">
        <div class="av-ring"></div>
        <img src="<?= $profileImg ?>" alt="<?= htmlspecialchars($fullName) ?>">
      </div>
      <?php if (!empty($vcard['phone'])): ?>
      <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="appt-btn"><i class="fas fa-calendar-check"></i> Book Appt.</a>
      <?php endif; ?>
    </div>
    <div class="doc-name"><?= htmlspecialchars($fullName) ?></div>
    <?php if (!empty($vcard['tagline'])): ?>
    <div class="tagline">"<?= htmlspecialchars($vcard['tagline']) ?>"</div>
    <?php endif; ?>
    <div class="doc-title"><?= htmlspecialchars($vcard['job_title'] ?? '') ?></div>
    <?php if (!empty($vcard['company']) || !empty($vcard['location'])): ?>
    <div class="doc-company"><?= htmlspecialchars(implode(', ', array_filter([$vcard['company'] ?? '', $vcard['location'] ?? '']))) ?></div>
    <?php endif; ?>
    <div class="stats-row">
      <div class="stat"><span class="stat-num">—</span><span class="stat-lbl">Years Exp.</span></div>
      <div class="stat"><span class="stat-num">—</span><span class="stat-lbl">Patients</span></div>
      <div class="stat"><span class="stat-num">5★</span><span class="stat-lbl">Rating</span></div>
    </div>
    <?php if (!empty($socialLinks)): ?>
    <div class="social-row">
      <?php foreach ($socialLinks as $s): $icon = $platformIcons[$s['platform']] ?? 'fa-globe'; ?>
      <a href="<?= htmlspecialchars($s['url']) ?>" class="soc" target="_blank" rel="noopener"><i class="fab <?= $icon ?>"></i></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php if (!empty($vcard['bio'])): ?>
  <div class="bio-wrap fade-in-section"><p><?= nl2br(htmlspecialchars($vcard['bio'])) ?></p></div>
  <?php endif; ?>
  <div class="section fade-in-section">
    <div class="sec-head"><div class="sec-icon"><i class="fas fa-address-book"></i></div><span class="sec-title">Contact</span></div>
    <div class="contact-grid">
      <?php if (!empty($vcard['email'])): ?>
      <a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="ci"><div class="ci-ico"><i class="fas fa-envelope"></i></div><div><div class="ci-lbl">Email</div><div class="ci-val"><?= htmlspecialchars($vcard['email']) ?></div></div></a>
      <?php endif; ?>
      <?php if (!empty($vcard['phone'])): ?>
      <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="ci"><div class="ci-ico"><i class="fas fa-phone"></i></div><div><div class="ci-lbl">Phone</div><div class="ci-val"><?= htmlspecialchars($vcard['phone']) ?></div></div></a>
      <?php endif; ?>
      <?php if (!empty($waPhone)): ?>
      <a href="https://wa.me/<?= $waPhone ?>" class="ci" target="_blank" rel="noopener"><div class="ci-ico"><i class="fab fa-whatsapp"></i></div><div><div class="ci-lbl">WhatsApp</div><div class="ci-val"><?= htmlspecialchars($vcard['phone']) ?></div></div></a>
      <?php endif; ?>
      <?php if (!empty($vcard['location'])): ?>
      <a href="<?= $locationUrl ?>" class="ci" target="_blank" rel="noopener"><div class="ci-ico"><i class="fas fa-map-marker-alt"></i></div><div><div class="ci-lbl">Address</div><div class="ci-val"><?= htmlspecialchars($vcard['location']) ?></div></div></a>
      <?php endif; ?>
    </div>
  </div>
  <?php if (!empty($vcard['website'])): ?>
  <a href="<?= htmlspecialchars($vcard['website']) ?>" target="_blank" class="website-btn fade-in-section">
    <i class="fas fa-globe"></i><span>Visit Our Website — <?= htmlspecialchars(preg_replace('#^https?://#','',$vcard['website'])) ?></span><i class="fas fa-arrow-right" style="margin-left:auto;"></i>
  </a>
  <?php endif; ?>
  <?php if (!empty($services)): ?>
  <div class="section fade-in-section">
    <div class="sec-head"><div class="sec-icon"><i class="fas fa-briefcase-medical"></i></div><span class="sec-title">Services</span></div>
    <div class="carousel-wrap" id="carousel18">
      <button class="carousel-btn prev" id="carousel18-prev"><i class="fas fa-chevron-left"></i></button>
      <div class="carousel-track" id="carousel18-track">
        <?php foreach ($services as $srv): ?>
        <div class="srv-card">
          <?php if (!empty($srv['image'])): ?><img src="<?= imgUrl($srv['image']) ?>" alt="<?= htmlspecialchars($srv['name']) ?>"><?php endif; ?>
          <div class="srv-body"><div class="srv-title"><?= htmlspecialchars($srv['name']) ?></div><?php if (!empty($srv['description'])): ?><div class="srv-desc"><?= htmlspecialchars($srv['description']) ?></div><?php endif; ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <button class="carousel-btn next" id="carousel18-next"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div class="carousel-dots" id="carousel18-dots"></div>
  </div>
  <?php endif; ?>
  <div class="qr-block fade-in-section">
    <div class="qr-box"><img src="<?= $qrUrl ?>" alt="QR Code"></div>
    <div class="qr-txt"><h4>Book Appointment</h4><p>Scan to book a dental appointment or save my contact instantly.</p></div>
  </div>
  <button class="save-btn fade-in-section" onclick="saveContact()"><i class="fas fa-user-plus"></i> Save to Contacts</button>
  <div class="share-area fade-in-section">
    <div class="sh-row">
      <button class="sh" onclick="shareCard('whatsapp')"><i class="fab fa-whatsapp"></i> WhatsApp</button>
      <button class="sh" onclick="shareCard('facebook')"><i class="fab fa-facebook-f"></i> Facebook</button>
      <button class="sh" onclick="shareCard('native')"><i class="fas fa-share-alt"></i> Share</button>
    </div>
    <div class="copy-row">
      <input class="copy-inp" id="cardUrlInput" value="<?= htmlspecialchars($cardUrl) ?>" readonly>
      <button class="cp-btn" onclick="navigator.clipboard.writeText(document.getElementById('cardUrlInput').value);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)">Copy</button>
    </div>
  </div>
  <?php if (empty($vcard['remove_branding'])): ?>
  <div class="footer">Powered by <span>Tapify</span> · Dental Edition</div>
  <?php endif; ?>
</div>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
<script>
const _obs=new IntersectionObserver(e=>{e.forEach(el=>{if(el.isIntersecting)el.target.classList.add('visible');});},{threshold:0.1});
document.querySelectorAll('.fade-in-section').forEach(el=>_obs.observe(el));
const carousels={};
function initCarousel(id){
  const track=document.getElementById(id+'-track');
  if(!track)return;
  const cards=track.querySelectorAll('.srv-card');
  const dotsEl=document.getElementById(id+'-dots');
  if(cards.length===0)return;
  let cur=0;const max=Math.max(0,cards.length-2);
  carousels[id]={track,cards,cur,max,dotsEl};
  if(dotsEl){for(let i=0;i<=max;i++){const d=document.createElement('div');d.className='dot'+(i===0?' active':'');d.onclick=()=>goTo(id,i);dotsEl.appendChild(d);}}
  renderCarousel(id);
  carousels[id].timer=setInterval(()=>moveCarousel(id,1),3000);
  const prev=document.getElementById(id+'-prev');const next=document.getElementById(id+'-next');
  if(prev)prev.onclick=()=>moveCarousel(id,-1);if(next)next.onclick=()=>moveCarousel(id,1);
}
function renderCarousel(id){
  const c=carousels[id];
  const cardW=(c.cards[0]?c.cards[0].offsetWidth:160)+12;
  c.track.style.transform=`translateX(-${c.cur*cardW}px)`;
  if(c.dotsEl)c.dotsEl.querySelectorAll('.dot').forEach((d,i)=>d.classList.toggle('active',i===c.cur));
}
function moveCarousel(id,dir){
  const c=carousels[id];
  c.cur+=dir;if(c.cur>c.max)c.cur=0;if(c.cur<0)c.cur=c.max;
  renderCarousel(id);
}
function goTo(id,idx){const c=carousels[id];c.cur=idx;renderCarousel(id);}
initCarousel('carousel18');
if(window.innerWidth>=768){
  const canvas=document.createElement('canvas');
  canvas.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;';
  document.body.insertBefore(canvas,document.body.firstChild);
  const ctx=canvas.getContext('2d');
  canvas.width=window.innerWidth;canvas.height=window.innerHeight;
  const pts=Array.from({length:40},()=>({x:Math.random()*canvas.width,y:Math.random()*canvas.height,vx:(Math.random()-.5)*.4,vy:(Math.random()-.5)*.4,r:Math.random()*2+1}));
  function drawCanvas(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    pts.forEach(p=>{p.x+=p.vx;p.y+=p.vy;if(p.x<0||p.x>canvas.width)p.vx*=-1;if(p.y<0||p.y>canvas.height)p.vy*=-1;ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);ctx.fillStyle='rgba(8,145,178,0.15)';ctx.fill();});
    pts.forEach((a,i)=>pts.slice(i+1).forEach(b=>{const d=Math.hypot(a.x-b.x,a.y-b.y);if(d<120){ctx.beginPath();ctx.moveTo(a.x,a.y);ctx.lineTo(b.x,b.y);ctx.strokeStyle=`rgba(8,145,178,${.08*(1-d/120)})`;ctx.stroke();}}));
    requestAnimationFrame(drawCanvas);
  }
  drawCanvas();
}
</script>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
