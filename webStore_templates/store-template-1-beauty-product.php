<?php
$shopName    = htmlspecialchars($store['store_name'] ?? 'Store');
$shopTagline = htmlspecialchars($store['tagline'] ?? '');
$shopDesc    = $store['description'] ?? '';
$logo        = !empty($store['logo_image'])  ? imgUrl($store['logo_image'])  : '';
$bannerImg   = !empty($store['cover_image']) ? imgUrl($store['cover_image']) : '';
$waPhone     = preg_replace('/\D/', '', $store['whatsapp_number'] ?? '');
$phone       = htmlspecialchars($store['phone'] ?? '');
$email       = htmlspecialchars($store['email'] ?? '');
$address     = htmlspecialchars($store['address'] ?? '');
$primary     = $store['primary_color'] ?? '#d63384';
$secondary   = $store['secondary_color'] ?? '#8b0043';
$currency    = $store['currency_symbol'] ?? '₹';
$deliveryFee = (float)($store['delivery_charge'] ?? 0);
$minOrder    = (float)($store['min_order_amount'] ?? 0);
$waTemplate  = $store['order_message_template'] ?? '';
$showSearch  = !empty($store['show_search']);
$showCats    = !empty($store['show_categories']) && count($categories) > 0;
$codAvail    = !empty($store['cod_available']);

