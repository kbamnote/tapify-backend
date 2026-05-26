<?php
/**
 * Tapify vCard Template: vcard27
 * Coaching Institute theme — Blue
 */
$cardUrl = 'https://tapify-backend-production.up.railway.app/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=2563eb&color=ffffff';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='.urlencode($cardUrl);
$platformIcons = ['linkedin'=>'fa-linkedin-in','instagram'=>'fa-instagram','x'=>'fa-x-twitter','twitter'=>'fa-twitter','facebook'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','telegram'=>'fa-telegram'];
// theme vars
$accent   = '#2563eb';
$accentD  = '#1d4ed8';
$accentL  = '#bfdbfe';
$accentXL = '#eff6ff';
$bgBody   = '#1e3a5f';
$textH    = '#1e3a5f';
$textS    = '#1e40af';
$bannerTag = 'Coaching Institute';
$ctaLabel  = 'Enrol Now';
$ctaIcon   = 'fa-graduation-cap';
$srvIcon   = 'fa-book';
$bannerIco = 'fa-graduation-cap';
$secIco    = 'fa-address-book';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($fullName) ?><?= !empty($vcard['company']) ? ' | '.htmlspecialchars($vcard['company']) : '' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --accent:<?= $accent ?>;
  --accentD:<?= $accentD ?>;
  --accentL:<?= $accentL ?>;
  --accentXL:<?= $accentXL ?>;
  --textH:<?= $textH ?>;
  --textS:<?= $textS ?>;
}
body{background:<?= $bgBody ?>;font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;justify-content:center;align-items:flex-start;padding:16px 0 40px}
.card-wrap{width:100%;max-width:440px;background:var(--accentXL);overflow:hidden;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.3)}

