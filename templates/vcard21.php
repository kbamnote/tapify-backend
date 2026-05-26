<?php
/**
 * Tapify vCard Template: vcard21
 * Fashion Designer theme — Dark Editorial + Gold + Pink
 */
$cardUrl = 'https://tapify-backend-production.up.railway.app/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=0c0c0c&color=c9a96e';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500&q=80';
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='.urlencode($cardUrl);
$platformIcons = ['linkedin'=>'fa-linkedin-in','instagram'=>'fa-instagram','x'=>'fa-x-twitter','twitter'=>'fa-twitter','facebook'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','telegram'=>'fa-telegram'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($fullName) ?><?= !empty($vcard['company']) ? ' | '.htmlspecialchars($vcard['company']) : '' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:#0c0c0c;font-family:'Lato',sans-serif;min-height:100vh;display:flex;justify-content:center;align-items:flex-start;padding:16px 0 40px}
.card-wrap{width:100%;max-width:440px;background:#111111;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.8)}

/* BANNER */
.banner{position:relative;height:220px;overflow:hidden}
.banner-bg{width:100%;height:100%;object-fit:cover;display:block;filter:brightness(0.55)}
.banner-video{width:100%;height:100%;object-fit:cover;border:none}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(12,12,12,0.3) 0%,rgba(12,12,12,0.82) 100%)}
.banner-content{position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:20px 24px;text-align:center}
.brand-logo{font-family:'Bodoni Moda',serif;font-size:1.5rem;font-style:italic;color:#c9a96e;letter-spacing:0.06em;margin-bottom:6px;text-shadow:0 2px 12px rgba(0,0,0,0.6)}
.banner-big{font-family:'Lato',sans-serif;font-size:0.7rem;font-weight:300;letter-spacing:0.22em;text-transform:uppercase;color:rgba(255,255,255,0.75)}

/* PROFILE */
.profile-section{background:#0c0c0c;padding:28px 22px 22px;display:flex;flex-direction:column;align-items:center;text-align:center}
.avatar-wrap{position:relative;margin-bottom:16px}
@keyframes shimmer-ring{0%{box-shadow:0 0 0 3px #c9a96e,0 0 0 6px rgba(201,169,110,0.25),0 0 18px rgba(201,169,110,0.2)}50%{box-shadow:0 0 0 3px #e8b4b8,0 0 0 6px rgba(232,180,184,0.25),0 0 18px rgba(232,180,184,0.2)}100%{box-shadow:0 0 0 3px #c9a96e,0 0 0 6px rgba(201,169,110,0.25),0 0 18px rgba(201,169,110,0.2)}}
.avatar{width:96px;height:96px;border-radius:50%;object-fit:cover;border:2px solid #c9a96e;display:block;animation:shimmer-ring 4s ease-in-out infinite}
.shop-btn{display:inline-flex;align-items:center;gap:7px;background:transparent;border:1px solid rgba(201,169,110,0.5);color:#c9a96e;font-family:'Lato',sans-serif;font-size:0.65rem;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;padding:7px 18px;text-decoration:none;margin-bottom:14px;transition:background 0.2s,color 0.2s}
.shop-btn:hover{background:#c9a96e;color:#0c0c0c}
.designer-name{font-family:'Bodoni Moda',serif;font-size:1.5rem;font-weight:700;color:#ffffff;line-height:1.2;margin-bottom:6px}
.prof-tagline{font-size:0.72rem;font-weight:300;letter-spacing:0.12em;text-transform:uppercase;color:#c9a96e;margin-bottom:3px}
.prof-company{font-size:0.7rem;color:#777;margin-bottom:16px;letter-spacing:0.06em}
.social-row{display:flex;flex-wrap:wrap;gap:8px;justify-content:center}
.soc{width:32px;height:32px;background:rgba(201,169,110,0.1);border:1px solid rgba(201,169,110,0.3);color:#c9a96e;display:flex;align-items:center;justify-content:center;font-size:0.78rem;text-decoration:none;transition:background 0.2s,transform 0.2s}
.soc:hover{background:#c9a96e;color:#0c0c0c;transform:translateY(-2px)}

/* BIO */
.bio-section{padding:20px 24px;background:#0c0c0c;position:relative}
.bio-box{position:relative;padding:20px 22px;border-bottom:2px solid #c9a96e;text-align:center}
.bio-box::before{content:'\201C';font-family:'Bodoni Moda',serif;font-size:4.5rem;color:rgba(201,169,110,0.2);position:absolute;top:-10px;left:16px;line-height:1}
.bio-text{font-family:'Bodoni Moda',serif;font-size:0.82rem;font-style:italic;line-height:1.8;color:#bbb;position:relative;z-index:1}

/* SECTION WRAPPER */
.section{padding:16px 24px}

/* SEC-H */
.sec-h{position:relative;text-align:center;margin-bottom:18px;display:flex;align-items:center;gap:12px}
.sec-h::before,.sec-h::after{content:'';flex:1;height:1px;background:rgba(201,169,110,0.3)}
.sec-h-txt{font-family:'Bodoni Moda',serif;font-size:0.9rem;font-style:italic;color:#c9a96e;white-space:nowrap;padding:0 4px}

/* CONTACT GRID */
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.c-item{background:#1a1a1a;border-radius:0;padding:12px 10px;display:flex;align-items:center;gap:10px;text-decoration:none;border:1px solid #2a2a2a;transition:border-color 0.2s,transform 0.2s;overflow:hidden}
.c-item:hover{border-color:#c9a96e;transform:translateY(-2px)}
.c-icon{width:34px;height:34px;background:#c9a96e;display:flex;align-items:center;justify-content:center;color:#0c0c0c;font-size:0.8rem;flex-shrink:0}
.c-text{min-width:0}
.c-label{font-size:0.58rem;color:#777;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;display:block;margin-bottom:1px}
.c-val{font-size:0.73rem;color:#e0d5c5;font-weight:400;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;max-width:100px}

/* WEBSITE BTN */
.website-btn{display:flex;align-items:center;justify-content:center;gap:8px;background:#c9a96e;color:#0c0c0c;text-decoration:none;padding:12px 20px;font-size:0.78rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;margin:0 24px 16px;transition:opacity 0.2s}
.website-btn:hover{opacity:0.85}

/* SERVICES CAROUSEL */
.carousel-wrap{position:relative;overflow:hidden}
.carousel-track{display:flex;gap:12px;transition:transform 0.4s cubic-bezier(0.25,0.46,0.45,0.94);padding:4px 2px}
.srv-card{flex:0 0 calc(50% - 6px);background:#1a1a1a;overflow:hidden;border:1px solid #2a2a2a}
.srv-card img{width:100%;height:110px;object-fit:cover;display:block;filter:brightness(0.85)}
.srv-no-img{width:100%;height:110px;background:#1a1a1a;display:flex;align-items:center;justify-content:center;color:#c9a96e;font-size:1.6rem;border-bottom:1px solid #2a2a2a}
.srv-body{padding:10px 12px}
.srv-title{font-family:'Bodoni Moda',serif;font-size:0.8rem;font-weight:600;color:#e0d5c5;margin-bottom:4px}
.srv-desc{font-size:0.7rem;color:#888;line-height:1.5}
.carousel-btn{position:absolute;top:50%;transform:translateY(-50%);width:28px;height:28px;background:#c9a96e;color:#0c0c0c;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.7rem;z-index:2}
.carousel-btn.prev{left:4px}
.carousel-btn.next{right:4px}
.carousel-dots{display:flex;justify-content:center;gap:6px;margin-top:12px}
.dot{width:6px;height:6px;background:#333;cursor:pointer;transition:background 0.2s}
.dot.active{background:#c9a96e}

/* QR */
.qr-block{margin:0 24px 16px;background:#0c0c0c;padding:18px;display:flex;align-items:center;gap:16px;border-top:1px solid rgba(201,169,110,0.4);border-bottom:1px solid rgba(201,169,110,0.4)}
.qr-box{flex-shrink:0;padding:6px;background:#fff}
.qr-txt h4{font-family:'Bodoni Moda',serif;font-size:0.92rem;font-style:italic;color:#c9a96e;margin-bottom:4px}
.qr-txt p{font-size:0.71rem;color:#888;line-height:1.55}

/* SAVE BTN */
.save-btn{display:block;width:calc(100% - 48px);margin:0 24px 12px;padding:14px;background:#c9a96e;color:#0c0c0c;border:none;font-size:0.82rem;font-weight:700;font-family:'Lato',sans-serif;cursor:pointer;letter-spacing:0.16em;text-transform:uppercase;transition:opacity 0.2s;border-radius:0}
.save-btn:hover{opacity:0.88}

/* SHARE */
.share-area{padding:0 24px 16px}
.sh-row{display:flex;gap:10px;margin-bottom:10px}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;background:#1a1a1a;border:1px solid #2a2a2a;color:#c9a96e;font-size:0.78rem;font-weight:700;text-decoration:none;cursor:pointer;transition:border-color 0.2s}
.sh:hover{border-color:#c9a96e}
.copy-row{display:flex;gap:8px}
.copy-inp{flex:1;border:1px solid #2a2a2a;padding:8px 10px;font-size:0.72rem;color:#aaa;background:#1a1a1a;outline:none}
.cp-btn{background:#c9a96e;color:#0c0c0c;border:none;padding:8px 14px;font-size:0.72rem;font-weight:700;cursor:pointer}

/* FOOTER */
.footer{text-align:center;padding:14px 16px 4px;font-size:0.73rem;color:#666}
.footer span{color:#c9a96e;font-weight:700}

/* FADE IN */
.fade-in{opacity:0;transform:translateY(18px);transition:opacity 0.5s ease,transform 0.5s ease}
.fade-in.visible{opacity:1;transform:none}
</style>
</head>
<body>
<div class="card-wrap">

  <!-- BANNER -->
  <div class="banner">
    <?php if (($vcard['cover_type'] ?? '') === 'video' && !empty($vcard['cover_image'])): ?>
    <?php
    $videoUrl = $vcard['cover_image'];
    if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ \s]{11})/i', $videoUrl, $match);
        $ytId = $match[1] ?? '';
        echo '<iframe style="width:100%;height:100%;display:block;border:none;" src="https://www.youtube.com/embed/'.$ytId.'?autoplay=1&mute=1&loop=1&playlist='.$ytId.'&controls=1&showinfo=0&rel=0&playsinline=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
    } elseif (strpos($videoUrl, 'instagram.com') !== false) {
        $embedUrl = rtrim($videoUrl, '/') . '/embed';
        echo '<iframe style="width:100%;height:100%;display:block;border:none;" src="'.htmlspecialchars($embedUrl).'" frameborder="0" allowtransparency="true"></iframe>';
    } else {
        echo '<video src="'.htmlspecialchars(imgUrl($videoUrl)).'" autoplay loop muted playsinline style="width:100%;height:100%;object-fit:cover;display:block;"></video>';
    }
    ?>
    <?php else: ?>
    <img class="banner-bg" src="<?= htmlspecialchars($coverImg) ?>" alt="Cover">
    <?php endif; ?>
    <div class="banner-overlay"></div>
    <div class="banner-content">
      <div class="brand-logo"><?= htmlspecialchars($vcard['company'] ?? $fullName) ?></div>
      <div class="banner-big"><?= htmlspecialchars($vcard['occupation'] ?? 'Wear the Art') ?></div>
    </div>
  </div>

  <!-- PROFILE -->
  <div class="profile-section fade-in">
    <img class="avatar" src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($fullName) ?>">
    <?php if (!empty($vcard['website'])): ?>
    <a href="<?= htmlspecialchars($vcard['website']) ?>" target="_blank" class="shop-btn" style="margin-top:14px"><i class="fas fa-shopping-bag"></i> Shop Collection</a>
    <?php elseif (!empty($vcard['phone'])): ?>
    <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="shop-btn" style="margin-top:14px"><i class="fas fa-phone"></i> Book Appointment</a>
    <?php else: ?>
    <div style="height:14px"></div>
    <?php endif; ?>
    <div class="designer-name"><?= htmlspecialchars($fullName) ?></div>
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
    <div class="sec-h"><span class="sec-h-txt">Contact</span></div>
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

  <!-- SERVICES -->
  <?php if (!empty($services)): ?>
  <div class="section fade-in">
    <div class="sec-h"><span class="sec-h-txt">Collections</span></div>
    <div class="carousel-wrap">
      <button class="carousel-btn prev" onclick="moveC('<?= $vcardId ?>',-1)"><i class="fas fa-chevron-left"></i></button>
      <div class="carousel-track" id="track<?= $vcardId ?>">
        <?php foreach ($services as $s): ?>
        <div class="srv-card">
          <?php if (!empty($s['image'])): ?>
          <img src="<?= imgUrl($s['image']) ?>" alt="<?= htmlspecialchars($s['name']) ?>">
          <?php else: ?>
          <div class="srv-no-img"><i class="fas fa-tshirt"></i></div>
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
      <img src="<?= htmlspecialchars($qrUrl) ?>" alt="QR Code" width="68" height="68" style="display:block;">
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
  <div style="text-align:center;font-size:0.7rem;padding:0 16px 14px;opacity:0.35;color:#aaa;">An innovative Product From : <strong>Mr Print World Pvt Ltd.</strong></div>

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
