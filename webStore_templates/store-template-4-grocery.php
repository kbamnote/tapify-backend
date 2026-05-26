<?php
/* ================================================================
   Tapify Store Template 4 — Grocery & Supermarket
   Fresh white/green, Nunito, 2-col grid with qty steppers
   ================================================================ */
$shopName    = htmlspecialchars($store['store_name'] ?? 'Grocery Store');
$shopTagline = htmlspecialchars($store['tagline'] ?? '');
$shopDesc    = $store['description'] ?? '';
$logo        = !empty($store['logo_image'])  ? imgUrl($store['logo_image'])  : '';
$bannerImg   = !empty($store['cover_image']) ? imgUrl($store['cover_image']) : '';
$waPhone     = preg_replace('/\D/', '', $store['whatsapp_number'] ?? '');
$phone       = htmlspecialchars($store['phone'] ?? '');
$email       = htmlspecialchars($store['email'] ?? '');
$address     = htmlspecialchars($store['address'] ?? '');
$primary     = $store['primary_color'] ?? '#16a34a';
$secondary   = $store['secondary_color'] ?? '#15803d';
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
    $p['disc_pct']     = $p['has_discount'] ? round((1 - $p['discount_price'] / $p['price']) * 100) : 0;
} unset($p);
foreach ($categories as &$c) {
    $c['img'] = !empty($c['image']) ? imgUrl($c['image']) : (!empty($c['image_url']) ? imgUrl($c['image_url']) : '');
} unset($c);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $shopName ?></title>
<link rel="icon" href="<?= $logo ?: '/images/tapify-logo-green.png' ?>">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
:root{--p:<?= $primary ?>;--s:<?= $secondary ?>;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Nunito',sans-serif;background:#f0fdf4;color:#14532d;overflow-x:hidden;}
.wrap{max-width:480px;margin:0 auto;background:#fff;min-height:100vh;box-shadow:0 0 60px rgba(0,0,0,.1);position:relative;padding-bottom:100px;}
/* TOP NAV */
.top-nav{position:sticky;top:0;z-index:100;background:#fff;display:flex;align-items:center;justify-content:space-between;padding:10px 14px;box-shadow:0 2px 10px rgba(22,163,74,.1);}
.nav-left{display:flex;align-items:center;gap:10px;}
.nav-logo{width:42px;height:42px;border-radius:50%;object-fit:cover;border:2px solid var(--p);}
.nav-logo-ph{width:42px;height:42px;border-radius:50%;background:var(--p);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;}
.nav-name{font-weight:800;font-size:1rem;color:#14532d;}
.nav-tag{font-size:.7rem;color:#6b7280;font-weight:400;}
.nav-right{display:flex;align-items:center;gap:8px;}
.nav-wa{width:36px;height:36px;border-radius:50%;background:#25D366;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;text-decoration:none;}
/* SEARCH */
.search-bar{padding:10px 14px;background:#fff;}
.search-inner{display:flex;align-items:center;gap:8px;background:#f0fdf4;border:2px solid #bbf7d0;border-radius:14px;padding:10px 14px;}
.search-inner i{color:var(--p);font-size:.9rem;}
.search-inner input{border:none;background:transparent;flex:1;font-family:'Nunito',sans-serif;font-size:.92rem;color:#14532d;outline:none;font-weight:600;}
.search-inner input::placeholder{font-weight:400;color:#9ca3af;}
/* HERO BANNER */
.hero-banner{height:160px;margin:0 14px;border-radius:18px;overflow:hidden;background:linear-gradient(135deg,var(--p),var(--s));position:relative;}
.hero-banner img{width:100%;height:100%;object-fit:cover;}
.hero-overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(21,128,61,.7),rgba(22,163,74,.3));}
.hero-text{position:absolute;inset:0;display:flex;flex-direction:column;justify-content:center;padding:20px;}
.hero-title{font-size:1.4rem;font-weight:800;color:#fff;line-height:1.2;margin-bottom:4px;}
.hero-sub{font-size:.8rem;color:rgba(255,255,255,.8);font-weight:600;}
.hero-badge{position:absolute;top:12px;right:12px;background:rgba(255,255,255,.9);color:var(--p);padding:5px 12px;border-radius:50px;font-size:.7rem;font-weight:800;text-transform:uppercase;}
/* STATS */
.stats-row{display:flex;gap:8px;padding:12px 14px;}
.stat-chip{flex:1;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:8px 6px;text-align:center;}
.stat-val{font-size:.88rem;font-weight:800;color:var(--p);}
.stat-lbl{font-size:.62rem;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:.3px;}
/* CATEGORIES */
.cat-section{padding:12px 14px 6px;}
.sec-hd{font-size:1rem;font-weight:800;color:#14532d;margin-bottom:10px;display:flex;align-items:center;gap:8px;}
.sec-hd i{color:var(--p);}
.cat-scroll{display:flex;gap:8px;overflow-x:auto;padding:4px 0 8px;scrollbar-width:none;}
.cat-scroll::-webkit-scrollbar{display:none;}
.cat-btn{flex-shrink:0;display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:50px;border:2px solid #bbf7d0;background:#f0fdf4;color:#14532d;font-family:'Nunito',sans-serif;font-size:.82rem;font-weight:700;cursor:pointer;transition:all .2s;white-space:nowrap;}
.cat-btn .cat-icon{width:24px;height:24px;border-radius:50%;overflow:hidden;background:#d1fae5;display:flex;align-items:center;justify-content:center;}
.cat-btn .cat-icon img{width:100%;height:100%;object-fit:cover;}
.cat-btn.active,.cat-btn:hover{background:var(--p);border-color:var(--p);color:#fff;}
.cat-btn.active .cat-icon,.cat-btn:hover .cat-icon{background:rgba(255,255,255,.2);}
/* PRODUCTS GRID */
.prod-section{padding:12px 14px 20px;}
.prod-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;}
.prod-card{background:#fff;border-radius:16px;overflow:hidden;border:1.5px solid #f0fdf4;transition:all .3s;box-shadow:0 2px 8px rgba(22,163,74,.06);}
.prod-card:hover{border-color:#86efac;box-shadow:0 8px 24px rgba(22,163,74,.15);transform:translateY(-3px);}
.p-img-wrap{position:relative;aspect-ratio:1;overflow:hidden;background:#f0fdf4;}
.p-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .4s;}
.prod-card:hover .p-img-wrap img{transform:scale(1.06);}
.p-no-img{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;}
.p-badge{position:absolute;padding:3px 8px;border-radius:50px;font-size:.6rem;font-weight:800;text-transform:uppercase;}
.p-badge-disc{top:7px;right:7px;background:#ef4444;color:#fff;}
.p-badge-feat{top:7px;left:7px;background:#f59e0b;color:#fff;}
.p-badge-oos{inset:0;position:absolute;background:rgba(255,255,255,.8);display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:800;color:#9ca3af;letter-spacing:1px;}
.p-body{padding:10px 10px 12px;}
.p-name{font-weight:800;font-size:.85rem;line-height:1.3;margin-bottom:3px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;height:2.2em;}
.p-desc{font-size:.7rem;color:#9ca3af;font-weight:600;margin-bottom:7px;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;}
.p-price-row{display:flex;align-items:center;gap:5px;margin-bottom:8px;}
.p-price{font-weight:800;font-size:.98rem;color:var(--p);}
.p-orig{font-size:.72rem;color:#d1d5db;text-decoration:line-through;}
.add-btn{width:100%;padding:9px;border:none;border-radius:10px;background:var(--p);color:#fff;font-family:'Nunito',sans-serif;font-size:.82rem;font-weight:800;cursor:pointer;transition:all .25s;display:flex;align-items:center;justify-content:center;gap:5px;}
.add-btn:hover{background:var(--s);}
.add-btn:disabled{background:#e5e7eb;color:#9ca3af;cursor:not-allowed;}
/* FOOTER */
.store-footer{background:#f0fdf4;padding:22px 18px;text-align:center;border-top:2px dashed #bbf7d0;margin-top:10px;}
.footer-name{font-size:1.05rem;font-weight:800;color:#14532d;margin-bottom:10px;}
.footer-links a{color:#6b7280;font-size:.82rem;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:5px;margin-bottom:5px;}
.footer-links a:hover{color:var(--p);}
.footer-copy{font-size:.7rem;color:#9ca3af;margin-top:8px;}
/* FADE-UP */
.fade-up{opacity:0;transform:translateY(20px);transition:opacity .5s,transform .5s;}
.fade-up.visible{opacity:1;transform:none;}
/* CART */
#cartOverlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:900;}
#cartDrawer{position:fixed;bottom:0;left:50%;transform:translateX(-50%) translateY(100%);width:100%;max-width:480px;background:#fff;border-radius:20px 20px 0 0;z-index:901;transition:transform .35s cubic-bezier(.32,0,.67,0);max-height:87vh;overflow-y:auto;box-shadow:0 -8px 40px rgba(0,0,0,.15);}
#cartDrawer.open{transform:translateX(-50%) translateY(0);}
.d-handle{width:40px;height:4px;background:#e5e7eb;border-radius:2px;margin:12px auto 16px;}
.d-header{display:flex;justify-content:space-between;align-items:center;padding:0 20px 14px;border-bottom:1px solid #f0fdf4;}
.d-title{font-size:1.05rem;font-weight:800;color:#14532d;}
.d-close{background:none;border:none;font-size:1.5rem;cursor:pointer;color:#9ca3af;line-height:1;}
.c-item{display:flex;gap:12px;padding:12px 20px;border-bottom:1px solid #f9fafb;}
.c-img{width:54px;height:54px;border-radius:10px;overflow:hidden;background:#f0fdf4;flex-shrink:0;display:flex;align-items:center;justify-content:center;color:#9ca3af;}
.c-img img{width:100%;height:100%;object-fit:cover;}
.c-info{flex:1;}
.c-name{font-size:.86rem;font-weight:700;margin-bottom:2px;color:#14532d;}
.c-price{font-weight:800;color:var(--p);font-size:.92rem;margin-bottom:6px;}
.c-qty-row{display:flex;align-items:center;gap:8px;}
.c-qty-btn{width:28px;height:28px;border-radius:50%;border:2px solid #bbf7d0;background:#f0fdf4;cursor:pointer;font-size:.9rem;font-weight:800;color:var(--p);display:flex;align-items:center;justify-content:center;transition:all .2s;}
.c-qty-btn:hover{background:var(--p);border-color:var(--p);color:#fff;}
.c-qty-val{font-weight:800;min-width:20px;text-align:center;color:#14532d;}
.c-del{border-color:#fecdd3;color:#f43f5e;background:#fff0f0;}
.c-del:hover{background:#f43f5e;border-color:#f43f5e;color:#fff;}
#cartSummary{padding:12px 20px;}
.c-sum-row{display:flex;justify-content:space-between;padding:5px 0;font-size:.88rem;color:#4b5563;}
.c-total{font-weight:800;font-size:1rem;padding-top:10px;border-top:2px dashed #bbf7d0;margin-top:6px;color:#14532d;}
.c-min-warn{color:#ef4444;font-size:.75rem;text-align:center;margin-top:6px;}
#checkoutSection{padding:16px 20px 28px;}
#checkoutSection h4{font-size:.95rem;font-weight:800;color:#14532d;margin-bottom:14px;}
.ck-field{margin-bottom:10px;}
.ck-field label{display:block;font-size:.72rem;font-weight:700;margin-bottom:4px;color:#9ca3af;text-transform:uppercase;letter-spacing:.3px;}
.ck-field input,.ck-field textarea{width:100%;padding:10px 14px;border:2px solid #bbf7d0;border-radius:10px;font-family:'Nunito',sans-serif;font-size:.88rem;outline:none;transition:border-color .2s;}
.ck-field input:focus,.ck-field textarea:focus{border-color:var(--p);}
.ck-btn{width:100%;padding:14px;border:none;border-radius:12px;background:linear-gradient(135deg,var(--p),var(--s));color:#fff;font-family:'Nunito',sans-serif;font-weight:800;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;}
#floatCart{display:none;position:fixed;bottom:22px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,var(--p),var(--s));color:#fff;padding:14px 28px;border-radius:50px;cursor:pointer;z-index:850;align-items:center;gap:12px;box-shadow:0 8px 30px rgba(22,163,74,.4);max-width:92vw;font-weight:800;white-space:nowrap;}
#cartBubble{background:#fff;color:var(--p);width:26px;height:26px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:800;}
.tap-toast{position:fixed;top:20px;left:50%;transform:translateX(-50%) translateY(-80px);background:#14532d;color:#fff;padding:10px 22px;border-radius:50px;font-weight:700;font-size:.85rem;z-index:9999;transition:transform .35s;white-space:nowrap;max-width:90vw;}
.tap-toast.show{transform:translateX(-50%) translateY(0);}
.tap-toast.error{background:#ef4444;}.tap-toast.success{background:#10b981;}
</style>
</head>
<body>
<div class="wrap">

<!-- NAV -->
<nav class="top-nav">
  <div class="nav-left">
    <?php if ($logo): ?><img src="<?= $logo ?>" class="nav-logo" alt=""><?php else: ?><div class="nav-logo-ph"><?= strtoupper(substr($shopName,0,1)) ?></div><?php endif; ?>
    <div><div class="nav-name"><?= $shopName ?></div><?php if ($shopTagline): ?><div class="nav-tag"><?= $shopTagline ?></div><?php endif; ?></div>
  </div>
  <div class="nav-right">
    <a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="nav-wa"><i class="fab fa-whatsapp"></i></a>
  </div>
</nav>

<!-- SEARCH -->
<div class="search-bar">
  <div class="search-inner"><i class="fas fa-search"></i><input type="text" id="searchInput" placeholder="Search groceries..." oninput="searchProducts()"></div>
</div>

<!-- HERO -->
<div class="hero-banner fade-up">
  <?php if ($bannerImg): ?><img src="<?= $bannerImg ?>" alt=""><?php endif; ?>
  <div class="hero-overlay"></div>
  <div class="hero-text">
    <div class="hero-title"><?= $shopName ?></div>
    <?php if ($shopTagline): ?><div class="hero-sub"><?= $shopTagline ?></div><?php endif; ?>
  </div>
  <?php if ($codAvail): ?><div class="hero-badge">✓ COD Available</div><?php endif; ?>
</div>

<!-- STATS -->
<div class="stats-row fade-up">
  <div class="stat-chip"><div class="stat-val"><?= number_format((int)$store['view_count']) ?></div><div class="stat-lbl">Views</div></div>
  <div class="stat-chip"><div class="stat-val"><?= count($products) ?></div><div class="stat-lbl">Products</div></div>
  <div class="stat-chip"><div class="stat-val"><?= $currency ?><?= $deliveryFee > 0 ? number_format($deliveryFee,0) : 'Free' ?></div><div class="stat-lbl">Delivery</div></div>
</div>

<?php if ($shopDesc): ?><div style="margin:4px 14px 12px;padding:12px 16px;background:#f0fdf4;border-radius:12px;font-size:.85rem;color:#4b5563;line-height:1.6;border-left:3px solid var(--p);" class="fade-up"><?= htmlspecialchars($shopDesc) ?></div><?php endif; ?>

<!-- CATEGORIES -->
<?php if ($showCats): ?>
<div class="cat-section fade-up">
  <div class="sec-hd"><i class="fas fa-th-large"></i> Shop by Category</div>
  <div class="cat-scroll">
    <button class="cat-btn active" onclick="filterByCategory('all',this)"><span>🛒 All</span></button>
    <?php foreach ($categories as $c): ?>
    <button class="cat-btn" onclick="filterByCategory('<?= $c['id'] ?>',this)">
      <?php if ($c['img']): ?><div class="cat-icon"><img src="<?= htmlspecialchars($c['img']) ?>" alt=""></div><?php endif; ?>
      <span><?= htmlspecialchars($c['name']) ?></span>
    </button>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- PRODUCTS -->
<div class="prod-section">
  <div class="sec-hd" style="margin-bottom:12px;"><i class="fas fa-leaf"></i> Fresh Products</div>
  <?php if (empty($products)): ?>
  <div style="text-align:center;padding:48px 20px;color:#9ca3af;"><i class="fas fa-shopping-basket" style="font-size:3rem;margin-bottom:12px;display:block;opacity:.3;"></i><p>No products yet!</p></div>
  <?php else: ?>
  <div class="prod-grid">
    <?php foreach ($products as $idx => $p): ?>
    <div class="prod-card fade-up" data-cat="<?= htmlspecialchars($p['category_id'] ?? 'none') ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>" style="transition-delay:<?= ($idx % 6) * 0.07 ?>s">
      <div class="p-img-wrap">
        <?php if ($p['img']): ?><img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"><?php else: ?><div class="p-no-img">🥬</div><?php endif; ?>
        <?php if ($p['has_discount']): ?><span class="p-badge p-badge-disc"><?= $p['disc_pct'] ?>% OFF</span><?php endif; ?>
        <?php if ($p['is_featured'] && !$p['has_discount']): ?><span class="p-badge p-badge-feat">★ Fresh</span><?php endif; ?>
        <?php if (!$p['in_stock']): ?><div class="p-badge p-badge-oos">OUT OF STOCK</div><?php endif; ?>
      </div>
      <div class="p-body">
        <div class="p-name"><?= htmlspecialchars($p['name']) ?></div>
        <?php if (!empty($p['description'])): ?><div class="p-desc"><?= htmlspecialchars($p['description']) ?></div><?php endif; ?>
        <div class="p-price-row">
          <span class="p-price"><?= $currency ?><?= number_format($p['effective'], 2) ?></span>
          <?php if ($p['has_discount']): ?><span class="p-orig"><?= $currency ?><?= number_format((float)$p['price'], 2) ?></span><?php endif; ?>
        </div>
        <button class="add-btn" <?= !$p['in_stock'] ? 'disabled' : '' ?>
          onclick="addToCart(<?= $p['id'] ?>,'<?= htmlspecialchars(addslashes($p['name'])) ?>',<?= $p['effective'] ?>,'<?= htmlspecialchars($p['img'] ?? '') ?>')">
          <i class="fas fa-<?= $p['in_stock'] ? 'plus' : 'clock' ?>"></i> <?= $p['in_stock'] ? 'Add' : 'Notify Me' ?>
        </button>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- FOOTER -->
<footer class="store-footer fade-up">
  <div class="footer-name"><?= $shopName ?></div>
  <div class="footer-links">
    <?php if ($phone): ?><a href="tel:<?= preg_replace('/\D/','',$phone) ?>"><i class="fas fa-phone" style="color:var(--p)"></i> <?= $phone ?></a><?php endif; ?>
    <?php if ($email): ?><a href="mailto:<?= $email ?>"><i class="fas fa-envelope" style="color:var(--p)"></i> <?= $email ?></a><?php endif; ?>
    <?php if ($address): ?><a href="#"><i class="fas fa-map-marker-alt" style="color:var(--p)"></i> <?= $address ?></a><?php endif; ?>
    <a href="https://wa.me/<?= $waPhone ?>" target="_blank"><i class="fab fa-whatsapp" style="color:#25D366"></i> Order on WhatsApp</a>
  </div>
  <p style="font-size:.75rem;color:#9ca3af;">Powered by <a href="/" style="color:var(--p);font-weight:700;">Tapify</a> · A unit of <strong>Mr Print World</strong></p>
  <p class="footer-copy">© <?= date('Y') ?> <?= $shopName ?></p>
</footer>
</div>

<div id="cartOverlay" onclick="closeCart()"></div>
<div id="cartDrawer">
  <div class="d-handle"></div>
  <div class="d-header"><span class="d-title">🛒 My Cart</span><button class="d-close" onclick="closeCart()">×</button></div>
  <div id="cartList"></div>
  <div id="cartSummary"></div>
  <form id="checkoutSection" onsubmit="placeOrder(event)" style="display:none;">
    <h4>Delivery Details</h4>
    <input type="hidden" name="store_id" value="<?= $storeId ?>">
    <div class="ck-field"><label>Name *</label><input name="name" required placeholder="Your full name"></div>
    <div class="ck-field"><label>Phone *</label><input name="phone" type="tel" required placeholder="WhatsApp number"></div>
    <div class="ck-field"><label>Delivery Address</label><textarea name="address" rows="2" placeholder="House, street, city, pincode..."></textarea></div>
    <div class="ck-field"><label>Notes</label><textarea name="notes" rows="2" placeholder="Substitutions, timing preferences..."></textarea></div>
    <button type="submit" class="ck-btn"><i class="fab fa-whatsapp"></i> Place Order on WhatsApp</button>
  </form>
</div>
<div id="floatCart" onclick="openCart()">
  <span id="cartBubble">0</span><span>My Cart</span><span id="floatTotal"><?= $currency ?>0</span>
</div>
<script>
const STORE_DATA={id:<?= $storeId ?>,whatsapp:'<?= $waPhone ?>',currency:'<?= $currency ?>',deliveryFee:<?= $deliveryFee ?>,minOrder:<?= $minOrder ?>,template:<?= json_encode($waTemplate) ?>};
let cart=[];
function addToCart(id,name,price,img){const ex=cart.find(i=>i.id===id);if(ex){ex.qty++;}else{cart.push({id,name,price,img,qty:1});}updateCartUI();showToast('✓ '+name+' added!');}
function removeFromCart(id){cart=cart.filter(i=>i.id!==id);updateCartUI();renderCartDrawer();}
function changeQty(id,d){const it=cart.find(i=>i.id===id);if(!it)return;it.qty=Math.max(1,it.qty+d);updateCartUI();renderCartDrawer();}
function getSub(){return cart.reduce((s,i)=>s+i.qty*i.price,0);}
function getCount(){return cart.reduce((s,i)=>s+i.qty,0);}
function updateCartUI(){const c=getCount(),s=getSub(),t=s+STORE_DATA.deliveryFee;document.getElementById('cartBubble').textContent=c;document.getElementById('floatTotal').textContent=STORE_DATA.currency+t.toFixed(2);document.getElementById('floatCart').style.display=c>0?'flex':'none';}
function openCart(){if(!cart.length){showToast('Cart is empty','error');return;}renderCartDrawer();document.getElementById('cartDrawer').classList.add('open');document.getElementById('cartOverlay').style.display='block';}
function closeCart(){document.getElementById('cartDrawer').classList.remove('open');document.getElementById('cartOverlay').style.display='none';}
function renderCartDrawer(){const s=getSub(),t=s+STORE_DATA.deliveryFee;document.getElementById('cartList').innerHTML=cart.map(it=>`<div class="c-item"><div class="c-img">${it.img?`<img src="${it.img}" alt="">`:''}</div><div class="c-info"><div class="c-name">${it.name}</div><div class="c-price">${STORE_DATA.currency}${it.price.toFixed(2)}</div><div class="c-qty-row"><button class="c-qty-btn" onclick="changeQty(${it.id},-1)">−</button><span class="c-qty-val">${it.qty}</span><button class="c-qty-btn" onclick="changeQty(${it.id},1)">+</button><button class="c-qty-btn c-del" onclick="removeFromCart(${it.id})"><i class="fas fa-trash-alt"></i></button></div></div></div>`).join('');document.getElementById('cartSummary').innerHTML=`<div class="c-sum-row"><span>Subtotal</span><span>${STORE_DATA.currency}${s.toFixed(2)}</span></div>${STORE_DATA.deliveryFee>0?`<div class="c-sum-row"><span>Delivery</span><span>${STORE_DATA.currency}${STORE_DATA.deliveryFee.toFixed(2)}</span></div>`:''}<div class="c-sum-row c-total"><span>Total</span><span>${STORE_DATA.currency}${t.toFixed(2)}</span></div>${s<STORE_DATA.minOrder&&STORE_DATA.minOrder>0?`<p class="c-min-warn">Min order: ${STORE_DATA.currency}${STORE_DATA.minOrder.toFixed(2)}</p>`:''}`;document.getElementById('checkoutSection').style.display=(s>=STORE_DATA.minOrder||STORE_DATA.minOrder===0)?'block':'none';}
function placeOrder(e){e.preventDefault();const d=Object.fromEntries(new FormData(e.target));const s=getSub(),t=s+STORE_DATA.deliveryFee;let items=cart.map(i=>`• ${i.name} x${i.qty} = ${STORE_DATA.currency}${(i.qty*i.price).toFixed(2)}`).join('\n');let msg=(STORE_DATA.template||'🛒 NEW ORDER\n\nName: {customer_name}\nPhone: {customer_phone}\n\n{items}\n\nTotal: {total}').replace('{customer_name}',d.name).replace('{customer_phone}',d.phone).replace('{items}',items).replace('{total}',STORE_DATA.currency+t.toFixed(2));if(d.address)msg+='\nAddress: '+d.address;if(d.notes)msg+='\nNotes: '+d.notes;fetch('/backend/store-order-submit.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({store_id:STORE_DATA.id,customer_name:d.name,customer_phone:d.phone,customer_address:d.address||'',items:cart,subtotal:s,delivery_charge:STORE_DATA.deliveryFee,total_amount:t,notes:d.notes||''})}).catch(()=>{});window.location.href=`https://wa.me/${STORE_DATA.whatsapp}?text=${encodeURIComponent(msg)}`;}
function filterByCategory(catId,el){document.querySelectorAll('.cat-btn').forEach(b=>b.classList.remove('active'));if(el)el.classList.add('active');document.querySelectorAll('.prod-card').forEach(c=>{c.style.display=(catId==='all'||c.dataset.cat==catId)?'':'none';});}
function searchProducts(){const q=(document.getElementById('searchInput')?.value||'').toLowerCase();document.querySelectorAll('.prod-card').forEach(c=>{c.style.display=c.dataset.name.includes(q)?'':'none';});}
function showToast(msg,type='success'){const t=document.createElement('div');t.className='tap-toast '+type;t.textContent=msg;document.body.appendChild(t);setTimeout(()=>t.classList.add('show'),10);setTimeout(()=>{t.classList.remove('show');setTimeout(()=>t.remove(),400);},2500);}
const obs=new IntersectionObserver(e=>e.forEach(en=>{if(en.isIntersecting){en.target.classList.add('visible');obs.unobserve(en.target);}}),{threshold:0.1});
document.querySelectorAll('.fade-up').forEach(el=>obs.observe(el));
</script>
</body>
</html>