/* BANNER */
.banner{position:relative;height:210px;overflow:hidden;border-radius:20px 20px 0 0}
.banner-bg{width:100%;height:100%;object-fit:cover;display:block}
.banner-video{width:100%;height:100%;object-fit:cover;border:none}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(30,58,95,0.35) 0%,rgba(30,58,95,0.82) 100%)}
.banner-content{position:absolute;inset:0;display:flex;flex-direction:column;justify-content:flex-end;padding:20px 22px}
.banner-tag{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.5);color:#fff;font-size:0.63rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;padding:4px 12px;border-radius:20px;margin-bottom:8px;width:fit-content}
.banner-big{font-size:1.3rem;font-weight:700;color:#fff;line-height:1.25;text-shadow:0 2px 10px rgba(0,0,0,0.35)}
.accent-bar{position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--accentD),var(--accent),#60a5fa,var(--accent),var(--accentD));background-size:200% 100%;animation:bar-slide 3s linear infinite}
@keyframes bar-slide{0%{background-position:0% 0}100%{background-position:200% 0}}

/* PROFILE */
.profile-section{background:#fff;padding:28px 22px 22px;display:flex;flex-direction:column;align-items:center;text-align:center}
.avatar-wrap{position:relative;margin-bottom:14px}
.avatar{width:92px;height:92px;border-radius:50%;object-fit:cover;border:4px solid #fff;display:block;box-shadow:0 0 0 3px var(--accent),0 6px 20px rgba(37,99,235,0.2)}
.cta-btn{display:inline-flex;align-items:center;gap:7px;background:var(--accent);color:#fff;font-size:0.72rem;font-weight:700;letter-spacing:0.06em;padding:8px 20px;border-radius:24px;text-decoration:none;margin-bottom:14px;box-shadow:0 4px 14px rgba(37,99,235,0.3);transition:background 0.2s,transform 0.2s}
.cta-btn:hover{background:var(--accentD);transform:translateY(-1px)}
.store-name{font-size:1.3rem;font-weight:700;color:var(--textH);margin-bottom:5px}
.prof-tagline{font-size:0.76rem;color:var(--accent);font-weight:600;margin-bottom:3px}
.prof-company{font-size:0.72rem;color:var(--textS);margin-bottom:14px;font-weight:500}
.social-row{display:flex;flex-wrap:wrap;gap:8px;justify-content:center}
.soc{width:34px;height:34px;border-radius:50%;background:rgba(37,99,235,0.1);border:2px solid rgba(37,99,235,0.35);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:0.8rem;text-decoration:none;transition:background 0.2s,transform 0.2s}
.soc:hover{background:var(--accent);color:#fff;transform:translateY(-2px)}

/* BIO */
.bio-section{padding:18px 22px;background:var(--accentXL)}
.bio-box{border-left:4px solid var(--accent);padding:14px 16px;background:linear-gradient(135deg,#dbeafe,var(--accentXL));border-radius:0 12px 12px 0;box-shadow:0 2px 8px rgba(37,99,235,0.1)}
.bio-text{font-size:0.83rem;line-height:1.75;color:var(--textH)}

/* SECTION WRAPPER */
.section{padding:16px 22px}

/* SEC-H */
.sec-h{display:flex;align-items:center;gap:10px;margin-bottom:16px}
.sec-ico{width:32px;height:32px;border-radius:8px;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.85rem;flex-shrink:0}
.sec-title{font-size:0.92rem;font-weight:700;color:var(--textH)}

/* CONTACT GRID */
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.c-item{background:#fff;border-radius:12px;padding:12px 10px;display:flex;align-items:center;gap:10px;text-decoration:none;border:1.5px solid var(--accentL);box-shadow:0 2px 6px rgba(37,99,235,0.08);transition:box-shadow 0.2s,transform 0.2s,border-color 0.2s;overflow:hidden}
.c-item:hover{box-shadow:0 4px 14px rgba(37,99,235,0.22);transform:translateY(-2px);border-color:var(--accent)}
.c-icon{width:34px;height:34px;border-radius:10px;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.8rem;flex-shrink:0}
.c-text{min-width:0}
.c-label{font-size:0.6rem;color:var(--textS);font-weight:700;letter-spacing:0.07em;text-transform:uppercase;display:block;margin-bottom:1px}
.c-val{font-size:0.73rem;color:var(--textH);font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;max-width:95px}

/* WEBSITE BTN */
.website-btn{display:flex;align-items:center;justify-content:center;gap:8px;background:transparent;border:2px solid var(--accent);color:var(--textH);text-decoration:none;padding:11px 20px;border-radius:24px;font-size:0.83rem;font-weight:700;margin:0 22px 16px;transition:background 0.2s,color 0.2s}
.website-btn:hover{background:var(--accent);color:#fff}

/* SERVICES CAROUSEL */
.carousel-wrap{position:relative;overflow:hidden}
.carousel-track{display:flex;gap:12px;transition:transform 0.4s cubic-bezier(0.25,0.46,0.45,0.94);padding:4px 2px}
.srv-card{flex:0 0 calc(50% - 6px);background:#fff;border-radius:12px;overflow:hidden;border:1.5px solid var(--accentL);box-shadow:0 2px 8px rgba(37,99,235,0.1)}
.srv-card img{width:100%;height:105px;object-fit:cover;display:block}
.srv-no-img{width:100%;height:105px;background:linear-gradient(135deg,#dbeafe,var(--accentL));display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:1.6rem}
.srv-body{padding:10px 12px}
.srv-title{font-size:0.82rem;font-weight:700;color:var(--textH);margin-bottom:4px}
.srv-desc{font-size:0.7rem;color:var(--textS);line-height:1.5}
.carousel-btn{position:absolute;top:50%;transform:translateY(-50%);width:30px;height:30px;border-radius:50%;background:var(--accent);color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.72rem;z-index:2;box-shadow:0 2px 8px rgba(37,99,235,0.3)}
.carousel-btn.prev{left:4px}
.carousel-btn.next{right:4px}
.carousel-dots{display:flex;justify-content:center;gap:6px;margin-top:12px}
.dot{width:7px;height:7px;border-radius:50%;background:var(--accentL);cursor:pointer;transition:background 0.2s}
.dot.active{background:var(--accent)}

/* QR */
.qr-block{margin:0 22px 16px;background:linear-gradient(135deg,var(--textH),var(--accent));border-radius:16px;padding:18px;display:flex;align-items:center;gap:16px}
.qr-box{flex-shrink:0;padding:6px;background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.12)}
.qr-txt h4{font-size:0.92rem;font-weight:700;color:#fff;margin-bottom:4px}
.qr-txt p{font-size:0.71rem;color:rgba(255,255,255,0.8);line-height:1.55}

/* SAVE BTN */
.save-btn{display:block;width:calc(100% - 44px);margin:0 22px 12px;padding:13px;background:linear-gradient(135deg,var(--accent) 0%,var(--accentD) 100%);color:#fff;border:none;border-radius:28px;font-size:0.88rem;font-weight:700;font-family:'DM Sans',sans-serif;cursor:pointer;letter-spacing:0.04em;transition:opacity 0.2s,transform 0.2s;box-shadow:0 4px 16px rgba(37,99,235,0.38)}
.save-btn:hover{opacity:0.92;transform:translateY(-1px)}

/* SHARE */
.share-area{padding:0 22px 16px}
.sh-row{display:flex;gap:10px;margin-bottom:10px}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;background:#fff;border:1.5px solid var(--accentL);border-radius:20px;color:var(--textS);font-size:0.78rem;font-weight:700;text-decoration:none;cursor:pointer;transition:background 0.2s}
.sh:hover{background:#dbeafe}
.copy-row{display:flex;gap:8px}
.copy-inp{flex:1;border:1.5px solid var(--accentL);border-radius:20px;padding:8px 12px;font-size:0.72rem;color:#666;background:#fff;outline:none}
.cp-btn{background:var(--accent);color:#fff;border:none;border-radius:20px;padding:8px 14px;font-size:0.72rem;font-weight:700;cursor:pointer}

/* FOOTER */
.footer{text-align:center;padding:14px 16px 4px;font-size:0.73rem;color:var(--textS)}
.footer span{color:var(--accent);font-weight:700}

/* FADE IN */
.fade-in{opacity:0;transform:translateY(18px);transition:opacity 0.5s ease,transform 0.5s ease}
.fade-in.visible{opacity:1;transform:none}
</style>
</head>
<body>
<div class="card-wrap">

  <!-- BANNER -->
  <div class="banner">
    <?php if (($vcard['cover_type'] ?? '') === 'video'): ?>
    <iframe class="banner-video" src="<?= htmlspecialchars($coverImg) ?>" frameborder="0" allow="autoplay; muted" allowfullscreen></iframe>
    <?php else: ?>
    <img class="banner-bg" src="<?= htmlspecialchars($coverImg) ?>" alt="Cover">
    <?php endif; ?>
    <div class="banner-overlay"></div>
    <div class="banner-content">
      <div class="banner-tag"><i class="fas fa-<?= $bannerIco ?>"></i> <?= htmlspecialchars($vcard['occupation'] ?? $bannerTag) ?></div>
      <div class="banner-big"><?= htmlspecialchars($vcard['company'] ?? 'Excellence in Education') ?></div>
    </div>
    <div class="accent-bar"></div>
  </div>

  <!-- PROFILE -->
  <div class="profile-section fade-in">
    <div class="avatar-wrap">
      <img class="avatar" src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($fullName) ?>">
    </div>
    <?php if (!empty($vcard['phone'])): ?>
    <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="cta-btn"><i class="fas fa-<?= $ctaIcon ?>"></i> <?= $ctaLabel ?></a>
    <?php endif; ?>
    <div class="store-name"><?= htmlspecialchars($fullName) ?></div>
    <?php if (!empty($vcard['occupation'])): ?>
    <div class="prof-tagline"><?= htmlspecialchars($vcard['occupation']) ?></div>
    <?php endif; ?>
    <?php if (!empty($vcard['company'])): ?>
    <div class="prof-company"><?= htmlspecialchars($vcard['company']) ?></div>
    <?php endif; ?>
    <?php if (!empty($socialLinks)): ?>
    <div class="social-row">
      <?php foreach ($socialLinks as $sl):
        $platform = strtolower($sl['platform'] ?? '');
        $icon = $platformIcons[$platform] ?? 'fa-link';
      ?>
      <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" class="soc" title="<?= htmlspecialchars($sl['platform']) ?>">
        <i class="fab <?= $icon ?>"></i>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- BIO -->
  <?php if (!empty($vcard['description'])): ?>
  <div class="bio-section fade-in">
    <div class="bio-box">
      <div class="bio-text"><?= nl2br(htmlspecialchars($vcard['description'])) ?></div>
    </div>
  </div>
  <?php endif; ?>

  <!-- CONTACT -->
  <div class="section fade-in">
    <div class="sec-h">
      <div class="sec-ico"><i class="fas fa-<?= $secIco ?>"></i></div>
      <span class="sec-title">Contact</span>
    </div>
    <div class="contact-grid">
      <?php if (!empty($vcard['phone'])): ?>
      <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="c-item">
        <div class="c-icon"><i class="fas fa-phone"></i></div>
        <div class="c-text"><span class="c-label">Phone</span><span class="c-val"><?= htmlspecialchars($vcard['phone']) ?></span></div>
      </a>
      <?php endif; ?>
      <?php if (!empty($vcard['email'])): ?>
      <a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="c-item">
        <div class="c-icon"><i class="fas fa-envelope"></i></div>
        <div class="c-text"><span class="c-label">Email</span><span class="c-val"><?= htmlspecialchars($vcard['email']) ?></span></div>
      </a>
      <?php endif; ?>
      <?php if (!empty($vcard['location'])): ?>
      <a href="<?= htmlspecialchars($locationUrl) ?>" target="_blank" class="c-item">
        <div class="c-icon"><i class="fas fa-map-marker-alt"></i></div>
        <div class="c-text"><span class="c-label">Location</span><span class="c-val"><?= htmlspecialchars($vcard['location']) ?></span></div>
      </a>
      <?php endif; ?>
      <?php if (!empty($waPhone)): ?>
      <a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="c-item">
        <div class="c-icon"><i class="fab fa-whatsapp"></i></div>
        <div class="c-text"><span class="c-label">WhatsApp</span><span class="c-val">Chat Now</span></div>
      </a>
      <?php endif; ?>
    </div>
  </div>

  <?php if (!empty($vcard['website'])): ?>
  <a href="<?= htmlspecialchars($vcard['website']) ?>" target="_blank" class="website-btn fade-in">
    <i class="fas fa-globe"></i> Visit Website
  </a>
  <?php endif; ?>

  <!-- SERVICES -->
  <?php if (!empty($services)): ?>
  <div class="section fade-in">
    <div class="sec-h">
      <div class="sec-ico"><i class="fas fa-<?= $srvIcon ?>"></i></div>
      <span class="sec-title">Courses &amp; Batches</span>
    </div>
    <div class="carousel-wrap">
      <button class="carousel-btn prev" onclick="moveC('<?= $vcardId ?>',-1)"><i class="fas fa-chevron-left"></i></button>
      <div class="carousel-track" id="track<?= $vcardId ?>">
        <?php foreach ($services as $s): ?>
        <div class="srv-card">
          <?php if (!empty($s['image'])): ?>
          <img src="<?= imgUrl($s['image']) ?>" alt="<?= htmlspecialchars($s['name']) ?>">
          <?php else: ?>
          <div class="srv-no-img"><i class="fas fa-chalkboard"></i></div>
          <?php endif; ?>
          <div class="srv-body">
            <div class="srv-title"><?= htmlspecialchars($s['name']) ?></div>
            <?php if (!empty($s['description'])): ?><div class="srv-desc"><?= htmlspecialchars($s['description']) ?></div><?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <button class="carousel-btn next" onclick="moveC('<?= $vcardId ?>',1)"><i class="fas fa-chevron-right"></i></button>
    </div>
    <div class="carousel-dots" id="dots<?= $vcardId ?>"></div>
  </div>
  <?php endif; ?>

  <?php include __DIR__ . '/_features.php'; ?>

  <div class="qr-block fade-in">
    <div class="qr-box">
      <img src="<?= htmlspecialchars($qrUrl) ?>" alt="QR Code" width="68" height="68" style="display:block;border-radius:6px;">
    </div>
    <div class="qr-txt"><h4>Scan &amp; Connect</h4><p>Scan to save contact or share this card.</p></div>
  </div>

  <button class="save-btn fade-in" onclick="saveContact()"><i class="fas fa-address-card"></i> Save Contact</button>

  <div class="share-area fade-in">
    <div class="sh-row">
      <?php if (!empty($waPhone)): ?>
      <a href="https://wa.me/?text=<?= urlencode($cardUrl) ?>" target="_blank" class="sh"><i class="fab fa-whatsapp"></i> Share</a>
      <?php endif; ?>
      <button onclick="shareCard()" class="sh"><i class="fas fa-share-alt"></i> Share</button>
    </div>
    <div class="copy-row">
      <input class="copy-inp" value="<?= htmlspecialchars($cardUrl) ?>" readonly>
      <button class="cp-btn" onclick="navigator.clipboard.writeText(this.previousElementSibling.value);this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)">Copy</button>
    </div>
  </div>

  <?php if (empty($vcard['remove_branding'])): ?>
  <div class="footer">Powered by <span>Tapify</span></div>
  <?php endif; ?>
  <div style="text-align:center;font-size:0.7rem;padding:0 16px 14px;opacity:0.5;">A unit of <strong>Mr Print World</strong></div>

</div><!-- .card-wrap -->

<script>
const _obs=new IntersectionObserver(e=>{e.forEach(el=>{if(el.isIntersecting)el.target.classList.add('visible');});},{threshold:0.1});
document.querySelectorAll('.fade-in').forEach(el=>_obs.observe(el));
const carousels={};
function initCarousel(id){const track=document.getElementById('track'+id);if(!track)return;const cards=track.querySelectorAll('.srv-card');const dotsEl=document.getElementById('dots'+id);if(!cards.length)return;let cur=0,max=Math.max(0,cards.length-2);carousels[id]={track,cards,cur,max,dotsEl};if(dotsEl){for(let i=0;i<=max;i++){const d=document.createElement('div');d.className='dot'+(i===0?' active':'');d.onclick=()=>goTo(id,i);dotsEl.appendChild(d);}}renderC(id);setInterval(()=>moveC(id,1),3200);}
function renderC(id){const c=carousels[id];if(!c)return;const w=c.cards[0].offsetWidth+12;c.track.style.transform=`translateX(-${c.cur*w}px)`;if(c.dotsEl)c.dotsEl.querySelectorAll('.dot').forEach((d,i)=>d.classList.toggle('active',i===c.cur));}
function moveC(id,dir){const c=carousels[id];if(!c)return;c.cur+=dir;if(c.cur>c.max)c.cur=0;if(c.cur<0)c.cur=c.max;renderC(id);}
function goTo(id,i){if(carousels[id]){carousels[id].cur=i;renderC(id);}}
initCarousel('<?= $vcardId ?>');
</script>
<?php if (!empty($vcard['custom_css'])): ?><style><?= $vcard['custom_css'] ?></style><?php endif; ?>
<?php if (!empty($vcard['custom_js'])): ?><script><?= $vcard['custom_js'] ?></script><?php endif; ?>
<?php include __DIR__ . '/_shared-scripts.php'; ?>
</body>
</html>