foreach ($products as &$p) {
    $p['img']          = !empty($p['image']) ? imgUrl($p['image']) : (!empty($p['image_url']) ? imgUrl($p['image_url']) : '');
    $p['effective']    = ($p['discount_price'] > 0) ? (float)$p['discount_price'] : (float)$p['price'];
    $p['has_discount'] = ($p['discount_price'] !== null && $p['discount_price'] > 0);
    $p['disc_pct']     = $p['has_discount'] ? round((1 - $p['discount_price']/$p['price'])*100) : 0;
}
unset($p);
foreach ($categories as &$c) {
    $c['img'] = !empty($c['image']) ? imgUrl($c['image']) : (!empty($c['image_url']) ? imgUrl($c['image_url']) : '');
}
unset($c);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $shopName ?> – Beauty & Cosmetics</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
:root{--p:<?= $primary ?>;--s:<?= $secondary ?>;}
body{background:#f2e8ee;font-family:'Lato',sans-serif;display:flex;justify-content:center;min-height:100vh;}
.wrap{width:100%;max-width:480px;background:#fdf0f3;min-height:100vh;box-shadow:0 0 60px rgba(0,0,0,.12);position:relative;overflow-x:hidden;}

/* Top bar */
.top-bar{position:sticky;top:0;z-index:200;background:rgba(253,240,243,0.97);backdrop-filter:blur(10px);display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-bottom:1px solid rgba(214,51,132,0.12);}
.top-logo-row{display:flex;align-items:center;gap:10px;}
.logo-circle{width:46px;height:46px;border-radius:50%;object-fit:cover;border:2.5px solid var(--p);flex-shrink:0;}
.logo-placeholder{width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,var(--p),var(--s));display:flex;align-items:center;justify-content:center;color:#fff;font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:700;flex-shrink:0;}
.top-name{font-family:'Cormorant Garamond',serif;font-size:1.1rem;font-weight:700;color:#3a0a1e;max-width:190px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.top-wa{width:40px;height:40px;background:#25D366;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;font-size:1.2rem;flex-shrink:0;transition:transform .2s,box-shadow .2s;}
.top-wa:hover{transform:scale(1.1);box-shadow:0 4px 14px rgba(37,211,102,0.4);}

/* Hero */
.hero{position:relative;width:100%;height:200px;overflow:hidden;}
.hero img{width:100%;height:100%;object-fit:cover;}
.hero-bg{width:100%;height:100%;background:linear-gradient(135deg,var(--p) 0%,var(--s) 100%);display:flex;align-items:center;justify-content:center;}
.hero-bg-icon{font-size:5rem;color:rgba(255,255,255,0.18);}
.hero-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(253,240,243,0.88) 0%,transparent 55%);}

/* Store info block */
.store-info{padding:20px 20px 4px;text-align:center;}
.store-info h1{font-family:'Cormorant Garamond',serif;font-size:2.1rem;font-weight:700;color:#3a0a1e;line-height:1.1;letter-spacing:.3px;}
.store-tagline{font-family:'Cormorant Garamond',serif;font-size:1rem;font-style:italic;color:#b0587a;margin-top:5px;}
.badges-row{display:flex;justify-content:center;gap:8px;margin-top:14px;flex-wrap:wrap;}
.badge{display:inline-flex;align-items:center;gap:5px;background:#fff;border:1px solid #f3c6db;color:#a0456b;font-size:.72rem;font-weight:700;padding:5px 12px;border-radius:50px;letter-spacing:.2px;}
.badge-p{background:linear-gradient(135deg,var(--p),var(--s));color:#fff;border:none;}
.store-desc-block{margin:14px 20px 0;background:#fff;border-radius:14px;padding:14px 16px;font-size:.84rem;color:#7a3050;line-height:1.65;border:1px solid #f3c6db;}

/* Search */
.search-wrap{padding:14px 20px 0;}
.search-inner{display:flex;align-items:center;gap:10px;background:#fff;border:1.5px solid #f3c6db;border-radius:50px;padding:10px 18px;transition:border-color .25s,box-shadow .25s;}
.search-inner:focus-within{border-color:var(--p);box-shadow:0 0 0 3px rgba(214,51,132,0.1);}
.search-inner i{color:var(--p);font-size:.88rem;}
.search-inner input{border:none;outline:none;background:transparent;flex:1;font-family:'Lato',sans-serif;font-size:.88rem;color:#3a0a1e;}
.search-inner input::placeholder{color:#c3a0b0;}

/* Section label */
.sec-label{padding:20px 20px 6px;font-family:'Cormorant Garamond',serif;font-size:1.25rem;font-weight:700;color:#3a0a1e;display:flex;align-items:center;gap:8px;}
.sec-label::after{content:'';flex:1;height:1px;background:linear-gradient(to right,#f3c6db,transparent);}

/* Category scroll */
.cat-scroll{display:flex;gap:14px;overflow-x:auto;padding:8px 20px 14px;scrollbar-width:none;}
.cat-scroll::-webkit-scrollbar{display:none;}
.cat-btn{border:none;background:none;display:flex;flex-direction:column;align-items:center;gap:6px;cursor:pointer;padding:2px;flex-shrink:0;}
.cat-img-wrap{width:64px;height:64px;border-radius:50%;overflow:hidden;background:#fff;border:2.5px solid #f3c6db;display:flex;align-items:center;justify-content:center;transition:all .25s;flex-shrink:0;}
.cat-img-wrap img{width:100%;height:100%;object-fit:cover;}
.cat-icon-inner{font-size:1.5rem;color:var(--p);}
.cat-btn.active .cat-img-wrap{border-color:var(--p);background:#fff0f6;box-shadow:0 0 0 4px rgba(214,51,132,0.14);}
.cat-lbl{font-size:.71rem;font-weight:700;color:#a0456b;white-space:nowrap;max-width:70px;overflow:hidden;text-overflow:ellipsis;text-align:center;transition:color .2s;}
.cat-btn.active .cat-lbl{color:var(--p);}

/* Product grid */
.prod-section{padding:4px 12px 100px;}
.prod-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.prod-card{background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 3px 14px rgba(214,51,132,0.08);transition:transform .3s,box-shadow .3s;position:relative;}
.prod-card:hover{transform:translateY(-5px);box-shadow:0 10px 30px rgba(214,51,132,0.2);}
.prod-img-wrap{position:relative;aspect-ratio:1;overflow:hidden;background:#fdf0f3;}
.prod-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .4s;}
.prod-card:hover .prod-img-wrap img{transform:scale(1.07);}
.prod-img-ph{width:100%;aspect-ratio:1;background:linear-gradient(135deg,#fce4ef 0%,#f8c5d5 100%);display:flex;align-items:center;justify-content:center;font-size:2.8rem;color:#e8a0bc;}
.bdg-disc{position:absolute;top:9px;right:9px;background:var(--p);color:#fff;font-size:.65rem;font-weight:900;padding:3px 9px;border-radius:50px;z-index:2;letter-spacing:.3px;}
.bdg-feat{position:absolute;top:9px;left:9px;background:#f5c842;color:#3a2000;font-size:.62rem;font-weight:900;padding:3px 8px;border-radius:50px;z-index:2;}
.oos-layer{position:absolute;inset:0;background:rgba(255,255,255,0.6);backdrop-filter:blur(2px);display:flex;align-items:center;justify-content:center;z-index:3;}
.oos-badge{background:rgba(80,20,40,0.82);color:#fff;font-size:.7rem;font-weight:900;padding:6px 14px;border-radius:50px;letter-spacing:.8px;}
.prod-body{padding:11px 12px 14px;}
.prod-name{font-size:.86rem;font-weight:700;color:#3a0a1e;margin-bottom:5px;line-height:1.35;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;}
.prod-prices{display:flex;align-items:baseline;gap:7px;margin-bottom:11px;}
.prod-price{font-size:1rem;font-weight:900;color:var(--p);}
.prod-orig{font-size:.76rem;color:#c0a0b0;text-decoration:line-through;}
.add-btn{width:100%;padding:10px;background:var(--p);color:#fff;border:none;border-radius:12px;font-family:'Lato',sans-serif;font-weight:700;font-size:.81rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;transition:background .2s,transform .15s,box-shadow .2s;}
.add-btn:hover{background:var(--s);box-shadow:0 4px 14px rgba(214,51,132,0.3);}
.add-btn:active{transform:scale(.95);}
.add-btn:disabled{background:#e8d8e0;color:#c0a0b0;cursor:not-allowed;box-shadow:none;}

/* Empty state */
.empty-state{text-align:center;padding:50px 20px;color:#c3a0b0;}
.empty-state i{font-size:3.5rem;margin-bottom:14px;display:block;}
.empty-state p{font-family:'Cormorant Garamond',serif;font-size:1.15rem;}

/* Footer */
.footer{background:linear-gradient(160deg,var(--s) 0%,var(--p) 100%);color:#fff;padding:28px 20px 24px;text-align:center;}
.footer-name{font-family:'Cormorant Garamond',serif;font-size:1.4rem;font-weight:700;letter-spacing:.5px;margin-bottom:14px;}
.footer-line{display:flex;align-items:center;justify-content:center;gap:8px;font-size:.82rem;margin-bottom:8px;opacity:.9;}
.footer-line i{font-size:.85rem;}
.footer-line a{color:#fff;text-decoration:none;}
.footer-cod-tag{display:inline-block;background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.35);color:#fff;font-size:.72rem;font-weight:700;padding:5px 14px;border-radius:50px;margin:10px 0 16px;}
.footer-powered{font-size:.7rem;opacity:.65;line-height:1.8;}
.footer-powered a{color:#fff;text-decoration:none;}

/* Fade up */
.fade-up{opacity:0;transform:translateY(24px);transition:opacity .55s ease,transform .55s ease;}
.fade-up.visible{opacity:1;transform:none;}
</style>
</head>
<body>
<div class="wrap">

  <!-- Top Bar -->
  <div class="top-bar">
    <div class="top-logo-row">
      <?php if($logo): ?>
        <img src="<?= $logo ?>" alt="logo" class="logo-circle">
      <?php else: ?>
        <div class="logo-placeholder"><?= mb_substr($shopName,0,1) ?></div>
      <?php endif; ?>
      <span class="top-name"><?= $shopName ?></span>
    </div>
    <?php if($waPhone): ?>
      <a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="top-wa" title="Chat on WhatsApp"><i class="fab fa-whatsapp"></i></a>
    <?php endif; ?>
  </div>

  <!-- Hero Banner -->
  <div class="hero">
    <?php if($bannerImg): ?>
      <img src="<?= $bannerImg ?>" alt="<?= $shopName ?>">
    <?php else: ?>
      <div class="hero-bg"><i class="fas fa-spa hero-bg-icon"></i></div>
    <?php endif; ?>
    <div class="hero-overlay"></div>
  </div>

  <!-- Store Info -->
  <div class="store-info fade-up">
    <h1><?= $shopName ?></h1>
    <?php if($shopTagline): ?><div class="store-tagline"><?= $shopTagline ?></div><?php endif; ?>
    <div class="badges-row">
      <span class="badge"><i class="fas fa-box-open"></i><?= count($products) ?> Products</span>
      <?php if($codAvail): ?><span class="badge badge-p"><i class="fas fa-money-bill-wave"></i>COD Available</span><?php endif; ?>
      <?php if($deliveryFee > 0): ?><span class="badge"><i class="fas fa-truck"></i>Delivery <?= $currency.number_format($deliveryFee,2) ?></span><?php endif; ?>
    </div>
  </div>
  <?php if($shopDesc): ?>
  <div class="store-desc-block fade-up"><i class="fas fa-quote-left" style="color:var(--p);margin-right:6px;font-size:.8rem;"></i><?= htmlspecialchars($shopDesc) ?></div>
  <?php endif; ?>

  <!-- Search -->
  <?php if($showSearch): ?>
  <div class="search-wrap fade-up">
    <div class="search-inner">
      <i class="fas fa-search"></i>
      <input type="text" id="searchInput" placeholder="Search beauty products…" oninput="searchProducts()">
    </div>
  </div>
  <?php endif; ?>

  <!-- Categories -->
  <?php if($showCats): ?>
  <div class="sec-label fade-up">Shop by Category</div>
  <div class="cat-scroll fade-up">
    <button class="cat-btn active" onclick="filterByCategory('all',this)">
      <div class="cat-img-wrap"><span class="cat-icon-inner"><i class="fas fa-th-large"></i></span></div>
      <span class="cat-lbl">All</span>
    </button>
    <?php foreach($categories as $c): ?>
    <button class="cat-btn" onclick="filterByCategory('<?= $c['id'] ?>',this)">
      <div class="cat-img-wrap">
        <?php if($c['img']): ?><img src="<?= $c['img'] ?>" alt=""><?php else: ?><span class="cat-icon-inner"><i class="fas fa-tag"></i></span><?php endif; ?>
      </div>
      <span class="cat-lbl"><?= htmlspecialchars($c['name']) ?></span>
    </button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Products -->
  <div class="sec-label fade-up">Our Products</div>
  <div class="prod-section">
    <?php if(!empty($products)): ?>
    <div class="prod-grid">
      <?php foreach($products as $i => $p): ?>
      <div class="prod-card fade-up" data-cat="<?= $p['category_id'] ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>" style="transition-delay:<?= ($i % 8) * 0.06 ?>s">
        <div class="prod-img-wrap">
          <?php if($p['img']): ?>
            <img src="<?= $p['img'] ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          <?php else: ?>
            <div class="prod-img-ph"><i class="fas fa-spa"></i></div>
          <?php endif; ?>
          <?php if($p['has_discount']): ?><span class="bdg-disc">-<?= $p['disc_pct'] ?>%</span><?php endif; ?>
          <?php if(!empty($p['is_featured'])): ?><span class="bdg-feat">★ Featured</span><?php endif; ?>
          <?php if(!$p['in_stock']): ?><div class="oos-layer"><span class="oos-badge">SOLD OUT</span></div><?php endif; ?>
        </div>
        <div class="prod-body">
          <div class="prod-name"><?= htmlspecialchars($p['name']) ?></div>
          <div class="prod-prices">
            <span class="prod-price"><?= $currency . number_format($p['effective'],2) ?></span>
            <?php if($p['has_discount']): ?><span class="prod-orig"><?= $currency . number_format($p['price'],2) ?></span><?php endif; ?>
          </div>
          <?php if($p['in_stock']): ?>
          <button class="add-btn" onclick="addToCart(<?= $p['id'] ?>,'<?= htmlspecialchars(addslashes($p['name'])) ?>',<?= $p['effective'] ?>,'<?= htmlspecialchars($p['img'] ?? '') ?>')">
            <i class="fas fa-shopping-cart"></i> Add to Cart
          </button>
          <?php else: ?>
          <button class="add-btn" disabled><i class="fas fa-times-circle"></i> Sold Out</button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <i class="fas fa-box-open"></i>
      <p>No products available yet</p>
    </div>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <div class="footer">
    <div class="footer-name"><?= $shopName ?></div>
    <?php if($address): ?><div class="footer-line"><i class="fas fa-map-marker-alt"></i><span><?= $address ?></span></div><?php endif; ?>
    <?php if($phone): ?><div class="footer-line"><i class="fas fa-phone"></i><a href="tel:<?= $phone ?>"><?= $phone ?></a></div><?php endif; ?>
    <?php if($email): ?><div class="footer-line"><i class="fas fa-envelope"></i><a href="mailto:<?= $email ?>"><?= $email ?></a></div><?php endif; ?>
    <?php if($codAvail): ?><div><span class="footer-cod-tag"><i class="fas fa-money-bill-wave"></i> Cash on Delivery Available</span></div><?php endif; ?>
    <div class="footer-powered">
      <p>Powered by <a href="/">Tapify</a></p>
      <p>A unit of <strong>Mr Print World</strong></p>
    </div>
  </div>

</div><!-- /.wrap -->

<!-- Cart Overlay -->
<div id="cartOverlay" onclick="closeCart()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:900;"></div>

<!-- Cart Drawer (bottom sheet) -->
<div id="cartDrawer" style="position:fixed;bottom:0;left:50%;transform:translateX(-50%) translateY(100%);width:100%;max-width:480px;background:#fff;border-radius:20px 20px 0 0;z-index:901;transition:transform .35s cubic-bezier(.32,0,.67,0);max-height:85vh;overflow-y:auto;box-shadow:0 -10px 40px rgba(0,0,0,0.2);">
  <style>
  #cartDrawer.open{transform:translateX(-50%) translateY(0);}
  #cartDrawer .drawer-handle{width:40px;height:4px;background:#e5e7eb;border-radius:2px;margin:12px auto 16px;}
  #cartDrawer .drawer-header{display:flex;justify-content:space-between;align-items:center;padding:0 20px 16px;border-bottom:1px solid #f3f4f6;}
  #cartDrawer .drawer-title{font-size:1.1rem;font-weight:700;}
  #cartDrawer .drawer-close{background:none;border:none;font-size:1.5rem;cursor:pointer;color:#6b7280;line-height:1;}
  .c-item{display:flex;gap:12px;padding:12px 20px;border-bottom:1px solid #f9fafb;}
  .c-img{width:56px;height:56px;border-radius:10px;overflow:hidden;background:#f3f4f6;flex-shrink:0;display:flex;align-items:center;justify-content:center;color:#9ca3af;}
  .c-img img{width:100%;height:100%;object-fit:cover;}
  .c-info{flex:1;}
  .c-name{font-size:.88rem;font-weight:600;margin-bottom:2px;}
  .c-price{font-weight:700;font-size:.95rem;margin-bottom:6px;}
  .c-qty-row{display:flex;align-items:center;gap:8px;}
  .c-qty-btn{width:26px;height:26px;border-radius:50%;border:1.5px solid #e5e7eb;background:#fff;cursor:pointer;font-size:.9rem;display:flex;align-items:center;justify-content:center;}
  .c-qty-val{font-weight:700;min-width:20px;text-align:center;}
  .c-del{border-color:#fee2e2;color:#ef4444;}
  #cartSummary{padding:12px 20px;}
  .c-sum-row{display:flex;justify-content:space-between;padding:5px 0;font-size:.9rem;}
  .c-total{font-weight:700;font-size:1rem;padding-top:10px;border-top:2px dashed #e5e7eb;margin-top:6px;}
  .c-min-warn{color:#ef4444;font-size:.78rem;text-align:center;margin-top:6px;}
  #checkoutSection{padding:16px 20px 24px;}
  #checkoutSection h4{font-size:.95rem;font-weight:700;margin-bottom:14px;}
  .ck-field{margin-bottom:10px;}
  .ck-field label{display:block;font-size:.78rem;font-weight:600;margin-bottom:4px;color:#6b7280;}
  .ck-field input,.ck-field textarea{width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-family:inherit;font-size:.88rem;}
  .ck-field input:focus,.ck-field textarea:focus{outline:none;border-color:var(--p);}
  .ck-btn{width:100%;padding:14px;border:none;border-radius:12px;background:var(--p);color:#fff;font-weight:700;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;}
  .tap-toast{position:fixed;top:20px;left:50%;transform:translateX(-50%) translateY(-80px);background:#1a2035;color:#fff;padding:10px 22px;border-radius:50px;font-weight:600;font-size:.88rem;z-index:9999;transition:transform .35s;white-space:nowrap;}
  .tap-toast.show{transform:translateX(-50%) translateY(0);}
  .tap-toast.error{background:#ef4444;}
  .tap-toast.success{background:#10b981;}
  </style>
  <div class="drawer-handle"></div>
  <div class="drawer-header">
    <span class="drawer-title">🛒 Your Cart</span>
    <button class="drawer-close" onclick="closeCart()">×</button>
  </div>
  <div id="cartList"></div>
  <div id="cartSummary"></div>
  <form id="checkoutSection" onsubmit="placeOrder(event)" style="display:none;">
    <h4>Your Details</h4>
    <input type="hidden" name="store_id" value="<?= $storeId ?>">
    <div class="ck-field"><label>Name *</label><input name="name" required placeholder="Your full name"></div>
    <div class="ck-field"><label>Phone *</label><input name="phone" type="tel" required placeholder="WhatsApp number"></div>
    <div class="ck-field"><label>Delivery Address</label><textarea name="address" rows="2" placeholder="Street, city, pincode..."></textarea></div>
    <div class="ck-field"><label>Notes</label><textarea name="notes" rows="2" placeholder="Any special instructions..."></textarea></div>
    <button type="submit" class="ck-btn"><i class="fab fa-whatsapp"></i> Place Order on WhatsApp</button>
  </form>
</div>

<!-- Floating Cart Bar -->
<div id="floatCart" onclick="openCart()" style="display:none;position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:var(--p);color:#fff;padding:14px 24px;border-radius:50px;cursor:pointer;z-index:850;align-items:center;gap:12px;box-shadow:0 8px 30px rgba(0,0,0,0.25);max-width:92vw;font-weight:700;white-space:nowrap;">
  <span id="cartBubble" style="background:#fff;color:var(--p);width:24px;height:24px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;">0</span>
  <span>View Cart</span>
  <span id="floatTotal" style="margin-left:6px;"><?= $currency ?>0</span>
</div>

<script>
const STORE_DATA = {
    id: <?= $storeId ?>,
    whatsapp: '<?= $waPhone ?>',
    currency: '<?= $currency ?>',
    deliveryFee: <?= $deliveryFee ?>,
    minOrder: <?= $minOrder ?>,
    template: <?= json_encode($waTemplate) ?>
};
let cart = [];

function addToCart(id, name, price, imgSrc) {
    const ex = cart.find(i => i.id === id);
    if (ex) { ex.qty++; } else { cart.push({ id, name, price, img: imgSrc, qty: 1 }); }
    updateCartUI();
    showToast('✓ ' + name + ' added!');
}
function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);
    updateCartUI();
    renderCartDrawer();
}
function changeQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty = Math.max(1, item.qty + delta);
    updateCartUI(); renderCartDrawer();
}
function getCount() { return cart.reduce((s,i)=>s+i.qty,0); }
function getSub()   { return cart.reduce((s,i)=>s+i.qty*i.price,0); }
function updateCartUI() {
    const count = getCount(), sub = getSub(), total = sub + STORE_DATA.deliveryFee;
    document.getElementById('cartBubble').textContent = count;
    document.getElementById('floatTotal').textContent = STORE_DATA.currency + total.toFixed(2);
    document.getElementById('floatCart').style.display = count > 0 ? 'flex' : 'none';
}
function openCart() {
    if (!cart.length) { showToast('Cart is empty','error'); return; }
    renderCartDrawer();
    document.getElementById('cartDrawer').classList.add('open');
    document.getElementById('cartOverlay').style.display = 'block';
}
function closeCart() {
    document.getElementById('cartDrawer').classList.remove('open');
    document.getElementById('cartOverlay').style.display = 'none';
}
function renderCartDrawer() {
    const sub = getSub(), total = sub + STORE_DATA.deliveryFee;
    document.getElementById('cartList').innerHTML = cart.map(item => `
        <div class="c-item">
            <div class="c-img">${item.img ? `<img src="${item.img}" alt="">` : '<i class="fas fa-image"></i>'}</div>
            <div class="c-info">
                <div class="c-name">${item.name}</div>
                <div class="c-price">${STORE_DATA.currency}${item.price.toFixed(2)}</div>
                <div class="c-qty-row">
                    <button class="c-qty-btn" onclick="changeQty(${item.id},-1)">−</button>
                    <span class="c-qty-val">${item.qty}</span>
                    <button class="c-qty-btn" onclick="changeQty(${item.id},1)">+</button>
                    <button class="c-qty-btn c-del" onclick="removeFromCart(${item.id})"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>
        </div>`).join('');
    document.getElementById('cartSummary').innerHTML = `
        <div class="c-sum-row"><span>Subtotal</span><span>${STORE_DATA.currency}${sub.toFixed(2)}</span></div>
        ${STORE_DATA.deliveryFee>0?`<div class="c-sum-row"><span>Delivery</span><span>${STORE_DATA.currency}${STORE_DATA.deliveryFee.toFixed(2)}</span></div>`:''}
        <div class="c-sum-row c-total"><span>Total</span><span>${STORE_DATA.currency}${total.toFixed(2)}</span></div>
        ${sub<STORE_DATA.minOrder&&STORE_DATA.minOrder>0?`<p class="c-min-warn">Min order: ${STORE_DATA.currency}${STORE_DATA.minOrder.toFixed(2)}</p>`:''}`;
    document.getElementById('checkoutSection').style.display = (sub >= STORE_DATA.minOrder || STORE_DATA.minOrder===0) ? 'block' : 'none';
}
function placeOrder(e) {
    e.preventDefault();
    const d = Object.fromEntries(new FormData(e.target));
    const sub = getSub(), total = sub + STORE_DATA.deliveryFee;
    let items = cart.map(i=>`• ${i.name} x${i.qty} = ${STORE_DATA.currency}${(i.qty*i.price).toFixed(2)}`).join('\n');
    let msg = (STORE_DATA.template||'🛍️ NEW ORDER\n\nName: {customer_name}\nPhone: {customer_phone}\n\n{items}\n\nTotal: {total}')
        .replace('{customer_name}',d.name).replace('{customer_phone}',d.phone)
        .replace('{items}',items).replace('{total}',STORE_DATA.currency+total.toFixed(2));
    if(d.address) msg+='\nAddress: '+d.address;
    if(d.notes) msg+='\nNotes: '+d.notes;
    fetch('/backend/store-order-submit.php',{method:'POST',headers:{'Content-Type':'application/json'},
        body:JSON.stringify({store_id:STORE_DATA.id,customer_name:d.name,customer_phone:d.phone,
        customer_address:d.address||'',items:cart,subtotal:sub,delivery_charge:STORE_DATA.deliveryFee,
        total_amount:total,notes:d.notes||''})}).catch(()=>{});
    window.location.href=`https://wa.me/${STORE_DATA.whatsapp}?text=${encodeURIComponent(msg)}`;
}
function filterByCategory(catId, el) {
    document.querySelectorAll('.cat-btn').forEach(b=>b.classList.remove('active'));
    if(el) el.classList.add('active');
    document.querySelectorAll('.prod-card').forEach(c=>{
        c.style.display=(catId==='all'||c.dataset.cat==catId)?'':'none';
    });
}
function searchProducts() {
    const q=(document.getElementById('searchInput')?.value||'').toLowerCase();
    document.querySelectorAll('.prod-card').forEach(c=>{
        c.style.display=c.dataset.name.includes(q)?'':'none';
    });
}
function showToast(msg, type='success') {
    const t=document.createElement('div'); t.className='tap-toast '+type; t.textContent=msg;
    document.body.appendChild(t);
    setTimeout(()=>t.classList.add('show'),10);
    setTimeout(()=>{t.classList.remove('show');setTimeout(()=>t.remove(),400);},2500);
}
const obs=new IntersectionObserver(entries=>entries.forEach(e=>{
    if(e.isIntersecting){e.target.classList.add('visible');obs.unobserve(e.target);}
}),{threshold:0.1});
document.querySelectorAll('.fade-up').forEach(el=>obs.observe(el));
</script>
</body>
</html>
