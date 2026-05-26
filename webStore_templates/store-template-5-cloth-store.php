<?php
/* ================================================================
   Tapify Store Template 5 — Fashion & Clothing
   Dark editorial, Cormorant Garamond + Raleway, gold accents
   ================================================================ */
$shopName    = htmlspecialchars($store['store_name'] ?? 'Fashion Store');
$shopTagline = htmlspecialchars($store['tagline'] ?? '');
$shopDesc    = $store['description'] ?? '';
$logo        = !empty($store['logo_image'])  ? imgUrl($store['logo_image'])  : '';
$bannerImg   = !empty($store['cover_image']) ? imgUrl($store['cover_image']) : '';
$waPhone     = preg_replace('/\D/', '', $store['whatsapp_number'] ?? '');
$phone       = htmlspecialchars($store['phone'] ?? '');
$email       = htmlspecialchars($store['email'] ?? '');
$address     = htmlspecialchars($store['address'] ?? '');
$primary     = $store['primary_color'] ?? '#c9a96e';
$secondary   = $store['secondary_color'] ?? '#a07840';
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
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
:root{--p:<?= $primary ?>;--s:<?= $secondary ?>;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Raleway',sans-serif;background:#080808;color:#f5f0e8;overflow-x:hidden;}
.wrap{max-width:480px;margin:0 auto;background:#0c0c0c;min-height:100vh;box-shadow:0 0 80px rgba(0,0,0,.6);position:relative;padding-bottom:100px;}
/* HERO */
.hero{position:relative;height:300px;overflow:hidden;}
.hero img{width:100%;height:100%;object-fit:cover;}
.hero-no-img{width:100%;height:100%;background:linear-gradient(160deg,#1a1a1a 0%,#0c0c0c 100%);}
.hero-overlay{position:absolute;inset:0;background:linear-gradient(to top,#0c0c0c 0%,rgba(12,12,12,.7) 45%,rgba(12,12,12,.1) 100%);}
.hero-content{position:absolute;bottom:0;left:0;right:0;padding:28px 20px;text-align:center;}
.hero-eyebrow{font-family:'Raleway',sans-serif;font-size:.68rem;letter-spacing:4px;text-transform:uppercase;color:var(--p);margin-bottom:8px;}
.hero-name{font-family:'Cormorant Garamond',serif;font-size:2.4rem;font-weight:700;font-style:italic;color:#f5f0e8;line-height:1.1;margin-bottom:6px;}
.hero-tag{font-size:.78rem;color:rgba(255,255,255,.4);letter-spacing:2px;text-transform:uppercase;}
.hero-logo{position:absolute;top:16px;left:16px;width:44px;height:44px;border-radius:50%;border:1px solid rgba(201,169,110,.4);object-fit:cover;}
/* GOLD DIVIDER */
.gold-divider{display:flex;align-items:center;gap:10px;padding:16px 20px;}
.gold-divider::before,.gold-divider::after{content:'';flex:1;height:1px;background:linear-gradient(to right,transparent,var(--p),transparent);}
.gold-divider span{font-size:.65rem;letter-spacing:3px;text-transform:uppercase;color:var(--p);white-space:nowrap;}
/* STORE INFO */
.info-block{padding:0 20px 16px;text-align:center;}
.info-pills{display:flex;justify-content:center;gap:8px;flex-wrap:wrap;margin-bottom:12px;}
.i-pill{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border:1px solid rgba(201,169,110,.3);border-radius:50px;font-size:.68rem;font-weight:600;color:var(--p);text-transform:uppercase;letter-spacing:.5px;}
.info-desc{font-size:.85rem;color:rgba(255,255,255,.5);line-height:1.7;font-style:italic;}
/* SEARCH */
.search-wrap{padding:0 18px 14px;}
.search-box{display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.04);border:1px solid rgba(201,169,110,.3);border-radius:4px;padding:11px 16px;transition:border-color .2s;}
.search-box:focus-within{border-color:var(--p);}
.search-box i{color:var(--p);font-size:.85rem;}
.search-box input{border:none;background:transparent;flex:1;font-family:'Raleway',sans-serif;font-size:.88rem;color:#f5f0e8;outline:none;}
.search-box input::placeholder{color:rgba(255,255,255,.25);}
/* CATEGORIES */
.cat-section{padding:0 18px 16px;}
.sec-hd{font-family:'Cormorant Garamond',serif;font-size:1.1rem;font-style:italic;color:var(--p);margin-bottom:12px;letter-spacing:1px;}
.cat-scroll{display:flex;gap:8px;overflow-x:auto;padding:4px 0 8px;scrollbar-width:none;}
.cat-scroll::-webkit-scrollbar{display:none;}
.cat-btn{flex-shrink:0;padding:7px 18px;border:1px solid rgba(201,169,110,.35);background:transparent;color:rgba(255,255,255,.55);font-family:'Raleway',sans-serif;font-size:.75rem;font-weight:600;cursor:pointer;transition:all .25s;letter-spacing:.5px;text-transform:uppercase;white-space:nowrap;}
.cat-btn.active,.cat-btn:hover{border-color:var(--p);color:var(--p);background:rgba(201,169,110,.07);}
/* PRODUCTS */
.prod-section{padding:0 18px 20px;}
.prod-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;}
.prod-card{background:#181818;border:1px solid rgba(255,255,255,.07);overflow:hidden;transition:all .35s;cursor:pointer;position:relative;}
.prod-card:hover{border-color:rgba(201,169,110,.5);box-shadow:0 0 0 1px rgba(201,169,110,.2);}
.p-img-wrap{position:relative;aspect-ratio:3/4;overflow:hidden;background:#111;}
.p-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .5s;}
.prod-card:hover .p-img-wrap img{transform:scale(1.08);}
.p-no-img{width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.1);font-size:3rem;}
.p-badge{position:absolute;padding:3px 9px;font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
.p-badge-disc{top:0;right:0;background:#7f1d1d;color:#fca5a5;}
.p-badge-feat{top:0;left:0;background:rgba(201,169,110,.9);color:#0c0c0c;}
.p-badge-oos{inset:0;position:absolute;background:rgba(12,12,12,.75);display:flex;align-items:center;justify-content:center;font-family:'Raleway',sans-serif;font-size:.7rem;font-weight:700;color:rgba(255,255,255,.3);letter-spacing:3px;}
.p-body{padding:12px;}
.p-name{font-family:'Cormorant Garamond',serif;font-size:1rem;font-weight:600;color:#f5f0e8;line-height:1.3;margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.p-price-row{display:flex;align-items:center;gap:6px;margin-bottom:10px;}
.p-price{font-size:.95rem;font-weight:600;color:var(--p);}
.p-orig{font-size:.72rem;color:rgba(255,255,255,.25);text-decoration:line-through;}
.add-btn{width:100%;padding:9px;border:1px solid rgba(201,169,110,.4);background:transparent;color:var(--p);font-family:'Raleway',sans-serif;font-size:.75rem;font-weight:700;cursor:pointer;letter-spacing:1px;text-transform:uppercase;transition:all .25s;}
.add-btn:hover{background:var(--p);color:#0c0c0c;border-color:var(--p);}
.add-btn:disabled{border-color:rgba(255,255,255,.1);color:rgba(255,255,255,.2);cursor:not-allowed;}
/* FOOTER */
.store-footer{background:#080808;padding:28px 20px;text-align:center;border-top:1px solid rgba(201,169,110,.2);}
.footer-name{font-family:'Cormorant Garamond',serif;font-size:1.2rem;font-style:italic;color:var(--p);margin-bottom:12px;}
.footer-links a{color:rgba(255,255,255,.35);font-size:.8rem;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:6px;transition:color .2s;}
.footer-links a:hover{color:var(--p);}
.footer-copy{font-size:.68rem;color:rgba(255,255,255,.2);margin-top:10px;}
/* FADE-UP */
.fade-up{opacity:0;transform:translateY(20px);transition:opacity .55s ease,transform .55s ease;}
.fade-up.visible{opacity:1;transform:none;}
/* CART */
#cartOverlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:900;}
#cartDrawer{position:fixed;bottom:0;left:50%;transform:translateX(-50%) translateY(100%);width:100%;max-width:480px;background:#141414;border-radius:0;z-index:901;transition:transform .35s cubic-bezier(.32,0,.67,0);max-height:87vh;overflow-y:auto;box-shadow:0 -4px 40px rgba(0,0,0,.6);border-top:1px solid rgba(201,169,110,.3);}
#cartDrawer.open{transform:translateX(-50%) translateY(0);}
.d-handle{width:40px;height:3px;background:rgba(201,169,110,.3);border-radius:2px;margin:14px auto 18px;}
.d-header{display:flex;justify-content:space-between;align-items:center;padding:0 20px 14px;border-bottom:1px solid rgba(255,255,255,.06);}
.d-title{font-family:'Cormorant Garamond',serif;font-size:1.1rem;font-style:italic;color:var(--p);}
.d-close{background:none;border:none;font-size:1.5rem;cursor:pointer;color:rgba(255,255,255,.3);line-height:1;}
.c-item{display:flex;gap:12px;padding:12px 20px;border-bottom:1px solid rgba(255,255,255,.05);}
.c-img{width:56px;height:56px;overflow:hidden;background:#1a1a1a;flex-shrink:0;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.15);}
.c-img img{width:100%;height:100%;object-fit:cover;}
.c-info{flex:1;}
.c-name{font-family:'Cormorant Garamond',serif;font-size:.95rem;color:#f5f0e8;margin-bottom:2px;}
.c-price{font-weight:600;color:var(--p);font-size:.88rem;margin-bottom:6px;}
.c-qty-row{display:flex;align-items:center;gap:8px;}
.c-qty-btn{width:26px;height:26px;border:1px solid rgba(201,169,110,.3);background:transparent;cursor:pointer;font-size:.85rem;color:var(--p);display:flex;align-items:center;justify-content:center;transition:all .2s;}
.c-qty-btn:hover{background:var(--p);border-color:var(--p);color:#0c0c0c;}
.c-qty-val{font-weight:700;min-width:20px;text-align:center;color:#f5f0e8;}
.c-del{border-color:rgba(239,68,68,.3);color:#ef4444;}
#cartSummary{padding:12px 20px;}
.c-sum-row{display:flex;justify-content:space-between;padding:5px 0;font-size:.85rem;color:rgba(255,255,255,.5);}
.c-total{font-weight:700;font-size:.98rem;padding-top:10px;border-top:1px solid rgba(201,169,110,.25);margin-top:6px;color:var(--p);}
.c-min-warn{color:#ef4444;font-size:.72rem;text-align:center;margin-top:6px;}
#checkoutSection{padding:16px 20px 28px;}
#checkoutSection h4{font-family:'Cormorant Garamond',serif;font-size:1.05rem;font-style:italic;color:var(--p);margin-bottom:14px;}
.ck-field{margin-bottom:10px;}
.ck-field label{display:block;font-size:.68rem;font-weight:700;margin-bottom:4px;color:rgba(255,255,255,.3);text-transform:uppercase;letter-spacing:.8px;}
.ck-field input,.ck-field textarea{width:100%;padding:10px 14px;border:1px solid rgba(201,169,110,.25);border-radius:0;font-family:'Raleway',sans-serif;font-size:.85rem;background:rgba(255,255,255,.03);color:#f5f0e8;outline:none;transition:border-color .2s;}
.ck-field input:focus,.ck-field textarea:focus{border-color:var(--p);}
.ck-btn{width:100%;padding:14px;border:none;background:linear-gradient(135deg,var(--p),var(--s));color:#0c0c0c;font-family:'Raleway',sans-serif;font-weight:700;font-size:.92rem;letter-spacing:1px;text-transform:uppercase;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;}
#floatCart{display:none;position:fixed;bottom:22px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,var(--p),var(--s));color:#0c0c0c;padding:14px 28px;border-radius:0;cursor:pointer;z-index:850;align-items:center;gap:12px;box-shadow:0 8px 30px rgba(0,0,0,.4);max-width:92vw;font-weight:700;white-space:nowrap;font-family:'Raleway',sans-serif;letter-spacing:1px;font-size:.88rem;text-transform:uppercase;}
#cartBubble{background:#0c0c0c;color:var(--p);width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:800;}
.tap-toast{position:fixed;top:20px;left:50%;transform:translateX(-50%) translateY(-80px);background:#1a1a1a;color:var(--p);padding:10px 22px;border-radius:0;font-weight:700;font-size:.82rem;z-index:9999;transition:transform .35s;white-space:nowrap;max-width:90vw;border:1px solid rgba(201,169,110,.3);}
.tap-toast.show{transform:translateX(-50%) translateY(0);}
.tap-toast.error{color:#ef4444;border-color:rgba(239,68,68,.3);}
.tap-toast.success{color:#10b981;border-color:rgba(16,185,129,.3);}
</style>
</head>
<body>
<div class="wrap">

<!-- HERO -->
<div class="hero">
  <?php if ($bannerImg): ?><img src="<?= $bannerImg ?>" alt=""><?php else: ?><div class="hero-no-img"></div><?php endif; ?>
  <div class="hero-overlay"></div>
  <?php if ($logo): ?><img src="<?= $logo ?>" class="hero-logo" alt="Logo"><?php endif; ?>
  <div class="hero-content">
    <div class="hero-eyebrow">Collection</div>
    <div class="hero-name"><?= $shopName ?></div>
    <?php if ($shopTagline): ?><div class="hero-tag"><?= $shopTagline ?></div><?php endif; ?>
  </div>
</div>

<!-- GOLD DIVIDER -->
<div class="gold-divider"><span>Est. <?= date('Y') ?></span></div>

<!-- STORE INFO -->
<div class="info-block fade-up">
  <div class="info-pills">
    <span class="i-pill"><i class="fas fa-eye"></i> <?= number_format((int)$store['view_count']) ?> views</span>
    <span class="i-pill"><i class="fas fa-tshirt"></i> <?= count($products) ?> pieces</span>
    <?php if ($codAvail): ?><span class="i-pill"><i class="fas fa-check"></i> COD</span><?php endif; ?>
  </div>
  <?php if ($shopDesc): ?><p class="info-desc"><?= htmlspecialchars($shopDesc) ?></p><?php endif; ?>
</div>

<!-- SEARCH -->
<?php if ($showSearch): ?>
<div class="search-wrap fade-up">
  <div class="search-box"><i class="fas fa-search"></i><input type="text" id="searchInput" placeholder="Search the collection..." oninput="searchProducts()"></div>
</div>
<?php endif; ?>

<!-- CATEGORIES -->
<?php if ($showCats): ?>
<div class="cat-section fade-up">
  <div class="sec-hd">Browse Collection</div>
  <div class="cat-scroll">
    <button class="cat-btn active" onclick="filterByCategory('all',this)">All Pieces</button>
    <?php foreach ($categories as $c): ?><button class="cat-btn" onclick="filterByCategory('<?= $c['id'] ?>',this)"><?= htmlspecialchars($c['name']) ?></button><?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- PRODUCTS -->
<div class="prod-section">
  <div class="sec-hd" style="margin-bottom:14px;">New Arrivals</div>
  <?php if (empty($products)): ?>
  <div style="text-align:center;padding:48px 20px;color:rgba(255,255,255,.2);"><i class="fas fa-hanger" style="font-size:3rem;margin-bottom:12px;display:block;"></i><p>Collection coming soon</p></div>
  <?php else: ?>
  <div class="prod-grid">
    <?php foreach ($products as $idx => $p): ?>
    <div class="prod-card fade-up" data-cat="<?= htmlspecialchars($p['category_id'] ?? 'none') ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>" style="transition-delay:<?= ($idx % 6) * 0.07 ?>s">
      <div class="p-img-wrap">
        <?php if ($p['img']): ?><img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"><?php else: ?><div class="p-no-img"><i class="fas fa-tshirt"></i></div><?php endif; ?>
        <?php if ($p['is_featured']): ?><span class="p-badge p-badge-feat">NEW</span><?php endif; ?>
        <?php if ($p['has_discount']): ?><span class="p-badge p-badge-disc">SALE <?= $p['disc_pct'] ?>%</span><?php endif; ?>
        <?php if (!$p['in_stock']): ?><div class="p-badge p-badge-oos">SOLD OUT</div><?php endif; ?>
      </div>
      <div class="p-body">
        <div class="p-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="p-price-row">
          <span class="p-price"><?= $currency ?><?= number_format($p['effective'], 2) ?></span>
          <?php if ($p['has_discount']): ?><span class="p-orig"><?= $currency ?><?= number_format((float)$p['price'], 2) ?></span><?php endif; ?>
        </div>
        <button class="add-btn" <?= !$p['in_stock'] ? 'disabled' : '' ?>
          onclick="addToCart(<?= $p['id'] ?>,'<?= htmlspecialchars(addslashes($p['name'])) ?>',<?= $p['effective'] ?>,'<?= htmlspecialchars($p['img'] ?? '') ?>')">
          <?= $p['in_stock'] ? '+ Add to Bag' : 'Sold Out' ?>
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
  <div class="gold-divider" style="padding:8px 40px;margin-bottom:12px;"><span style="font-size:.55rem;">CRAFTED WITH CARE</span></div>
  <div class="footer-links">
    <?php if ($phone): ?><a href="tel:<?= preg_replace('/\D/','',$phone) ?>"><i class="fas fa-phone" style="color:var(--p)"></i> <?= $phone ?></a><?php endif; ?>
    <?php if ($email): ?><a href="mailto:<?= $email ?>"><i class="fas fa-envelope" style="color:var(--p)"></i> <?= $email ?></a><?php endif; ?>
    <?php if ($address): ?><a href="#"><i class="fas fa-map-marker-alt" style="color:var(--p)"></i> <?= $address ?></a><?php endif; ?>
    <a href="https://wa.me/<?= $waPhone ?>" target="_blank"><i class="fab fa-whatsapp" style="color:#25D366"></i> Shop via WhatsApp</a>
  </div>
  <p style="font-size:.72rem;color:rgba(255,255,255,.25);">Powered by <a href="/" style="color:var(--p);font-weight:700;">Tapify</a> · A unit of <strong>Mr Print World</strong></p>
  <p class="footer-copy">© <?= date('Y') ?> <?= $shopName ?></p>
</footer>
</div>

<div id="cartOverlay" onclick="closeCart()"></div>
<div id="cartDrawer">
  <div class="d-handle"></div>
  <div class="d-header"><span class="d-title">Your Bag</span><button class="d-close" onclick="closeCart()">×</button></div>
  <div id="cartList"></div>
  <div id="cartSummary"></div>
  <form id="checkoutSection" onsubmit="placeOrder(event)" style="display:none;">
    <h4>Your Details</h4>
    <input type="hidden" name="store_id" value="<?= $storeId ?>">
    <div class="ck-field"><label>Name *</label><input name="name" required placeholder="Your full name"></div>
    <div class="ck-field"><label>Phone *</label><input name="phone" type="tel" required placeholder="WhatsApp number"></div>
    <div class="ck-field"><label>Delivery Address</label><textarea name="address" rows="2" placeholder="Street, city, pincode..."></textarea></div>
    <div class="ck-field"><label>Notes</label><textarea name="notes" rows="2" placeholder="Size, colour preferences..."></textarea></div>
    <button type="submit" class="ck-btn"><i class="fab fa-whatsapp"></i> Complete Order</button>
  </form>
</div>
<div id="floatCart" onclick="openCart()">
  <span id="cartBubble">0</span><span>My Bag</span><span id="floatTotal"><?= $currency ?>0</span>
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
function openCart(){if(!cart.length){showToast('Bag is empty','error');return;}renderCartDrawer();document.getElementById('cartDrawer').classList.add('open');document.getElementById('cartOverlay').style.display='block';}
function closeCart(){document.getElementById('cartDrawer').classList.remove('open');document.getElementById('cartOverlay').style.display='none';}
function renderCartDrawer(){const s=getSub(),t=s+STORE_DATA.deliveryFee;document.getElementById('cartList').innerHTML=cart.map(it=>`<div class="c-item"><div class="c-img">${it.img?`<img src="${it.img}" alt="">`:''}</div><div class="c-info"><div class="c-name">${it.name}</div><div class="c-price">${STORE_DATA.currency}${it.price.toFixed(2)}</div><div class="c-qty-row"><button class="c-qty-btn" onclick="changeQty(${it.id},-1)">−</button><span class="c-qty-val">${it.qty}</span><button class="c-qty-btn" onclick="changeQty(${it.id},1)">+</button><button class="c-qty-btn c-del" onclick="removeFromCart(${it.id})"><i class="fas fa-trash-alt"></i></button></div></div></div>`).join('');document.getElementById('cartSummary').innerHTML=`<div class="c-sum-row"><span>Subtotal</span><span>${STORE_DATA.currency}${s.toFixed(2)}</span></div>${STORE_DATA.deliveryFee>0?`<div class="c-sum-row"><span>Delivery</span><span>${STORE_DATA.currency}${STORE_DATA.deliveryFee.toFixed(2)}</span></div>`:''}<div class="c-sum-row c-total"><span>Total</span><span>${STORE_DATA.currency}${t.toFixed(2)}</span></div>${s<STORE_DATA.minOrder&&STORE_DATA.minOrder>0?`<p class="c-min-warn">Min order: ${STORE_DATA.currency}${STORE_DATA.minOrder.toFixed(2)}</p>`:''}`;document.getElementById('checkoutSection').style.display=(s>=STORE_DATA.minOrder||STORE_DATA.minOrder===0)?'block':'none';}
function placeOrder(e){e.preventDefault();const d=Object.fromEntries(new FormData(e.target));const s=getSub(),t=s+STORE_DATA.deliveryFee;let items=cart.map(i=>`• ${i.name} x${i.qty} = ${STORE_DATA.currency}${(i.qty*i.price).toFixed(2)}`).join('\n');let msg=(STORE_DATA.template||'👗 NEW ORDER\n\nName: {customer_name}\nPhone: {customer_phone}\n\n{items}\n\nTotal: {total}').replace('{customer_name}',d.name).replace('{customer_phone}',d.phone).replace('{items}',items).replace('{total}',STORE_DATA.currency+t.toFixed(2));if(d.address)msg+='\nAddress: '+d.address;if(d.notes)msg+='\nNotes: '+d.notes;fetch('/backend/store-order-submit.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({store_id:STORE_DATA.id,customer_name:d.name,customer_phone:d.phone,customer_address:d.address||'',items:cart,subtotal:s,delivery_charge:STORE_DATA.deliveryFee,total_amount:t,notes:d.notes||''})}).catch(()=>{});window.location.href=`https://wa.me/${STORE_DATA.whatsapp}?text=${encodeURIComponent(msg)}`;}
function filterByCategory(catId,el){document.querySelectorAll('.cat-btn').forEach(b=>b.classList.remove('active'));if(el)el.classList.add('active');document.querySelectorAll('.prod-card').forEach(c=>{c.style.display=(catId==='all'||c.dataset.cat==catId)?'':'none';});}
function searchProducts(){const q=(document.getElementById('searchInput')?.value||'').toLowerCase();document.querySelectorAll('.prod-card').forEach(c=>{c.style.display=c.dataset.name.includes(q)?'':'none';});}
function showToast(msg,type='success'){const t=document.createElement('div');t.className='tap-toast '+type;t.textContent=msg;document.body.appendChild(t);setTimeout(()=>t.classList.add('show'),10);setTimeout(()=>{t.classList.remove('show');setTimeout(()=>t.remove(),400);},2500);}
const obs=new IntersectionObserver(e=>e.forEach(en=>{if(en.isIntersecting){en.target.classList.add('visible');obs.unobserve(en.target);}}),{threshold:0.1});
document.querySelectorAll('.fade-up').forEach(el=>obs.observe(el));
</script>
</body>
</html>
