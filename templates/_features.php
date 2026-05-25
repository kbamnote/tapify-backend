<?php
/**
 * _features.php — Shared feature sections for standalone templates
 * Renders: Products, Galleries, Testimonials, Iframes, Instagram Feed, Inquiry Form, Appointment Booking
 * Included by vcard01–18 (except vcard17 which has its own layout for most features)
 */
?>
<style>
.tf-sec{padding:0 22px 20px;}
.tf-sec-title{font-size:10px;letter-spacing:2.5px;text-transform:uppercase;font-weight:700;margin-bottom:14px;display:flex;align-items:center;gap:10px;opacity:.75;}
.tf-sec-title::after{content:'';flex:1;height:1px;background:currentColor;opacity:.2;}
/* ── Products ── */
.tf-products{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.tf-prod{border-radius:14px;overflow:hidden;background:rgba(128,128,128,.07);border:1px solid rgba(128,128,128,.12);text-decoration:none;color:inherit;display:block;transition:transform .3s,box-shadow .3s;}
.tf-prod:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,.1);}
.tf-prod-img{width:100%;height:110px;object-fit:cover;display:block;}
.tf-prod-no-img{width:100%;height:80px;display:flex;align-items:center;justify-content:center;background:rgba(128,128,128,.08);font-size:1.8rem;color:rgba(128,128,128,.3);}
.tf-prod-info{padding:10px 12px;}
.tf-prod-name{font-size:12px;font-weight:600;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.tf-prod-price{font-size:11px;font-weight:700;margin-bottom:2px;opacity:.8;}
.tf-prod-desc{font-size:10px;opacity:.55;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.4;}
/* ── Gallery ── */
.tf-gal-name{font-size:11px;font-weight:600;margin-bottom:8px;opacity:.6;padding:0 22px;}
.tf-gal-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;padding:0 22px;margin-bottom:14px;}
.tf-gal-item{aspect-ratio:1;border-radius:10px;overflow:hidden;}
.tf-gal-item img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .3s;}
.tf-gal-item:hover img{transform:scale(1.06);}
/* ── Testimonials ── */
.tf-testimonials{display:flex;flex-direction:column;gap:12px;}
.tf-testi{padding:14px 16px;background:rgba(128,128,128,.06);border-radius:14px;border:1px solid rgba(128,128,128,.1);}
.tf-testi-stars{font-size:13px;color:#f59e0b;margin-bottom:6px;}
.tf-testi-msg{font-size:13px;font-style:italic;margin-bottom:8px;line-height:1.65;opacity:.85;}
.tf-testi-author{font-size:11px;font-weight:700;}
.tf-testi-meta{font-size:10px;opacity:.55;margin-top:2px;}
/* ── Iframes ── */
.tf-iframe-wrap{position:relative;width:100%;border-radius:12px;overflow:hidden;margin-bottom:12px;background:rgba(128,128,128,.05);border:1px solid rgba(128,128,128,.1);}
.tf-iframe-wrap iframe{width:100%;border:none;display:block;}
/* ── Instagram ── */
.tf-insta-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;}
.tf-insta-item{border-radius:12px;overflow:hidden;min-height:150px;}
.tf-insta-raw{padding:0 4px;}
/* ── View More Products button ── */
.tf-more-btn{display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 20px;border-radius:12px;background:rgba(128,128,128,.08);border:1.5px solid rgba(128,128,128,.2);text-decoration:none;color:inherit;font-size:13px;font-weight:600;margin-top:12px;transition:background .2s,transform .2s;}
.tf-more-btn:hover{background:rgba(128,128,128,.15);transform:translateY(-1px);}
.tf-more-btn i{font-size:14px;}
/* ── Social Connect ── */
.tf-social-grid{display:flex;flex-wrap:wrap;gap:10px;}
.tf-soc{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:17px;text-decoration:none;color:#fff;transition:transform .25s,box-shadow .25s;}
.tf-soc:hover{transform:translateY(-3px);box-shadow:0 6px 16px rgba(0,0,0,.2);}
.tf-soc-Facebook,.tf-soc-facebook{background:#1877F2;}
.tf-soc-Instagram,.tf-soc-instagram{background:radial-gradient(circle at 30% 107%,#fdf497 0%,#fdf497 5%,#fd5949 45%,#d6249f 60%,#285AEB 90%);}
.tf-soc-Twitter,.tf-soc-twitter,.tf-soc-X,.tf-soc-x-twitter{background:#000;}
.tf-soc-LinkedIn,.tf-soc-linkedin{background:#0A66C2;}
.tf-soc-WhatsApp,.tf-soc-whatsapp{background:#25D366;}
.tf-soc-YouTube,.tf-soc-youtube{background:#FF0000;}
.tf-soc-TikTok,.tf-soc-tiktok{background:#010101;}
.tf-soc-Pinterest,.tf-soc-pinterest{background:#E60023;}
.tf-soc-Snapchat,.tf-soc-snapchat{background:#FFFC00;color:#000 !important;}
.tf-soc-GitHub,.tf-soc-github{background:#333;}
.tf-soc-Spotify,.tf-soc-spotify{background:#1DB954;}
.tf-soc-Behance,.tf-soc-behance{background:#1769FF;}
.tf-soc-Dribbble,.tf-soc-dribbble{background:#EA4C89;}
/* ── Forms (Inquiry & Appointment) ── */
.tf-form{display:flex;flex-direction:column;gap:12px;}
.tf-form-group{display:flex;flex-direction:column;gap:5px;}
.tf-form-group label{font-size:11px;font-weight:600;opacity:.7;letter-spacing:.3px;}
.tf-form-group input,.tf-form-group textarea,.tf-form-group select{width:100%;padding:10px 14px;border:1.5px solid rgba(128,128,128,.2);border-radius:10px;font-family:inherit;font-size:13px;background:rgba(128,128,128,.06);color:inherit;outline:none;transition:border-color .2s;}
.tf-form-group input:focus,.tf-form-group textarea:focus,.tf-form-group select:focus{border-color:rgba(128,128,128,.5);}
.tf-form-group select option{background:#1a1a2e;color:#fff;}
.tf-form-2col{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.tf-submit-btn{width:100%;padding:13px;border:none;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;letter-spacing:.5px;background:rgba(128,128,128,.15);color:inherit;border:1.5px solid rgba(128,128,128,.25);transition:opacity .2s;}
.tf-submit-btn:hover{opacity:.8;}
.tf-submit-btn:disabled{opacity:.5;cursor:not-allowed;}
</style>

<?php if (!empty($products)): ?>
<div class="tf-sec fade-in-section">
  <div class="tf-sec-title"><i class="fas fa-shopping-bag"></i> Products</div>
  <div class="tf-products">
    <?php foreach ($products as $p): ?>
    <a href="<?= htmlspecialchars($p['product_url'] ?: '#') ?>" <?= !empty($p['product_url']) ? 'target="_blank"' : '' ?> class="tf-prod">
      <?php if (!empty($p['image'])): ?>
        <img src="<?= imgUrl($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="tf-prod-img" loading="lazy">
      <?php else: ?>
        <div class="tf-prod-no-img"><i class="fas fa-image"></i></div>
      <?php endif; ?>
      <div class="tf-prod-info">
        <div class="tf-prod-name"><?= htmlspecialchars($p['name']) ?></div>
        <?php if (isset($p['price']) && $p['price'] !== null && $p['price'] !== ''): ?>
        <div class="tf-prod-price"><?= htmlspecialchars($p['currency'] ?: 'INR') ?> <?= number_format((float)$p['price'], 2) ?></div>
        <?php endif; ?>
        <?php if (!empty($p['description'])): ?>
        <div class="tf-prod-desc"><?= htmlspecialchars($p['description']) ?></div>
        <?php endif; ?>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php if (!empty($storeUrl)): ?>
  <a href="<?= htmlspecialchars($storeUrl) ?>" target="_blank" class="tf-more-btn">
    <i class="fas fa-store"></i> View More Products
  </a>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php if (!empty($galleries)): ?>
<div class="tf-sec fade-in-section" style="padding-left:0;padding-right:0;">
  <div class="tf-sec-title" style="padding:0 22px;"><i class="fas fa-images"></i> Gallery</div>
  <?php foreach ($galleries as $g): ?>
    <?php if (!empty($g['images'])): ?>
      <?php if (!empty($g['name'])): ?><div class="tf-gal-name"><?= htmlspecialchars($g['name']) ?></div><?php endif; ?>
      <div class="tf-gal-grid">
        <?php foreach ($g['images'] as $img): ?>
        <div class="tf-gal-item">
          <a href="<?= imgUrl($img['image_url']) ?>" target="_blank">
            <img src="<?= imgUrl($img['image_url']) ?>" alt="Gallery" loading="lazy">
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (($vcard['show_appointments'] ?? 1)): ?>
<div class="tf-sec fade-in-section">
  <div class="tf-sec-title"><i class="fas fa-calendar-check"></i> Book Appointment</div>
  <form class="tf-form" onsubmit="submitAppointment(event)">
    <input type="hidden" name="vcard_id" value="<?= $vcardId ?>">
    <div class="tf-form-group">
      <label>Your Name *</label>
      <input type="text" name="name" required placeholder="Your name">
    </div>
    <div class="tf-form-2col">
      <div class="tf-form-group">
        <label>Phone *</label>
        <input type="tel" name="phone" required placeholder="9876543210">
      </div>
      <div class="tf-form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="optional">
      </div>
    </div>
    <?php if (!empty($services)): ?>
    <div class="tf-form-group">
      <label>Service</label>
      <select name="service">
        <option value="">Choose a service</option>
        <?php foreach ($services as $s): ?>
          <option value="<?= htmlspecialchars($s['name']) ?>"><?= htmlspecialchars($s['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php else: ?>
    <div class="tf-form-group">
      <label>Service / Reason</label>
      <input type="text" name="service" placeholder="What service do you need?">
    </div>
    <?php endif; ?>
    <div class="tf-form-group">
      <label>Date *</label>
      <input type="date" name="date" id="appointment-date" required min="<?= date('Y-m-d') ?>" onchange="fetchAvailableSlots(this.value, <?= $vcardId ?>)">
    </div>
    <div class="tf-form-group" id="time-container" style="display:none;">
      <label id="time-label">Available Times *</label>
      <select name="time" id="appointment-time" required disabled>
        <option value="">Select date first</option>
      </select>
    </div>
    <div class="tf-form-group">
      <label>Notes</label>
      <textarea name="notes" rows="2" placeholder="Any specific requirements..."></textarea>
    </div>
    <button type="submit" class="tf-submit-btn">
      <i class="fas fa-calendar-plus"></i> Book Appointment
    </button>
  </form>
</div>
<?php endif; ?>

<?php if (!empty($testimonials)): ?>
<div class="tf-sec fade-in-section">
  <div class="tf-sec-title"><i class="fas fa-quote-left"></i> What People Say</div>
  <div class="tf-testimonials">
    <?php foreach ($testimonials as $t): ?>
    <div class="tf-testi">
      <div class="tf-testi-stars"><?= str_repeat('★', (int)$t['rating']) . str_repeat('☆', 5 - (int)$t['rating']) ?></div>
      <div class="tf-testi-msg">"<?= htmlspecialchars($t['message']) ?>"</div>
      <div class="tf-testi-author"><?= htmlspecialchars($t['name']) ?></div>
      <?php
        $meta = trim(($t['designation'] ?? '') . (!empty($t['company']) ? ' @ ' . $t['company'] : ''), ' @');
        if ($meta):
      ?>
      <div class="tf-testi-meta"><?= htmlspecialchars($meta) ?></div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php
// Only render iframes section if at least one has a valid URL
$validIframes = array_filter($iframes, fn($fr) => !empty($fr['url']) && preg_match('#^https?://#i', $fr['url']));
if (!empty($validIframes)):
?>
<div class="tf-sec fade-in-section">
  <div class="tf-sec-title"><i class="fas fa-code"></i> Embedded Content</div>
  <?php foreach ($validIframes as $fr): ?>
  <div class="tf-iframe-wrap">
    <iframe src="<?= htmlspecialchars($fr['url']) ?>" height="320" allowfullscreen loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php
// Build renderable instagram items: prefer embed_url (iframe), fall back to raw tag HTML
$instaItems = [];
foreach ($insta_feed as $insta) {
    if (!empty($insta['embed_url']) && preg_match('#^https?://#i', $insta['embed_url'])) {
        $instaItems[] = ['type' => 'iframe', 'src' => $insta['embed_url']];
    } elseif (!empty($insta['tag'])) {
        // Check if tag itself is a direct Instagram URL → build embed URL
        if (preg_match('#https?://(?:www\.)?instagram\.com/(p|reel|tv)/([A-Za-z0-9_-]+)#', $insta['tag'], $m)) {
            $instaItems[] = ['type' => 'iframe', 'src' => 'https://www.instagram.com/' . $m[1] . '/' . $m[2] . '/embed/'];
        } elseif (strlen($insta['tag']) > 20) {
            // Raw HTML embed (blockquote format)
            $instaItems[] = ['type' => 'html', 'html' => $insta['tag']];
        }
    }
}
if (!empty($instaItems)):
?>
<div class="tf-sec fade-in-section">
  <div class="tf-sec-title"><i class="fab fa-instagram"></i> Instagram</div>
  <?php foreach ($instaItems as $item): ?>
    <?php if ($item['type'] === 'iframe'): ?>
    <div class="tf-insta-grid" style="margin-bottom:10px;">
      <div class="tf-insta-item" style="height:300px;grid-column:1/-1;">
        <iframe src="<?= htmlspecialchars($item['src']) ?>" width="100%" height="300" frameborder="0" scrolling="no" allowtransparency="true" loading="lazy" style="display:block;"></iframe>
      </div>
    </div>
    <?php else: ?>
    <div class="tf-insta-raw"><?= $item['html'] ?></div>
    <?php endif; ?>
  <?php endforeach; ?>
  <?php
  // Inject Instagram embed.js once if any raw blockquote embeds exist
  $hasRaw = array_filter($instaItems, fn($i) => $i['type'] === 'html');
  if (!empty($hasRaw)):
  ?>
  <script async src="https://www.instagram.com/embed.js"></script>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php if (!empty($socialLinks)): ?>
<div class="tf-sec fade-in-section">
  <div class="tf-sec-title"><i class="fas fa-share-nodes"></i> Connect</div>
  <div class="tf-social-grid">
    <?php
    $tfIconMap = [
      'Facebook'=>'fa-facebook-f','Instagram'=>'fa-instagram','Twitter'=>'fa-twitter',
      'X'=>'fa-x-twitter','LinkedIn'=>'fa-linkedin-in','WhatsApp'=>'fa-whatsapp',
      'YouTube'=>'fa-youtube','TikTok'=>'fa-tiktok','Pinterest'=>'fa-pinterest-p',
      'Snapchat'=>'fa-snapchat-ghost','GitHub'=>'fa-github','Spotify'=>'fa-spotify',
      'Behance'=>'fa-behance','Dribbble'=>'fa-dribbble',
    ];
    foreach ($socialLinks as $sl):
      $tfIcon = $tfIconMap[$sl['platform']] ?? 'fa-globe';
      $tfType = str_replace(' ','-', $sl['platform'] ?? '');
    ?>
    <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" class="tf-soc tf-soc-<?= htmlspecialchars($tfType) ?>" title="<?= htmlspecialchars($sl['platform']) ?>">
      <i class="fab <?= $tfIcon ?>"></i>
    </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php if (($vcard['display_inquiry_form'] ?? 1)): ?>
<div class="tf-sec fade-in-section">
  <div class="tf-sec-title"><i class="fas fa-paper-plane"></i> Get in Touch</div>
  <form class="tf-form" onsubmit="submitInquiry(event)">
    <input type="hidden" name="vcard_id" value="<?= $vcardId ?>">
    <div class="tf-form-group">
      <label>Name *</label>
      <input type="text" name="name" required placeholder="Your name">
    </div>
    <div class="tf-form-group">
      <label>Email *</label>
      <input type="email" name="email" required placeholder="you@example.com">
    </div>
    <div class="tf-form-group">
      <label>Phone</label>
      <input type="tel" name="phone" placeholder="+91 9876543210">
    </div>
    <div class="tf-form-group">
      <label>Message *</label>
      <textarea name="message" rows="3" required placeholder="Your message..."></textarea>
    </div>
    <button type="submit" class="tf-submit-btn">
      <i class="fas fa-paper-plane"></i> Send Message
    </button>
  </form>
</div>
<?php endif; ?>
