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
$primary     = $store['primary_color'] ?? '#2563eb';
$secondary   = $store['secondary_color'] ?? '#1e40af';
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
<title><?= $shopName ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
:root{--p:<?= $primary ?>;--s:<?= $secondary ?>;}
body{background:#e8edf5;font-family:'Inter',sans-serif;display:flex;justify-content:center;min-height:100vh;}
.wrap{width:100%;max-width:480px;background:#f8fafc;min-height:100vh;box-shadow:0 0 60px rgba(0,0,0,.12);overflow-x:hidden;position:relative;}

/* Navbar */
.navbar{position:sticky;top:0;z-index:300;background:var(--s);padding:0 16px;display:flex;align-items:center;justify-content:space-between;height:58px;}
.nav-brand{display:flex;align-items:center;gap:10px;}
.nav-logo{width:36px;height:36px;border-radius:10px;object-fit:cover;border:2px solid rgba(255,255,255,0.3);}
.nav-logo-ph{width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:1rem;}
.nav-name{color:#fff;font-weight:700;font-size:1rem;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.nav-actions{display:flex;align-items:center;gap:10px;}
.nav-icon-btn{width:38px;height:38px;background:rgba(255,255,255,0.15);border:none;border-radius:10px;color:#fff;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;text-decoration:none;}
.nav-icon-btn:hover{background:rgba(255,255,255,0.25);}

/* Search bar slide */
.search-bar{background:var(--s);padding:0 16px;max-height:0;overflow:hidden;transition:max-height .3s ease,padding .3s ease;}
.search-bar.open{max-height:70px;padding:0 16px 14px;}
.search-inner{display:flex;align-items:center;gap:10px;background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);border-radius:10px;padding:9px 14px;transition:border-color .2s;}
.search-inner:focus-within{border-color:rgba(255,255,255,0.8);}
.search-inner i{color:rgba(255,255,255,0.7);font-size:.88rem;}
.search-inner input{border:none;outline:none;background:transparent;flex:1;font-family:'Inter',sans-serif;font-size:.88rem;color:#fff;}
.search-inner input::placeholder{color:rgba(255,255,255,0.55);}

/* Hero */
.hero{position:relative;width:100%;height:180px;overflow:hidden;}
.hero img{width:100%;height:100%;object-fit:cover;}
.hero-gradient{width:100%;height:100%;background:linear-gradient(135deg,var(--s) 0%,var(--p) 100%);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;}
.hero-gradient h2{color:#fff;font-size:1.6rem;font-weight:800;letter-spacing:-.3px;}
.hero-gradient p{color:rgba(255,255,255,0.8);font-size:.9rem;}
.hero-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(248,250,252,0.7) 0%,transparent 50%);}

/* Stats row */
.stats-row{display:flex;justify-content:center;gap:8px;padding:14px 16px;flex-wrap:wrap;}
.stat-pill{display:inline-flex;align-items:center;gap:6px;background:#fff;border:1px solid #e2e8f0;color:#475569;font-size:.72rem;font-weight:700;padding:5px 13px;border-radius:50px;box-shadow:0 1px 4px rgba(0,0,0,0.05);}
.stat-pill.primary{background:var(--p);border-color:var(--p);color:#fff;}

/* Description */
.desc-box{margin:0 16px 4px;background:#fff;border-radius:14px;padding:14px 16px;border:1px solid #e2e8f0;display:flex;gap:10px;align-items:flex-start;}
.desc-box i{color:var(--p);font-size:1.1rem;margin-top:2px;flex-shrink:0;}
.desc-box p{font-size:.84rem;color:#475569;line-height:1.65;}

/* Section label */
.sec-label{padding:18px 16px 8px;font-size:.72rem;font-weight:800;color:#94a3b8;letter-spacing:.8px;text-transform:uppercase;}

/* Categories */
.cat-scroll{display:flex;gap:8px;overflow-x:auto;padding:0 16px 14px;scrollbar-width:none;}
.cat-scroll::-webkit-scrollbar{display:none;}
.cat-btn{border:2px solid #e2e8f0;background:#fff;color:#64748b;font-family:'Inter',sans-serif;font-size:.8rem;font-weight:600;padding:7px 16px;border-radius:50px;cursor:pointer;white-space:nowrap;transition:all .2s;flex-shrink:0;}
.cat-btn.active{background:var(--p);border-color:var(--p);color:#fff;}
.cat-btn:hover:not(.active){border-color:var(--p);color:var(--p);}

/* Products */
.prod-section{padding:0 12px 100px;}
.prod-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.prod-card{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);transition:transform .3s,box-shadow .3s;position:relative;}
.prod-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,0.12);}
.prod-card.featured{border-top:3px solid var(--p);}
.prod-img-wrap{position:relative;aspect-ratio:1;overflow:hidden;background:#f1f5f9;}
.prod-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .4s;}
.prod-card:hover .prod-img-wrap img{transform:scale(1.06);}
.prod-img-ph{width:100%;aspect-ratio:1;background:linear-gradient(135deg,#e0eaff,#c7d8ff);display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:#93b4ff;}
.bdg-disc{position:absolute;top:8px;right:8px;background:#ef4444;color:#fff;font-size:.63rem;font-weight:900;padding:3px 8px;border-radius:50px;z-index:2;}
.bdg-feat{position:absolute;top:8px;left:8px;background:var(--p);color:#fff;font-size:.62rem;font-weight:800;padding:3px 8px;border-radius:50px;z-index:2;}
.oos-layer{position:absolute;inset:0;background:rgba(248,250,252,0.7);backdrop-filter:blur(2px);display:flex;align-items:center;justify-content:center;z-index:3;}
.oos-badge{background:rgba(71,85,105,0.85);color:#fff;font-size:.68rem;font-weight:800;padding:5px 12px;border-radius:50px;}
.prod-body{padding:10px 12px 13px;}
.prod-name{font-size:.84rem;font-weight:600;color:#1e293b;margin-bottom:5px;line-height:1.35;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;}
.prod-prices{display:flex;align-items:baseline;gap:6px;margin-bottom:10px;}
.prod-price{font-size:1rem;font-weight:800;color:var(--p);}
.prod-orig{font-size:.75rem;color:#94a3b8;text-decoration:line-through;}
.add-btn{width:100%;padding:9px;background:var(--p);color:#fff;border:none;border-radius:10px;font-family:'Inter',sans-serif;font-weight:700;font-size:.8rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;transition:background .2s,transform .15s;}
.add-btn:hover{background:var(--s);}
.add-btn:active{transform:scale(.95);}
.add-btn:disabled{background:#cbd5e1;color:#94a3b8;cursor:not-allowed;}

/* Stagger animation */
@keyframes fadeSlideUp{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.prod-card{animation:fadeSlideUp .5s ease both;}

/* Empty */
.empty-state{text-align:center;padding:50px 20px;color:#94a3b8;}
.empty-state i{font-size:3.5rem;margin-bottom:14px;display:block;color:#cbd5e1;}

/* Footer */
.footer{background:var(--s);color:#fff;padding:24px 16px 20px;}
.footer-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;}
.footer-brand{font-size:1.1rem;font-weight:800;}
.footer-tagline{font-size:.78rem;opacity:.7;margin-top:3px;}
.footer-links a{display:block;color:rgba(255,255,255,0.8);text-decoration:none;font-size:.8rem;margin-bottom:4px;}
.footer-divider{border:none;border-top:1px solid rgba(255,255,255,0.15);margin-bottom:14px;}
.footer-bottom-row{display:flex;justify-content:space-between;align-items:center;font-size:.72rem;opacity:.65;}
.footer-powered a{color:#fff;text-decoration:none;}

/* Fade up */
.fade-up{opacity:0;transform:translateY(24px);transition:opacity .5s,transform .5s;}
.fade-up.visible{opacity:1;transform:none;}
</style>
</head>
<body>
<div class="wrap">

  <!-- Navbar -->
  <div class="navbar">
    <div class="nav-brand">
      <?php if($logo): ?><img src="<?= $logo ?>" alt="logo" class="nav-logo"><?php else: ?><div class="nav-logo-ph"><?= mb_substr($shopName,0,1) ?></div><?php endif; ?>
      <span class="nav-name"><?= $shopName ?></span>
    </div>
    <div class="nav-actions">
      <?php if($showSearch): ?>
      <button class="nav-icon-btn" id="searchToggle" onclick="toggleSearch()" title="Search"><i class="fas fa-search"></i></button>
      <?php endif; ?>
      <?php if($waPhone): ?>
      <a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="nav-icon-btn" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Search bar (collapsible) -->
  <?php if($showSearch): ?>
  <div class="search-bar" id="searchBarWrap">
    <div class="search-inner">
      <i class="fas fa-search"></i>
      <input type="text" id="searchInput" placeholder="Search products…" oninput="searchProducts()">
    </div>
  </div>
  <?php endif; ?>

  <!-- Hero -->
  <div class="hero">
    <?php if($bannerImg): ?>
      <img src="<?= $bannerImg ?>" alt="<?= $shopName ?>">
    <?php else: ?>
      <div class="hero-gradient">
        <h2><?= $shopName ?></h2>
        <?php if($shopTagline): ?><p><?= $shopTagline ?></p><?php endif; ?>
      </div>
    <?php endif; ?>
    <?php if($bannerImg): ?><div class="hero-overlay"></div><?php endif; ?>
  </div>

  <!-- Stats -->
  <div class="stats-row fade-up">
    <span class="stat-pill"><i class="fas fa-box-open"></i><?= count($products) ?> Products</span>
    <?php if($codAvail): ?><span class="stat-pill primary"><i class="fas fa-money-bill-wave"></i>COD</span><?php endif; ?>
    <?php if($deliveryFee > 0): ?><span class="stat-pill"><i class="fas fa-truck"></i>Delivery <?= $currency . number_format($deliveryFee,2) ?></span><?php endif; ?>
    <?php if($minOrder > 0): ?><span class="stat-pill"><i class="fas fa-shopping-bag"></i>Min <?= $currency . number_format($minOrder,2) ?></span><?php endif; ?>
  </div>

  <!-- Description -->
  <?php if($shopDesc): ?>
  <div class="desc-box fade-up"><i class="fas fa-quote-left"></i><p><?= htmlspecialchars($shopDesc) ?></p></div>
  <?php endif; ?>

  <!-- Categories -->
  <?php if($showCats): ?>
  <div class="sec-label fade-up">Categories</div>
  <div class="cat-scroll fade-up">
    <button class="cat-btn active" onclick="filterByCategory('all',this)">All</button>
    <?php foreach($categories as $c): ?>
    <button class="cat-btn" onclick="filterByCategory('<?= $c['id'] ?>',this)"><?= htmlspecialchars($c['name']) ?></button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Products -->
  <div class="sec-label fade-up">Products</div>
  <div class="prod-section">
    <?php if(!empty($products)): ?>
    <div class="prod-grid">
      <?php foreach($products as $i => $p): ?>
      <div class="prod-card<?= !empty($p['is_featured']) ? ' featured' : '' ?> fade-up" data-cat="<?= $p['category_id'] ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>" style="animation-delay:<?= $i * 0.05 ?>s;transition-delay:<?= $i * 0.05 ?>s">
        <div class="prod-img-wrap">
          <?php if($p['img']): ?><img src="<?= $p['img'] ?>" alt="<?= htmlspecialchars($p['name']) ?>"><?php else: ?><div class="prod-img-ph"><i class="fas fa-shopping-bag"></i></div><?php endif; ?>
          <?php if($p['has_discount']): ?><span class="bdg-disc">-<?= $p['disc_pct'] ?>%</span><?php endif; ?>
          <?php if(!empty($p['is_featured'])): ?><span class="bdg-feat"><i class="fas fa-star"></i> Featured</span><?php endif; ?>
          <?php if(!$p['in_stock']): ?><div class="oos-layer"><span class="oos-badge">OUT OF STOCK</span></div><?php endif; ?>
        </div>
        <div class="prod-body">
          <div class="prod-name"><?= htmlspecialchars($p['name']) ?></div>
          <div class="prod-prices">
            <span class="prod-price"><?= $currency . number_format($p['effective'],2) ?></span>
            <?php if($p['has_discount']): ?><span class="prod-orig"><?= $currency . number_format($p['price'],2) ?></span><?php endif; ?>
          </div>
          <?php if($p['in_stock']): ?>
          <button class="add-btn" onclick="addToCart(<?= $p['id'] ?>,'<?= htmlspecialchars(addslashes($p['name'])) ?>',<?= $p['effective'] ?>,'<?= htmlspecialchars($p['img'] ?? '') ?>')">
            <i class="fas fa-cart-plus"></i> Add to Cart
          </button>
          <?php else: ?>
          <button class="add-btn" disabled><i class="fas fa-ban"></i> Out of Stock</button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state fade-up">
      <i class="fas fa-store-slash"></i>
      <p style="font-size:1rem;font-weight:600;color:#64748b;">No products yet</p>
      <p style="font-size:.82rem;margin-top:4px;">Check back soon!</p>
    </div>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <div class="footer">
    <div class="footer-top">
      <div>
        <div class="footer-brand"><?= $shopName ?></div>
        <?php if($shopTagline): ?><div class="footer-tagline"><?= $shopTagline ?></div><?php endif; ?>
      </div>
      <div class="footer-links">
        <?php if($phone): ?><a href="tel:<?= $phone ?>"><i class="fas fa-phone"></i> <?= $phone ?></a><?php endif; ?>
        <?php if($email): ?><a href="mailto:<?= $email ?>"><i class="fas fa-envelope"></i> <?= $email ?></a><?php endif; ?>
        <?php if($waPhone): ?><a href="https://wa.me/<?= $waPhone ?>" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a><?php endif; ?>
      </div>
    </div>
    <?php if($address): ?><p style="font-size:.78rem;opacity:.75;margin-bottom:14px;"><i class="fas fa-map-marker-alt"></i> <?= $address ?></p><?php endif; ?>
    <hr class="footer-divider">
    <div class="footer-bottom-row">
      <div>
        <p>Powered by <a href="/">Tapify</a></p>
        <p>A unit of <strong>Mr Print World</strong></p>
      </div>
      <?php if($codAvail): ?><span style="background:rgba(255,255,255,0.15);padding:4px 10px;border-radius:50px;font-size:.7rem;font-weight:700;">COD Available</span><?php endif; ?>
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
    fetch('/store-order-submit.php',{method:'POST',headers:{'Content-Type':'application/json'},
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
function toggleSearch() {
    const bar = document.getElementById('searchBarWrap');
    if(bar) bar.classList.toggle('open');
}
const obs=new IntersectionObserver(entries=>entries.forEach(e=>{
    if(e.isIntersecting){e.target.classList.add('visible');obs.unobserve(e.target);}
}),{threshold:0.1});
document.querySelectorAll('.fade-up').forEach(el=>obs.observe(el));
</script>
</body>
</html>
