<?php
/**
 * _features.php — Shared feature sections for standalone templates
 * Renders: Products, Galleries, Testimonials, Iframes, Instagram Feed
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
.tf-insta-item{border-radius:12px;overflow:hidden;height:150px;}
.tf-insta-item iframe{width:100%;height:100%;border:none;display:block;}
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
        <?php if ($p['price'] !== null && $p['price'] !== ''): ?>
        <div class="tf-prod-price"><?= htmlspecialchars($p['currency'] ?: 'INR') ?> <?= number_format((float)$p['price'], 2) ?></div>
        <?php endif; ?>
        <?php if (!empty($p['description'])): ?>
        <div class="tf-prod-desc"><?= htmlspecialchars($p['description']) ?></div>
        <?php endif; ?>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
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

<?php if (!empty($iframes)): ?>
<div class="tf-sec fade-in-section">
  <div class="tf-sec-title"><i class="fas fa-code"></i> Embedded Content</div>
  <?php foreach ($iframes as $fr): ?>
  <div class="tf-iframe-wrap">
    <iframe src="<?= htmlspecialchars($fr['url']) ?>" height="320" allowfullscreen loading="lazy" sandbox="allow-scripts allow-same-origin allow-forms allow-popups"></iframe>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($insta_feed)): ?>
<div class="tf-sec fade-in-section">
  <div class="tf-sec-title"><i class="fab fa-instagram"></i> Instagram</div>
  <div class="tf-insta-grid">
    <?php foreach ($insta_feed as $insta): ?>
    <div class="tf-insta-item">
      <iframe src="<?= htmlspecialchars($insta['embed_url']) ?>" scrolling="no" allowtransparency="true" loading="lazy"></iframe>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>
