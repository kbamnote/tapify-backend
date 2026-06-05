<?php
/**
 * _service-categories.php — Category-based services.
 * Each category renders as its own section; its items show as a big SQUARE
 * image carousel (image + name) with tap-to-open lightbox.
 * Relies on the carousel + lightbox CSS/JS in _shared-scripts.php.
 */
if (!empty($serviceCategories)):
?>
<style>
.tf-svci-card{position:relative;display:block;border-radius:18px;overflow:hidden;text-decoration:none;color:#fff;background:rgba(128,128,128,.08);border:1px solid rgba(128,128,128,.14);box-shadow:0 8px 24px rgba(0,0,0,.18);}
.tf-svci-card img{width:100%;aspect-ratio:1/1;height:auto;object-fit:cover;display:block;transition:transform 6s ease;}
.tf-slide:hover .tf-svci-card img{transform:scale(1.07);}
.tf-svci-noimg{width:100%;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;background:rgba(128,128,128,.12);font-size:3rem;color:rgba(128,128,128,.35);}
.tf-svci-info{position:absolute;left:0;right:0;bottom:0;padding:34px 14px 14px;background:linear-gradient(to top,rgba(0,0,0,.82),rgba(0,0,0,.3) 55%,transparent);}
.tf-svci-name{font-size:15px;font-weight:700;line-height:1.25;text-shadow:0 1px 4px rgba(0,0,0,.5);color:#fff;}
.tf-svccat-sec{padding:0 0 20px;}
.tf-svccat-title{font-size:11px;letter-spacing:2.5px;text-transform:uppercase;font-weight:700;margin-bottom:14px;opacity:.8;display:flex;align-items:center;gap:10px;padding:0 22px;}
</style>
<?php foreach ($serviceCategories as $cat): ?>
  <?php if (!empty($cat['items'])): ?>
  <div class="tf-svccat-sec fade-in-section">
    <div class="tf-svccat-title"><i class="fas fa-briefcase"></i> <?= htmlspecialchars($cat['name'] ?: 'Services') ?></div>
    <div class="tf-carousel" data-carousel>
      <?php if (count($cat['items']) > 1): ?>
      <button class="tf-arrow tf-arrow-prev" type="button" data-carousel-prev aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
      <button class="tf-arrow tf-arrow-next" type="button" data-carousel-next aria-label="Next"><i class="fas fa-chevron-right"></i></button>
      <?php endif; ?>
      <div class="tf-track" data-carousel-track>
        <?php foreach ($cat['items'] as $si): ?>
        <div class="tf-slide">
          <div class="tf-svci-card"<?= !empty($si['image']) ? ' data-lightbox="'.htmlspecialchars(imgUrl($si['image'])).'"' : '' ?> style="cursor:<?= !empty($si['image']) ? 'zoom-in' : 'default' ?>;">
            <?php if (!empty($si['image'])): ?>
              <img src="<?= imgUrl($si['image']) ?>" alt="<?= htmlspecialchars($si['name'] ?? '') ?>" loading="lazy">
            <?php else: ?>
              <div class="tf-svci-noimg"><i class="fas fa-briefcase"></i></div>
            <?php endif; ?>
            <div class="tf-svci-info"><div class="tf-svci-name"><?= htmlspecialchars($si['name'] ?? '') ?></div></div>
            <?php if (!empty($si['image'])): ?><div class="tf-gal-zoom"><i class="fas fa-expand"></i></div><?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (count($cat['items']) > 1): ?><div class="tf-dots" data-carousel-dots></div><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
