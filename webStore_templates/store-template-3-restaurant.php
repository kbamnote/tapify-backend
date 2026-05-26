<?php
/* ================================================================
   Tapify Store Template 3 — Restaurant & Food
   Dark amber, DM Serif Display + DM Sans, list-style food cards
   ================================================================ */
$shopName    = htmlspecialchars($store['store_name'] ?? 'Restaurant');
$shopTagline = htmlspecialchars($store['tagline'] ?? '');
$shopDesc    = $store['description'] ?? '';
$logo        = !empty($store['logo_image'])  ? imgUrl($store['logo_image'])  : '';
$bannerImg   = !empty($store['cover_image']) ? imgUrl($store['cover_image']) : '';
$waPhone     = preg_replace('/\D/', '', $store['whatsapp_number'] ?? '');
$phone       = htmlspecialchars($store['phone'] ?? '');
$email       = htmlspecialchars($store['email'] ?? '');
$address     = htmlspecialchars($store['address'] ?? '');
$primary     = $store['primary_color'] ?? '#f97316';
$secondary   = $store['secondary_color'] ?? '#c2410c';
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
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
:root{--p:<?= $primary ?>;--s:<?= $secondary ?>;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',sans-serif;background:#0d0805;color:#f5ede4;overflow-x:hidden;}
.wrap{max-width:480px;margin:0 auto;background:#150e08;min-height:100vh;box-shadow:0 0 60px rgba(0,0,0,.5);position:relative;padding-bottom:100px;}
/* HERO */
.hero{position:relative;height:280px;overflow:hidden;}
.hero img{width:100%;height:100%;object-fit:cover;}
.hero-grad{position:absolute;inset:0;background:linear-gradient(to top,#150e08 0%,rgba(21,14,8,.6) 50%,rgba(21,14,8,.2) 100%);}
.hero-no-img{width:100%;height:100%;background:linear-gradient(135deg,var(--s) 0%,var(--p) 100%);display:flex;align-items:center;justify-content:center;font-size:5rem;}
.hero-content{position:absolute;bottom:0;left:0;right:0;padding:20px;}
.hero-logo{width:52px;height:52px;border-radius:50%;border:2px solid var(--p);object-fit:cover;margin-bottom:10px;}
.hero-logo-ph{width:52px;height:52px;border-radius:50%;background:var(--p);display:flex;align-items:center;justify-content:center;font-family:'DM Serif Display',serif;font-size:1.3rem;color:#fff;margin-bottom:10px;}
.hero-name{font-family:'DM Serif Display',serif;font-size:1.9rem;font-weight:400;color:#fff;line-height:1.15;margin-bottom:4px;}
.hero-tag{color:rgba(255,255,255,.65);font-size:.85rem;}
/* STATS BAR */
.stats-bar{display:flex;gap:0;border-bottom:1px solid rgba(255,255,255,.07);}
.stat-item{flex:1;padding:12px 8px;text-align:center;border-right:1px solid rgba(255,255,255,.07);}
.stat-item:last-child{border-right:none;}
.stat-num{font-size:.9rem;font-weight:700;color:var(--p);}
.stat-lbl{font-size:.65rem;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.5px;}
/* DESCRIPTION */
.desc-block{padding:16px 18px;background:rgba(255,255,255,.04);margin:12px 14px;border-radius:12px;border-left:3px solid var(--p);}
.desc-block p{font-size:.88rem;color:rgba(255,255,255,.7);line-height:1.65;}
/* SEARCH */
.search-wrap{padding:12px 14px 4px;}
.search-box{display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:50px;padding:10px 16px;}
.search-box i{color:var(--p);font-size:.9rem;}
.search-box input{border:none;background:transparent;flex:1;font-family:inherit;font-size:.9rem;color:#f5ede4;outline:none;}
.search-box input::placeholder{color:rgba(255,255,255,.35);}
/* CATEGORIES */
.cat-wrap{padding:16px 14px 8px;}
.sec-hd{font-family:'DM Serif Display',serif;font-size:1.1rem;color:#f5ede4;margin-bottom:12px;display:flex;align-items:center;gap:8px;}
.sec-hd i{color:var(--p);}
.cat-scroll{display:flex;gap:8px;overflow-x:auto;padding:4px 0 8px;scrollbar-width:none;}
.cat-scroll::-webkit-scrollbar{display:none;}
.cat-btn{flex-shrink:0;padding:8px 18px;border-radius:50px;border:1.5px solid rgba(255,255,255,.15);background:transparent;color:rgba(255,255,255,.65);font-family:'DM Sans',sans-serif;font-size:.82rem;font-weight:500;cursor:pointer;transition:all .25s;white-space:nowrap;}
.cat-btn.active,.cat-btn:hover{background:var(--p);border-color:var(--p);color:#fff;}
/* FOOD ITEMS */
.menu-wrap{padding:12px 14px 20px;}
.food-card{display:flex;gap:14px;padding:14px;background:rgba(255,255,255,.04);border-radius:14px;margin-bottom:10px;border:1px solid rgba(255,255,255,.06);transition:all .3s;cursor:pointer;}
.food-card:hover{background:rgba(255,255,255,.07);border-color:rgba(249,115,22,.3);transform:translateX(3px);}
.food-img-wrap{position:relative;width:90px;height:90px;border-radius:12px;overflow:hidden;flex-shrink:0;background:rgba(255,255,255,.05);}
.food-img-wrap img{width:100%;height:100%;object-fit:cover;}
.food-no-img{width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.2);font-size:2rem;}
.food-badge{position:absolute;top:5px;right:5px;padding:2px 7px;border-radius:50px;font-size:.6rem;font-weight:700;text-transform:uppercase;}
.badge-feat{background:var(--p);color:#fff;}
.badge-disc{background:#ef4444;color:#fff;}
.food-info{flex:1;display:flex;flex-direction:column;justify-content:space-between;}
.food-name{font-family:'DM Serif Display',serif;font-size:1rem;color:#f5ede4;margin-bottom:4px;line-height:1.3;}
.food-desc{font-size:.78rem;color:rgba(255,255,255,.45);line-height:1.5;margin-bottom:8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.food-bottom{display:flex;align-items:center;justify-content:space-between;gap:8px;}
.food-price{font-weight:700;font-size:1rem;color:var(--p);}
.food-orig{font-size:.75rem;color:rgba(255,255,255,.3);text-decoration:line-through;margin-left:4px;}
.food-btn{padding:8px 16px;border:none;border-radius:50px;background:var(--p);color:#fff;font-family:'DM Sans',sans-serif;font-size:.78rem;font-weight:700;cursor:pointer;transition:all .25s;white-space:nowrap;}
.food-btn:hover{background:var(--s);}
.food-btn:disabled{background:rgba(255,255,255,.1);color:rgba(255,255,255,.3);cursor:not-allowed;}
.oos-overlay{opacity:.45;}
/* FOOTER */
.store-footer{background:rgba(0,0,0,.4);padding:24px 18px;text-align:center;border-top:1px solid rgba(255,255,255,.07);margin-top:10px;}
.footer-name{font-family:'DM Serif Display',serif;font-size:1.1rem;margin-bottom:12px;color:#f5ede4;}
.footer-links a{color:rgba(255,255,255,.5);font-size:.82rem;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:6px;transition:color .2s;}
.footer-links a:hover{color:var(--p);}
.footer-copy{font-size:.72rem;color:rgba(255,255,255,.25);margin-top:10px;}
/* FADE-UP */
.fade-up{opacity:0;transform:translateY(22px);transition:opacity .5s ease,transform .5s ease;}
.fade-up.visible{opacity:1;transform:none;}
/* CART */
#cartOverlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:900;}
#cartDrawer{position:fixed;bottom:0;left:50%;transform:translateX(-50%) translateY(100%);width:100%;max-width:480px;background:#1e140a;border-radius:20px 20px 0 0;z-index:901;transition:transform .35s cubic-bezier(.32,0,.67,0);max-height:87vh;overflow-y:auto;box-shadow:0 -8px 40px rgba(0,0,0,.5);}
#cartDrawer.open{transform:translateX(-50%) translateY(0);}
.d-handle{width:40px;height:4px;background:rgba(255,255,255,.15);border-radius:2px;margin:12px auto 16px;}
.d-header{display:flex;justify-content:space-between;align-items:center;padding:0 20px 14px;border-bottom:1px solid rgba(255,255,255,.08);}
.d-title{font-family:'DM Serif Display',serif;font-size:1.1rem;color:#f5ede4;}
.d-close{background:none;border:none;font-size:1.5rem;cursor:pointer;color:rgba(255,255,255,.4);line-height:1;}
.c-item{display:flex;gap:12px;padding:12px 20px;border-bottom:1px solid rgba(255,255,255,.05);}
.c-img{width:56px;height:56px;border-radius:10px;overflow:hidden;background:rgba(255,255,255,.05);flex-shrink:0;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.2);}
.c-img img{width:100%;height:100%;object-fit:cover;}
.c-info{flex:1;}.c-name{font-size:.86rem;font-weight:600;margin-bottom:2px;color:#f5ede4;}
.c-price{font-weight:700;color:var(--p);font-size:.92rem;margin-bottom:6px;}
.c-qty-row{display:flex;align-items:center;gap:8px;}
.c-qty-btn{width:28px;height:28px;border-radius:50%;border:1.5px solid rgba(255,255,255,.15);background:transparent;cursor:pointer;font-size:.9rem;color:#f5ede4;display:flex;align-items:center;justify-content:center;transition:all .2s;}
.c-qty-btn:hover{background:var(--p);border-color:var(--p);}
.c-qty-val{font-weight:700;min-width:20px;text-align:center;color:#f5ede4;}
.c-del:hover{background:#ef4444;border-color:#ef4444;}
#cartSummary{padding:12px 20px;}
.c-sum-row{display:flex;justify-content:space-between;padding:5px 0;font-size:.88rem;color:rgba(255,255,255,.7);}
.c-total{font-weight:800;font-size:1rem;padding-top:10px;border-top:1px dashed rgba(255,255,255,.15);margin-top:6px;color:#f5ede4;}
.c-min-warn{color:#ef4444;font-size:.75rem;text-align:center;margin-top:6px;}
#checkoutSection{padding:16px 20px 28px;}
#checkoutSection h4{font-family:'DM Serif Display',serif;font-size:1rem;color:#f5ede4;margin-bottom:14px;}
.ck-field{margin-bottom:10px;}
.ck-field label{display:block;font-size:.75rem;font-weight:600;margin-bottom:4px;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.4px;}
.ck-field input,.ck-field textarea{width:100%;padding:10px 14px;border:1.5px solid rgba(255,255,255,.12);border-radius:10px;font-family:inherit;font-size:.88rem;background:rgba(255,255,255,.05);color:#f5ede4;outline:none;transition:border-color .2s;}
.ck-field input:focus,.ck-field textarea:focus{border-color:var(--p);}
.ck-btn{width:100%;padding:14px;border:none;border-radius:12px;background:linear-gradient(135deg,var(--p),var(--s));color:#fff;font-family:'DM Sans',sans-serif;font-weight:700;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;}
#floatCart{display:none;position:fixed;bottom:22px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,var(--p),var(--s));color:#fff;padding:14px 28px;border-radius:50px;cursor:pointer;z-index:850;align-items:center;gap:12px;box-shadow:0 8px 30px rgba(249,115,22,.4);max-width:92vw;font-weight:700;white-space:nowrap;}
#cartBubble{background:#fff;color:var(--p);width:26px;height:26px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:800;}
.tap-toast{position:fixed;top:20px;left:50%;transform:translateX(-50%) translateY(-80px);background:#1e140a;color:#f5ede4;padding:10px 22px;border-radius:50px;font-weight:700;font-size:.85rem;z-index:9999;transition:transform .35s;white-space:nowrap;max-width:90vw;border:1px solid rgba(255,255,255,.1);}
.tap-toast.show{transform:translateX(-50%) translateY(0);}
.tap-toast.error{background:#7f1d1d;}.tap-toast.success{background:#14532d;}
</style>
</head>
<body>
<div class="wrap">

<!-- HERO -->
<div class="hero">
  <?php if ($bannerImg): ?><img src="<?= $bannerImg ?>" alt="Banner"><?php else: ?><div class="hero-no-img">🍽️</div><?php endif; ?>
  <div class="hero-grad"></div>
  <div class="hero-content">
    <?php if ($logo): ?><img src="<?= $logo ?>" class="hero-logo" alt="Logo"><?php else: ?><div class="hero-logo-ph"><?= strtoupper(substr($shopName,0,1)) ?></div><?php endif; ?>
    <h1 class="hero-name"><?= $shopName ?></h1>
    <?php if ($shopTagline): ?><p class="hero-tag"><?= $shopTagline ?></p><?php endif; ?>
  </div>
</div>

<!-- STATS -->
<div class="stats-bar">
  <div class="stat-item"><div class="stat-num"><?= number_format((int)$store['view_count']) ?></div><div class="stat-lbl">Views</div></div>
  <div class="stat-item"><div class="stat-num"><?= count($products) ?></div><div class="stat-lbl">Items</div></div>
  <?php if ($codAvail): ?><div class="stat-item"><div class="stat-num" style="font-size:.75rem;"><i class="fas fa-check"></i> COD</div><div class="stat-lbl">Available</div></div><?php endif; ?>
  <?php if ($deliveryFee > 0): ?><div class="stat-item"><div class="stat-num"><?= $currency ?><?= $deliveryFee ?></div><div class="stat-lbl">Delivery</div></div><?php endif; ?>
</div>

<?php if ($shopDesc): ?><div class="desc-block fade-up"><p><?= htmlspecialchars($shopDesc) ?></p></div><?php endif; ?>

<!-- SEARCH -->
<?php if ($showSearch): ?>
<div class="search-wrap fade-up">
  <div class="search-box"><i class="fas fa-search"></i><input type="text" id="searchInput" placeholder="Search menu..." oninput="searchProducts()"></div>
</div>
<?php endif; ?>

<!-- CATEGORIES -->
<?php if ($showCats): ?>
<div class="cat-wrap fade-up">
  <div class="sec-hd"><i class="fas fa-utensils"></i> Menu</div>
  <div class="cat-scroll">
    <button class="cat-btn active" onclick="filterByCategory('all',this)">All Items</button>
    <?php foreach ($categories as $c): ?><button class="cat-btn" onclick="filterByCategory('<?= $c['id'] ?>',this)"><?= htmlspecialchars($c['name']) ?></button><?php endforeach; ?>
  </div>
</div>
<?php else: ?>
<div class="cat-wrap"><div class="sec-hd"><i class="fas fa-utensils"></i> Our Menu</div></div>
<?php endif; ?>

<!-- MENU ITEMS -->
<div class="menu-wrap">
  <?php if (empty($products)): ?>
  <div style="text-align:center;padding:48px 20px;color:rgba(255,255,255,.25);"><i class="fas fa-concierge-bell" style="font-size:3rem;margin-bottom:12px;display:block;"></i><p>Menu coming soon!</p></div>
  <?php else: ?>
  <?php foreach ($products as $idx => $p): ?>
  <div class="prod-card food-card <?= !$p['in_stock'] ? 'oos-overlay' : '' ?> fade-up" data-cat="<?= htmlspecialchars($p['category_id'] ?? 'none') ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>" style="transition-delay:<?= ($idx % 8) * 0.06 ?>s">
    <div class="food-img-wrap">
      <?php if ($p['img']): ?><img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"><?php else: ?><div class="food-no-img"><i class="fas fa-drumstick-bite"></i></div><?php endif; ?>
      <?php if ($p['is_featured']): ?><span class="food-badge badge-feat">⭐ Chef's Pick</span><?php elseif ($p['has_discount']): ?><span class="food-badge badge-disc"><?= $p['disc_pct'] ?>% OFF</span><?php endif; ?>
    </div>
    <div class="food-info">
      <div>
        <div class="food-name"><?= htmlspecialchars($p['name']) ?></div>
        <?php if (!empty($p['description'])): ?><div class="food-desc"><?= htmlspecialchars($p['description']) ?></div><?php endif; ?>
      </div>
      <div class="food-bottom">
        <div>
          <span class="food-price"><?= $currency ?><?= number_format($p['effective'], 2) ?></span>
          <?php if ($p['has_discount']): ?><span class="food-orig"><?= $currency ?><?= number_format((float)$p['price'], 2) ?></span><?php endif; ?>
        </div>
        <button class="food-btn" <?= !$p['in_stock'] ? 'disabled' : '' ?>
          onclick="addToCart(<?= $p['id'] ?>,'<?= htmlspecialchars(addslashes($p['name'])) ?>',<?= $p['effective'] ?>,'<?= htmlspecialchars($p['img'] ?? '') ?>')">
          <?= $p['in_stock'] ? '+ Add' : 'Sold Out' ?>
        </button>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- FOOTER -->
<footer class="store-footer fade-up">
  <div class="footer-name"><?= $shopName ?></div>
  <div class="footer-links">
    <?php if ($phone): ?><a href="tel:<?= preg_replace('/\D/','',$phone) ?>"><i class="fas fa-phone" style="color:var(--p)"></i> <?= $phone ?></a><?php endif; ?>
    <?php if ($address): ?><a href="#"><i class="fas fa-map-marker-alt" style="color:var(--p)"></i> <?= $address ?></a><?php endif; ?>
    <a href="https://wa.me/<?= $waPhone ?>" target="_blank"><i class="fab fa-whatsapp" style="color:#25D366"></i> Order on WhatsApp</a>
  </div>
  <p style="font-size:.75rem;color:rgba(255,255,255,.3);">Powered by <a href="/" style="color:var(--p);font-weight:700;">Tapify</a> · A unit of <strong>Mr Print World</strong></p>
  <p class="footer-copy">© <?= date('Y') ?> <?= $shopName ?></p>
</footer>
</div>

<div id="cartOverlay" onclick="closeCart()"></div>
<div id="cartDrawer">
  <div class="d-handle"></div>
  <div class="d-header"><span class="d-title">🛒 Your Order</span><button class="d-close" onclick="closeCart()">×</button></div>
  <div id="cartList"></div>
  <div id="cartSummary"></div>
  <form id="checkoutSection" onsubmit="placeOrder(event)" style="display:none;">
    <h4>Your Details</h4>
    <input type="hidden" name="store_id" value="<?= $storeId ?>">
    <div class="ck-field"><label>Name *</label><input name="name" required placeholder="Your full name"></div>
    <div class="ck-field"><label>Phone *</label><input name="phone" type="tel" required placeholder="WhatsApp number"></div>
    <div class="ck-field"><label>Delivery Address</label><textarea name="address" rows="2" placeholder="Street, city, pincode..."></textarea></div>
    <div class="ck-field"><label>Notes</label><textarea name="notes" rows="2" placeholder="Allergies, preferences..."></textarea></div>
    <button type="submit" class="ck-btn"><i class="fab fa-whatsapp"></i> Place Order on WhatsApp</button>
  </form>
</div>
<div id="floatCart" onclick="openCart()">
  <span id="cartBubble">0</span><span>View Order</span><span id="floatTotal"><?= $currency ?>0</span>
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
function placeOrder(e){e.preventDefault();const d=Object.fromEntries(new FormData(e.target));const s=getSub(),t=s+STORE_DATA.deliveryFee;let items=cart.map(i=>`• ${i.name} x${i.qty} = ${STORE_DATA.currency}${(i.qty*i.price).toFixed(2)}`).join('\n');let msg=(STORE_DATA.template||'🍽️ NEW ORDER\n\nName: {customer_name}\nPhone: {customer_phone}\n\n{items}\n\nTotal: {total}').replace('{customer_name}',d.name).replace('{customer_phone}',d.phone).replace('{items}',items).replace('{total}',STORE_DATA.currency+t.toFixed(2));if(d.address)msg+='\nAddress: '+d.address;if(d.notes)msg+='\nNotes: '+d.notes;fetch('/backend/store-order-submit.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({store_id:STORE_DATA.id,customer_name:d.name,customer_phone:d.phone,customer_address:d.address||'',items:cart,subtotal:s,delivery_charge:STORE_DATA.deliveryFee,total_amount:t,notes:d.notes||''})}).catch(()=>{});window.location.href=`https://wa.me/${STORE_DATA.whatsapp}?text=${encodeURIComponent(msg)}`;}
function filterByCategory(catId,el){document.querySelectorAll('.cat-btn').forEach(b=>b.classList.remove('active'));if(el)el.classList.add('active');document.querySelectorAll('.prod-card').forEach(c=>{c.style.display=(catId==='all'||c.dataset.cat==catId)?'':'none';});}
function searchProducts(){const q=(document.getElementById('searchInput')?.value||'').toLowerCase();document.querySelectorAll('.prod-card').forEach(c=>{c.style.display=c.dataset.name.includes(q)?'':'none';});}
function showToast(msg,type='success'){const t=document.createElement('div');t.className='tap-toast '+type;t.textContent=msg;document.body.appendChild(t);setTimeout(()=>t.classList.add('show'),10);setTimeout(()=>{t.classList.remove('show');setTimeout(()=>t.remove(),400);},2500);}
const obs=new IntersectionObserver(e=>e.forEach(en=>{if(en.isIntersecting){en.target.classList.add('visible');obs.unobserve(en.target);}}),{threshold:0.1});
document.querySelectorAll('.fade-up').forEach(el=>obs.observe(el));
</script>
</body>
</html>
