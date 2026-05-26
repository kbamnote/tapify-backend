<?php
/**
 * Tapify vCard Template: vcard24
 * Event Planner theme — Deep Purple + Pink + Gold
 */
$cardUrl = 'https://tapify-backend-production.up.railway.app/'.($vcard['url_alias'] ?? $vcardId);
$waPhone = preg_replace('/\D/', '', $vcard['phone'] ?? '');
$locationUrl = !empty($vcard['location_url']) ? $vcard['location_url'] : 'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg = !empty($vcard['profile_image']) ? imgUrl($vcard['profile_image']) : 'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=9333ea&color=ffffff';
$coverImg = !empty($vcard['cover_image']) ? imgUrl($vcard['cover_image']) : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=500&q=80';
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
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Josefin+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:#2d0057;font-family:'Josefin Sans',sans-serif;min-height:100vh;display:flex;justify-content:center;align-items:flex-start;padding:16px 0 40px}
.card-wrap{width:100%;max-width:440px;background:#0d0010;overflow:hidden;border-radius:16px;box-shadow:0 20px 60px rgba(147,51,234,0.3),0 8px 30px rgba(0,0,0,0.7)}

/* BANNER */
.banner{position:relative;height:210px;overflow:hidden;border-radius:16px 16px 0 0}
.banner-bg{width:100%;height:100%;object-fit:cover;display:block;filter:brightness(0.45)}
.banner-video{width:100%;height:100%;object-fit:cover;border:none}
.banner-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(13,0,16,0.3) 0%,rgba(13,0,16,0.85) 100%)}
.banner-content{position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:20px 24px;text-align:center}
.event-brand{font-family:'Great Vibes',cursive;font-size:2.4rem;color:#f59e0b;text-shadow:0 2px 12px rgba(245,158,11,0.4);line-height:1.1;margin-bottom:8px}
.event-tag{font-size:0.64rem;font-weight:600;letter-spacing:0.18em;text-transform:uppercase;color:rgba(255,255,255,0.75);margin-bottom:0}
.pp-bar{position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#9333ea,#ec4899,#f59e0b,#ec4899,#9333ea);background-size:200% 100%;animation:pp-slide 3s linear infinite}
@keyframes pp-slide{0%{background-position:0% 0}100%{background-position:200% 0}}

/* PROFILE */
.profile-section{background:#140020;padding:28px 22px 22px;display:flex;flex-direction:column;align-items:center;text-align:center}
.avatar-wrap{position:relative;margin-bottom:14px}
@keyframes conic-spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
.avatar{width:94px;height:94px;border-radius:50%;object-fit:cover;border:3px solid #9333ea;display:block;box-shadow:0 0 0 2px rgba(147,51,234,0.3),0 6px 20px rgba(147,51,234,0.25)}
.avatar-conic{position:absolute;inset:-5px;border-radius:50%;background:conic-gradient(#9333ea,#ec4899,#f59e0b,#ec4899,#9333ea);animation:conic-spin 6s linear infinite;z-index:-1;filter:blur(1px)}
.plan-btn{display:inline-flex;align-items:center;gap:7px;background:linear-gradient(135deg,#9333ea,#ec4899);color:#fff;font-size:0.68rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;padding:8px 20px;border-radius:24px;text-decoration:none;margin-bottom:14px;box-shadow:0 4px 14px rgba(147,51,234,0.4);transition:opacity 0.2s,transform 0.2s}
.plan-btn:hover{opacity:0.9;transform:translateY(-1px)}
.planner-name{font-family:'Great Vibes',cursive;font-size:2.2rem;color:#fff;line-height:1.1;margin-bottom:6px;text-shadow:0 2px 10px rgba(147,51,234,0.4)}
.prof-tagline{font-size:0.72rem;color:#ec4899;font-weight:600;letter-spacing:0.08em;margin-bottom:3px}
.prof-title{font-size:0.67rem;color:#a78bfa;margin-bottom:3px}
.prof-company{font-size:0.7rem;color:#c4b5fd;margin-bottom:14px}
.social-row{display:flex;flex-wrap:wrap;gap:8px;justify-content:center}
.soc{width:32px;height:32px;border-radius:50%;background:rgba(147,51,234,0.18);border:1px solid rgba(147,51,234,0.4);color:#c084fc;display:flex;align-items:center;justify-content:center;font-size:0.78rem;text-decoration:none;transition:background 0.2s,transform 0.2s}
.soc:hover{background:#9333ea;color:#fff;transform:translateY(-2px)}

/* BIO */
.bio-section{padding:18px 22px;background:#0d0010}
.bio-box{position:relative;padding:18px;background:#140020;border-radius:12px;text-align:center;overflow:hidden}
.bio-box::before{content:'';position:absolute;top:0;left:16px;right:16px;height:2px;background:linear-gradient(90deg,transparent,#9333ea,#ec4899,#f59e0b,#ec4899,#9333ea,transparent)}
.bio-text{font-size:0.82rem;line-height:1.8;color:#c4b5fd}

/* SECTION WRAPPER */
.section{padding:16px 22px}

/* SEC-H */
.sec-h{font-size:0.75rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:#c084fc;margin-bottom:16px;position:relative;padding-bottom:8px}
.sec-h::after{content:'';position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,#9333ea,transparent)}

/* CONTACT GRID */
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.c-item{background:#140020;border-radius:12px;padding:12px 10px;display:flex;align-items:center;gap:10px;text-decoration:none;border:1px solid rgba(147,51,234,0.25);transition:border-color 0.2s,transform 0.2s;overflow:hidden}
.c-item:hover{border-color:#9333ea;transform:translateY(-2px)}
.c-icon{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#9333ea,#ec4899);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.8rem;flex-shrink:0}
.c-text{min-width:0}
.c-label{font-size:0.58rem;color:#a78bfa;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;display:block;margin-bottom:1px}
.c-val{font-size:0.73rem;color:#e9d5ff;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;max-width:100px}

/* WEBSITE BTN */
.website-btn{display:flex;align-items:center;justify-content:center;gap:8px;background:transparent;border:1.5px solid #9333ea;color:#c084fc;text-decoration:none;padding:11px 20px;border-radius:24px;font-size:0.78rem;font-weight:700;letter-spacing:0.08em;margin:0 22px 16px;transition:background 0.2s,color 0.2s}
.website-btn:hover{background:#9333ea;color:#fff}

/* SERVICES CAROUSEL */
.carousel-wrap{position:relative;overflow:hidden}
.carousel-track{display:flex;gap:12px;transition:transform 0.4s cubic-bezier(0.25,0.46,0.45,0.94);padding:4px 2px}
.srv-card{flex:0 0 calc(50% - 6px);background:#140020;border-radius:12px;overflow:hidden;border:1px solid rgba(147,51,234,0.25)}
.srv-card:hover{border-color:#9333ea}
.srv-card img{width:100%;height:105px;object-fit:cover;display:block}
.srv-no-img{width:100%;height:105px;background:linear-gradient(135deg,#1e0035,#2d0057);display:flex;align-items:center;justify-content:center;color:#c084fc;font-size:1.6rem}
.srv-body{padding:10px 12px}
.srv-title{font-size:0.8rem;font-weight:700;color:#e9d5ff;margin-bottom:4px;letter-spacing:0.04em}
.srv-desc{font-size:0.7rem;color:#a78bfa;line-height:1.5}
.carousel-btn{position:absolute;top:50%;transform:translateY(-50%);width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#9333ea,#ec4899);color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.72rem;z-index:2;box-shadow:0 2px 8px rgba(147,51,234,0.4)}
.carousel-btn.prev{left:4px}
.carousel-btn.next{right:4px}
.carousel-dots{display:flex;justify-content:center;gap:6px;margin-top:12px}
.dot{width:7px;height:7px;border-radius:50%;background:#2d0057;cursor:pointer;transition:background 0.2s}
.dot.active{background:#9333ea}

/* QR */
.qr-block{margin:0 22px 16px;background:linear-gradient(135deg,#9333ea,#ec4899);border-radius:16px;padding:18px;display:flex;align-items:center;gap:16px}
.qr-box{flex-shrink:0;padding:6px;background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.2)}
.qr-txt h4{font-family:'Great Vibes',cursive;font-size:1.3rem;color:#fff;margin-bottom:4px}
.qr-txt p{font-size:0.71rem;color:rgba(255,255,255,0.85);line-height:1.55}

/* SAVE BTN */
.save-btn{display:block;width:calc(100% - 44px);margin:0 22px 12px;padding:13px;background:linear-gradient(135deg,#9333ea 0%,#ec4899 100%);color:#fff;border:none;border-radius:28px;font-size:0.85rem;font-weight:700;font-family:'Josefin Sans',sans-serif;cursor:pointer;letter-spacing:0.08em;text-transform:uppercase;transition:opacity 0.2s,transform 0.2s;box-shadow:0 4px 16px rgba(147,51,234,0.45)}
.save-btn:hover{opacity:0.92;transform:translateY(-1px)}

/* SHARE */
.share-area{padding:0 22px 16px}
.sh-row{display:flex;gap:10px;margin-bottom:10px}
.sh{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;background:#140020;border:1px solid rgba(147,51,234,0.3);border-radius:20px;color:#c084fc;font-size:0.78rem;font-weight:700;text-decoration:none;cursor:pointer;transition:border-color 0.2s}
.sh:hover{border-color:#9333ea}
.copy-row{display:flex;gap:8px}
.copy-inp{flex:1;border:1px solid rgba(147,51,234,0.3);border-radius:20px;padding:8px 12px;font-size:0.72rem;color:#c4b5fd;background:#140020;outline:none}
.cp-btn{background:#9333ea;color:#fff;border:none;border-radius:20px;padding:8px 14px;font-size:0.72rem;font-weight:700;cursor:pointer}

/* FOOTER */
.footer{text-align:center;padding:14px 16px 4px;font-size:0.73rem;color:#7c3aed}
.footer span{color:#c084fc;font-weight:700}

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
      <div class="event-brand"><?= htmlspecialchars($fullName) ?></div>
      <div class="event-tag"><?= htmlspecialchars($vcard['occupation'] ?? 'Event Planner') ?></div>
    </div>
    <div class="pp-bar"></div>
  </div>

  <!-- PROFILE -->
  <div class="profile-section fade-in">
    <div class="avatar-wrap">
      <div class="avatar-conic"></div>
      <img class="avatar" src="<?= htmlspecialchars($profileImg) ?>" alt="<?= htmlspecialchars($fullName) ?>">
    </div>
    <?php if (!empty($vcard['phone'])): ?>
    <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="plan-btn"><i class="fas fa-calendar-star"></i> Book Now</a>
    <?php endif; ?>
    <div class="planner-name"><?= htmlspecialchars($fullName) ?></div>
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
    <div class="sec-h">Contact</div>
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
    <div class="sec-h">Our Services</div>
    <div class="carousel-wrap">
      <button class="carousel-btn prev" onclick="moveC('<?= $vcardId ?>',-1)"><i class="fas fa-chevron-left"></i></button>
      <div class="carousel-track" id="track<?= $vcardId ?>">
        <?php foreach ($services as $s): ?>
        <div class="srv-card">
          <?php if (!empty($s['image'])): ?>
          <img src="<?= imgUrl($s['image']) ?>" alt="<?= htmlspecialchars($s['name']) ?>">
          <?php else: ?>
          <div class="srv-no-img"><i class="fas fa-star"></i></div>
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
  <div style="text-align:center;font-size:0.7rem;padding:0 16px 14px;opacity:0.35;color:#c4b5fd;">A unit of <strong>Mr Print World</strong></div>

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
