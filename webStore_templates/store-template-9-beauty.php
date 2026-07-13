<?php
/**
 * TAPIFY - WhatsApp Store Template 9 — "Ethereal Beauty" (v2, light theme)
 * Converted from webStoreTemps/Theme 1. Renders shared $store/$categories/$products.
 */
$cfg   = $templateConfig ?? [];
$asset = '/webStore_templates/assets/store_template_9';
$defC  = $cfg['default_colors'] ?? ['primary' => '#c29c77', 'secondary' => '#3d1f2b', 'accent' => '#e9c2cf', 'text' => '#2d0a18'];
$legacy = ['#25D366', '#128C7E', '', null];
$P = (!in_array($store['primary_color']   ?? '', $legacy, true)) ? $store['primary_color']   : $defC['primary'];
$S = (!in_array($store['secondary_color'] ?? '', $legacy, true)) ? $store['secondary_color'] : $defC['secondary'];
$A = !empty($store['accent_color']) ? $store['accent_color'] : ($defC['accent'] ?? $P);

$shopName  = htmlspecialchars($store['store_name'] ?? 'Store');
$initial   = function_exists('mb_substr') ? mb_substr($shopName, 0, 1) : substr($shopName, 0, 1);
$logo      = !empty($store['logo_image']) ? imgUrl($store['logo_image']) : '';
$bannerImg = !empty($store['cover_image']) ? imgUrl($store['cover_image']) : "$asset/banner.webp";
$waPhone   = preg_replace('/\D/', '', $store['whatsapp_number'] ?? '');
$address   = htmlspecialchars($store['address'] ?? '');
$deliveryFee = (float)($store['delivery_charge'] ?? 0);
$minOrder    = (float)($store['min_order_amount'] ?? 0);
$waTemplate  = $store['order_message_template'] ?? '';
$cta         = $cfg['product_cta'] ?? 'Add to Cart';
$enableTranslate = !array_key_exists('enable_translate', $store) || !empty($store['enable_translate']);
$seoTitle  = !empty($store['seo_title']) ? htmlspecialchars($store['seo_title']) : "Product Listing | $shopName";
$favicon   = !empty($store['favicon_image']) ? imgUrl($store['favicon_image']) : ($logo ?: '/images/tapify-logo-green.png');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $seoTitle ?></title>
<?php if (!empty($store['seo_description'])): ?><meta name="description" content="<?= htmlspecialchars($store['seo_description']) ?>"><?php endif; ?>
<link rel="icon" href="<?= $favicon ?>" type="image/png">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#faf8f5;color:#2d0a18;font-family:'Poppins',sans-serif;overflow-x:hidden;}
.d-none{display:none!important;}
:root{--tp:<?= $P ?>;--ts:<?= $S ?>;--ta:<?= $A ?>;}

/* Background Vectors */
.tp-bg-vector{position:fixed;pointer-events:none;z-index:0;opacity:.10;}
.tp-bg-v1{top:10%;left:-5%;width:300px;height:300px;background:var(--ta);border-radius:50%;filter:blur(80px);}
.tp-bg-v2{top:40%;right:-8%;width:250px;height:250px;background:var(--tp);border-radius:50%;filter:blur(60px);}
.tp-bg-v3{bottom:15%;left:10%;width:200px;height:200px;background:var(--ta);border-radius:50%;filter:blur(70px);}

/* Navbar */
.tp-nav{display:flex;align-items:center;justify-content:space-between;padding:12px 24px;background:rgba(255,255,255,.85);backdrop-filter:blur(10px);position:sticky;top:0;z-index:100;box-shadow:0 1px 4px rgba(0,0,0,.04);}
.tp-nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;color:#2d0a18;}
.tp-nav-brand img{width:36px;height:36px;border-radius:50%;object-fit:cover;}
.tp-nav-brand .brand-letter{width:36px;height:36px;border-radius:50%;background:var(--tp);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;}
.tp-nav-brand span{font-size:17px;font-weight:600;color:#2d0a18;}
.tp-nav-right{display:flex;align-items:center;gap:16px;}
.tp-lang-select{padding:6px 10px;border:1px solid #ddd;border-radius:10px;font-size:13px;color:#555;background:#fff;cursor:pointer;}
.tp-cart-btn{position:relative;background:none;border:none;cursor:pointer;color:#2d0a18;font-size:22px;}
.tp-cart-badge{position:absolute;top:-6px;right:-8px;background:var(--tp);color:#fff;font-size:10px;font-weight:700;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;}

/* Hero */
.hero-img{width:100%;overflow:hidden;position:relative;z-index:1;}
.hero-img img{width:100%;height:auto;object-fit:cover;display:block;}

/* Section Headings */
.tp-section-title{font-size:22px;font-weight:700;color:#2d0a18;margin:28px 0 16px;padding:0 24px;position:relative;z-index:1;}

/* Category Carousel */
.tp-cat-carousel{position:relative;padding:0 50px 10px;z-index:1;}
.tp-cat-track{display:flex;gap:16px;overflow-x:auto;padding:10px 0;scroll-behavior:smooth;-ms-overflow-style:none;scrollbar-width:none;}
.tp-cat-track::-webkit-scrollbar{display:none;}
.tp-cat-item{min-width:130px;text-align:center;cursor:pointer;transition:transform .2s;}
.tp-cat-item:hover{transform:scale(1.03);}
.tp-cat-item.active .tp-cat-circle{border-color:var(--tp);}
.tp-cat-circle{width:72px;height:72px;border-radius:50%;overflow:hidden;border:3px solid transparent;margin:0 auto 8px;background:#f0ebe5;}
.tp-cat-circle img{width:100%;height:100%;object-fit:cover;}
.tp-cat-label{font-size:12px;color:#555;font-weight:500;line-height:1.3;}
.tp-carousel-arrow{position:absolute;top:50%;transform:translateY(-50%);width:32px;height:32px;border-radius:50%;background:#fff;border:1px solid #e0d8d0;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;color:#333;z-index:5;box-shadow:0 2px 6px rgba(0,0,0,.06);}
.tp-carousel-arrow.left{left:8px;}
.tp-carousel-arrow.right{right:8px;}

/* Product Grid */
.tp-products{padding:0 24px 30px;position:relative;z-index:1;}
.tp-product-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;}
.tp-product-card{background:#fff;border-radius:14px;overflow:hidden;display:flex;flex-direction:column;border:1px solid #f0ebe5;transition:transform .3s,box-shadow .3s;}
.tp-product-card:hover{transform:translateY(-3px);box-shadow:0 6px 20px rgba(194,156,119,.12);}
.tp-product-img{width:100%;aspect-ratio:1;overflow:hidden;background:#faf8f5;}
.tp-product-img img{width:100%;height:100%;object-fit:cover;}
.tp-product-img .no-img{width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ddd;font-size:2rem;}
.tp-product-info{padding:12px 14px;flex-grow:1;display:flex;flex-direction:column;}
.tp-product-name{font-size:14px;font-weight:600;color:#2d0a18;margin-bottom:2px;}
.tp-product-cat{font-size:12px;color:#999;margin-bottom:8px;}
.tp-product-price{font-size:15px;font-weight:700;color:#2d0a18;margin-bottom:10px;}
.tp-product-price del{color:#bbb;font-size:12px;font-weight:400;margin-left:4px;}
.tp-add-cart{background:var(--tp);color:#fff;border:none;border-radius:10px;padding:9px 14px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;width:100%;margin-top:auto;transition:filter .2s;}
.tp-add-cart:hover{filter:brightness(1.1);}
.tp-add-cart:disabled{opacity:.5;cursor:not-allowed;}

/* View More Button */
.tp-view-more-wrap{text-align:center;padding:10px 0 50px;position:relative;z-index:1;}
.tp-view-more{display:inline-flex;align-items:center;gap:0;background:#fff;border:1px solid #e0d8d0;border-radius:50px;overflow:hidden;text-decoration:none;transition:transform .2s,box-shadow .2s;}
.tp-view-more:hover{transform:scale(1.03);box-shadow:0 4px 12px rgba(194,156,119,.15);}
.tp-view-more-arrow{width:40px;height:40px;border-radius:50%;background:var(--tp);display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;margin-right:-1px;}
.tp-view-more-text{padding:10px 22px 10px 14px;color:#333;font-size:14px;font-weight:600;}

/* Filtered View (after View More) */
.tp-filtered-view{display:none;padding:20px 24px;max-width:1400px;margin:0 auto;position:relative;z-index:1;}
.tp-filtered-view.active{display:block;}
.tp-filter-layout{display:flex;gap:24px;}
.tp-filter-sidebar{width:260px;min-width:260px;background:#fff;border-radius:14px;padding:20px;height:fit-content;position:sticky;top:80px;border:1px solid #f0ebe5;}
.tp-filter-sidebar input[type="search"],.tp-filter-sidebar input[type="number"],.tp-filter-sidebar select{width:100%;padding:10px 12px;border:1px solid #e0d8d0;border-radius:8px;background:#fff;color:#333;font-size:14px;outline:none;}
.tp-filter-sidebar input[type="search"]:focus,.tp-filter-sidebar input[type="number"]:focus,.tp-filter-sidebar select:focus{border-color:var(--tp);}
.tp-filter-sidebar input[type="search"]{padding-left:36px;}
.tp-search-wrap{position:relative;margin-bottom:16px;}
.tp-search-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#999;font-size:14px;}
.tp-price-row{display:flex;gap:8px;margin-bottom:12px;}
.tp-price-row input{flex:1;}
.tp-apply-btn{background:var(--tp);color:#fff;border:none;border-radius:8px;padding:10px 16px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;}
.tp-apply-btn:hover{filter:brightness(1.1);}
.tp-filter-section{margin-bottom:20px;}
.tp-filter-section h4{font-size:15px;font-weight:700;color:#2d0a18;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid #f0ebe5;}
.tp-filter-section label{display:flex;align-items:center;gap:8px;font-size:14px;color:#555;margin-bottom:10px;cursor:pointer;}
.tp-filter-section input[type="radio"],.tp-filter-section input[type="checkbox"]{accent-color:var(--tp);width:16px;height:16px;cursor:pointer;}
.tp-filter-section select{margin-bottom:12px;}
.tp-reset-btn{width:100%;background:var(--tp);color:#fff;border:none;border-radius:10px;padding:12px;font-size:14px;font-weight:600;cursor:pointer;margin-top:8px;}
.tp-reset-btn:hover{filter:brightness(1.1);}
.tp-filter-products{flex:1;}
.tp-filter-products .tp-product-grid{max-width:none;margin:0;}

/* Responsive */
@media(max-width:1024px){.tp-product-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:992px){
  .tp-filter-layout{flex-direction:column;}
  .tp-filter-sidebar{width:100%;min-width:100%;position:static;}
}
@media(max-width:768px){
  .tp-product-grid{grid-template-columns:repeat(2,1fr);gap:12px;}
  .tp-cat-circle{width:60px;height:60px;}
  .tp-cat-label{font-size:11px;}
  .tp-section-title{font-size:18px;padding:0 16px;}
  .tp-cat-carousel{padding:0 40px 10px;}
  .tp-products{padding:0 16px 30px;}
}
@media(max-width:480px){
  .tp-product-grid{grid-template-columns:repeat(2,1fr);gap:10px;}
  .tp-product-info{padding:10px 12px;}
  .tp-product-name{font-size:13px;}
  .tp-product-price{font-size:14px;}
  .tp-add-cart{padding:8px 12px;font-size:12px;}
}

/* Cart Drawer */
#tpCartOverlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1200;}
#tpCartDrawer{position:fixed;top:0;right:0;height:100%;width:100%;max-width:400px;background:#fff;z-index:1201;transform:translateX(105%);transition:transform .35s ease;display:flex;flex-direction:column;box-shadow:-10px 0 40px rgba(0,0,0,.15);}
#tpCartDrawer.open{transform:none;}
.tp-cart-head{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid #f0ebe5;color:#2d0a18;}
.tp-cart-head h5{margin:0;font-weight:700;}
.tp-cart-close{background:#f3f3f3;border:none;width:34px;height:34px;border-radius:50%;font-size:1.2rem;cursor:pointer;}
.tp-cart-items{flex:1;overflow-y:auto;padding:10px 20px;}
.tp-ci{display:flex;gap:12px;padding:12px 0;border-bottom:1px solid #f4f4f4;color:#2d0a18;}
.tp-ci img{width:56px;height:56px;object-fit:cover;border-radius:8px;background:#f4f4f4;}
.tp-ci .n{font-weight:600;font-size:.9rem;}
.tp-ci .p{color:var(--tp);font-weight:700;}
.tp-qty{display:flex;align-items:center;gap:8px;margin-top:6px;}
.tp-qty button{width:26px;height:26px;border:1px solid #ddd;background:#fff;border-radius:50%;cursor:pointer;}
.tp-cart-foot{padding:16px 20px;border-top:1px solid #f0ebe5;color:#2d0a18;}
.tp-cart-foot .row-l{display:flex;justify-content:space-between;padding:3px 0;font-size:.92rem;}
.tp-cart-foot .tot{font-weight:700;font-size:1.05rem;}
.tp-field{margin-bottom:10px;}
.tp-field input,.tp-field textarea{width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px;font-size:.9rem;}
.tp-checkout{width:100%;padding:13px;border:none;border-radius:10px;background:#25D366;color:#fff;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;}
.tp-toast{position:fixed;top:20px;left:50%;transform:translateX(-50%) translateY(-90px);background:#2d0a18;color:#fff;padding:11px 22px;border-radius:50px;font-weight:600;z-index:9999;transition:transform .35s;}
.tp-toast.show{transform:translateX(-50%) translateY(0);}
</style>
</head>
<body>
<!-- Background Vectors -->
<div class="tp-bg-vector tp-bg-v1"></div>
<div class="tp-bg-vector tp-bg-v2"></div>
<div class="tp-bg-vector tp-bg-v3"></div>

<div class="main-content mx-auto w-100">
  <!-- Navbar -->
  <nav class="tp-nav">
    <a class="tp-nav-brand" href="#">
      <?php if ($logo): ?><img src="<?= $logo ?>" alt="logo">
      <?php else: ?><span class="brand-letter"><?= $initial ?></span><?php endif; ?>
      <span><?= $shopName ?></span>
    </a>
    <div class="tp-nav-right">
      <?php if ($enableTranslate): ?><select class="tp-lang-select"><option>Select Language</option></select><?php endif; ?>
      <button class="tp-cart-btn" onclick="tpOpenCart()">
        <i class="fas fa-shopping-bag"></i>
        <span class="tp-cart-badge" id="tpCartCount">0</span>
      </button>
    </div>
  </nav>

  <!-- Hero Image -->
  <div class="hero-img"><img src="<?= $bannerImg ?>" alt="banner" onerror="this.style.display='none'"></div>

  <!-- Category Section -->
  <h2 class="tp-section-title">Choose your Category</h2>
  <div class="tp-cat-carousel">
    <button class="tp-carousel-arrow left" onclick="tpScrollCats(-1)"><i class="fas fa-chevron-left"></i></button>
    <div class="tp-cat-track" id="tpCatTrack">
      <div class="tp-cat-item active" data-cat="all" onclick="tpFilterCat('all',this)">
        <div class="tp-cat-circle"><img src="<?= $asset ?>/bg-vector.webp" alt="All"></div>
        <div class="tp-cat-label">All</div>
      </div>
      <?php foreach ($categories as $c): ?>
      <div class="tp-cat-item" data-cat="<?= (int)$c['id'] ?>" onclick="tpFilterCat(<?= (int)$c['id'] ?>,this)">
        <div class="tp-cat-circle"><?php if (!empty($c['image'])): ?><img src="<?= imgUrl($c['image']) ?>" alt="<?= htmlspecialchars($c['name']) ?>"><?php else: ?><img src="<?= $asset ?>/bg-vector_2.webp" alt="cat"><?php endif; ?></div>
        <div class="tp-cat-label"><?= htmlspecialchars($c['name']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <button class="tp-carousel-arrow right" onclick="tpScrollCats(1)"><i class="fas fa-chevron-right"></i></button>
  </div>

  <!-- Products Section -->
  <h2 class="tp-section-title">Choose your Products</h2>
  <div class="tp-products">
    <div class="tp-product-grid" id="tpProductGrid">
      <?php if (!empty($products)): foreach ($products as $p): $pimg = $p['img'] ?? ''; ?>
      <div class="tp-product-card tp-product"
           data-cat="<?= (int)$p['category_id'] ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
           data-price="<?= (float)$p['effective_price'] ?>">
        <div class="tp-product-img">
          <?php if ($pimg): ?><img src="<?= $pimg ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          <?php else: ?><div class="no-img"><i class="fas fa-image"></i></div><?php endif; ?>
        </div>
        <div class="tp-product-info">
          <div class="tp-product-name"><?= htmlspecialchars($p['name']) ?></div>
          <?php if (!empty($p['category_name'])): ?><div class="tp-product-cat"><?= htmlspecialchars($p['category_name']) ?></div><?php endif; ?>
          <div class="tp-product-price">
            <span class="currency_icon"><?= $currency ?></span>
            <span class="selling_price"><?= number_format($p['effective_price'], 2) ?></span>
            <?php if (!empty($p['has_discount'])): ?><del><?= $currency ?> <?= number_format((float)$p['price'], 2) ?></del><?php endif; ?>
          </div>
          <?php if (!empty($p['in_stock'])): ?>
          <button class="tp-add-cart" data-id="<?= (int)$p['id'] ?>" data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                  data-price="<?= (float)$p['effective_price'] ?>" data-img="<?= htmlspecialchars($pimg, ENT_QUOTES) ?>" onclick="tpAddToCart(this)">
            <i class="fas fa-shopping-bag"></i> <?= htmlspecialchars($cta) ?>
          </button>
          <?php else: ?>
          <button class="tp-add-cart" disabled>Sold Out</button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; else: ?>
      <div class="text-center py-5" style="grid-column:1/-1;"><i class="fas fa-box-open" style="font-size:3rem;color:#ddd"></i><p class="mt-3" style="color:#999;">No products available yet</p></div>
      <?php endif; ?>
    </div>
    <div class="text-center py-5 d-none" id="tpNoResults" style="grid-column:1/-1;color:#999;"><i class="fas fa-search" style="font-size:2.4rem;color:#ddd"></i><p class="mt-3">No products match your filters</p></div>
  </div>

  <!-- View More Button -->
  <div class="tp-view-more-wrap" id="tpViewMoreWrap">
    <a href="#" class="tp-view-more" onclick="tpShowFiltered(event)">
      <span class="tp-view-more-arrow"><i class="fas fa-arrow-right"></i></span>
      <span class="tp-view-more-text">View More</span>
    </a>
  </div>

  <!-- Filtered View (shown after View More click) -->
  <div class="tp-filtered-view" id="tpFilteredView">
    <div class="tp-filter-layout">
      <!-- Sidebar Filters -->
      <div class="tp-filter-sidebar">
        <div class="tp-search-wrap">
          <i class="fas fa-search"></i>
          <input type="search" id="tpSearch2" placeholder="Search for Items" oninput="tpSearch2()">
        </div>
        <div class="tp-price-row">
          <input type="number" id="tpMinPrice2" placeholder="Min" min="0">
          <input type="number" id="tpMaxPrice2" placeholder="Max" min="0">
          <button class="tp-apply-btn" onclick="tpApplyPrice2()">Apply</button>
        </div>
        <div class="tp-filter-section">
          <h4>Date Posted</h4>
          <?php $__dates = ['3_days'=>'3 Days Ago','1_week'=>'1 Week Ago','1_month'=>'1 Month Ago','6_months'=>'6 Months Ago','1_year'=>'1 Year Ago']; $__i=0; foreach ($__dates as $val=>$lbl): $__i++; ?>
          <label><input type="radio" name="tpDateFilter2" value="<?= $val ?>" onchange="tpApplyDate2('<?= $val ?>')"> <?= $lbl ?></label>
          <?php endforeach; ?>
        </div>
        <div class="tp-filter-section">
          <h4>Select Price Range</h4>
          <select id="tpPriceRange2" onchange="tpApplyPriceRange2(this.value)">
            <option value="">Select Price Range</option>
          </select>
        </div>
        <div class="tp-filter-section">
          <h4>All Categories</h4>
          <?php foreach ($categories as $c): ?>
          <label><input type="checkbox" class="tpCatCheck2" value="<?= (int)$c['id'] ?>" onchange="tpApplyCats2()"> <?= htmlspecialchars($c['name']) ?></label>
          <?php endforeach; ?>
        </div>
        <button class="tp-reset-btn" onclick="tpResetFilters2()">Reset Filters</button>
      </div>
      <!-- Products Grid -->
      <div class="tp-filter-products">
        <div class="tp-product-grid" id="tpFilteredGrid">
          <?php if (!empty($products)): foreach ($products as $p): $pimg = $p['img'] ?? ''; ?>
          <div class="tp-product-card tp-product2"
               data-cat="<?= (int)$p['category_id'] ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
               data-price="<?= (float)$p['effective_price'] ?>" data-created="<?= strtotime($p['created_at'] ?? 'now') ?>">
            <div class="tp-product-img">
              <?php if ($pimg): ?><img src="<?= $pimg ?>" alt="<?= htmlspecialchars($p['name']) ?>">
              <?php else: ?><div class="no-img"><i class="fas fa-image"></i></div><?php endif; ?>
            </div>
            <div class="tp-product-info">
              <div class="tp-product-name"><?= htmlspecialchars($p['name']) ?></div>
              <?php if (!empty($p['category_name'])): ?><div class="tp-product-cat"><?= htmlspecialchars($p['category_name']) ?></div><?php endif; ?>
              <div class="tp-product-price">
                <span class="currency_icon"><?= $currency ?></span>
                <span class="selling_price"><?= number_format($p['effective_price'], 2) ?></span>
                <?php if (!empty($p['has_discount'])): ?><del><?= $currency ?> <?= number_format((float)$p['price'], 2) ?></del><?php endif; ?>
              </div>
              <?php if (!empty($p['in_stock'])): ?>
              <button class="tp-add-cart" data-id="<?= (int)$p['id'] ?>" data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                      data-price="<?= (float)$p['effective_price'] ?>" data-img="<?= htmlspecialchars($pimg, ENT_QUOTES) ?>" onclick="tpAddToCart(this)">
                <i class="fas fa-shopping-bag"></i> <?= htmlspecialchars($cta) ?>
              </button>
              <?php else: ?>
              <button class="tp-add-cart" disabled>Sold Out</button>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; endif; ?>
        </div>
        <div class="text-center py-5 d-none" id="tpNoResults2" style="grid-column:1/-1;color:#999;"><i class="fas fa-search" style="font-size:2.4rem;color:#ddd"></i><p class="mt-3">No products match your filters</p></div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer style="background:#3d1f2b;text-align:center;padding:24px 20px;color:#ccc;font-size:14px;position:relative;z-index:1;">
    <?php if ($address): ?><div class="mb-2" style="color:#aaa;"><i class="fas fa-map-marker-alt"></i> <?= $address ?></div><?php endif; ?>
    <div>© Copyright <?= date('Y') ?> Tapify. All Rights Reserved.</div>
  </footer>
</div>

<!-- Cart Drawer -->
<div id="tpCartOverlay" onclick="tpCloseCart()"></div>
<div id="tpCartDrawer">
  <div class="tp-cart-head"><h5>Your Cart</h5><button class="tp-cart-close" onclick="tpCloseCart()">×</button></div>
  <div class="tp-cart-items" id="tpCartItems"></div>
  <div class="tp-cart-foot" id="tpCartFoot"></div>
</div>

<?php if ($enableTranslate): ?>
<script>function googleTranslateElementInit(){new google.translate.TranslateElement({pageLanguage:'en'},'google_translate_element');}</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php endif; ?>
<script>
const TP = { id:<?= (int)$storeId ?>, whatsapp:'<?= $waPhone ?>', currency:<?= json_encode($currency) ?>, deliveryFee:<?= $deliveryFee ?>, minOrder:<?= $minOrder ?>, template:<?= json_encode($waTemplate) ?>, priceMin:<?= (float)$priceMin ?>, priceMax:<?= (float)$priceMax ?> };
let tpCart = [];
let tpCurrentCat = 'all';
let tpFilters2 = { cats: new Set(), q:'', min:null, max:null, date:null };

(function(){
  const sel = document.getElementById('tpPriceRange2'); if(!sel) return;
  const max = Math.ceil(TP.priceMax); if(max<=0) return;
  const step = Math.max(1, Math.ceil(max/4));
  const ranges = [[0,step],[step,step*2],[step*2,step*3],[step*3,max+1]];
  sel.innerHTML = '<option value="">Select Price Range</option>' +
    ranges.map(r=>`<option value="${r[0]}-${r[1]}">${TP.currency}${r[0]} - ${TP.currency}${r[1]>max?max:r[1]}</option>`).join('');
})();

function tpScrollCats(dir){
  const track = document.getElementById('tpCatTrack');
  track.scrollBy({ left: dir * 200, behavior: 'smooth' });
}

function tpFilterCat(cat, el){
  tpCurrentCat = cat;
  document.querySelectorAll('.tp-cat-item').forEach(i => i.classList.remove('active'));
  el.classList.add('active');
  tpRender();
}

function tpShowFiltered(e){
  e.preventDefault();
  document.getElementById('tpViewMoreWrap').style.display = 'none';
  document.getElementById('tpFilteredView').classList.add('active');
  document.querySelectorAll('.tp-section-title').forEach(el => el.style.display = 'none');
  document.querySelector('.tp-cat-carousel').style.display = 'none';
  document.querySelector('.tp-products').style.display = 'none';
  tpRender2();
}

function tpRender(){
  let shown = 0;
  document.querySelectorAll('.tp-product').forEach(card => {
    let ok = true;
    if (tpCurrentCat !== 'all' && card.dataset.cat != tpCurrentCat) ok = false;
    card.style.display = ok ? '' : 'none';
    if (ok) shown++;
  });
  document.getElementById('tpNoResults').classList.toggle('d-none', shown > 0);
}

function tpApplyPriceRange2(val){
  if(!val){tpFilters2.min=null;tpFilters2.max=null;}else{const p=val.split('-');tpFilters2.min=parseFloat(p[0]);tpFilters2.max=parseFloat(p[1]);}tpRender2();
}
function tpApplyCats2(){ tpFilters2.cats = new Set([...document.querySelectorAll('.tpCatCheck2:checked')].map(c=>c.value)); tpRender2(); }
function tpSearch2(){ tpFilters2.q=(document.getElementById('tpSearch2').value||'').toLowerCase(); tpRender2(); }
function tpApplyPrice2(){ const mn=parseFloat(document.getElementById('tpMinPrice2').value),mx=parseFloat(document.getElementById('tpMaxPrice2').value); tpFilters2.min=isNaN(mn)?null:mn; tpFilters2.max=isNaN(mx)?null:mx; document.getElementById('tpPriceRange2').value=''; tpRender2(); }
function tpApplyDate2(v){ const now=Date.now()/1000, m={'3_days':3*86400,'1_week':7*86400,'1_month':30*86400,'6_months':182*86400,'1_year':365*86400}; tpFilters2.date=now-(m[v]||0); tpRender2(); }
function tpResetFilters2(){ tpFilters2={cats:new Set(),q:'',min:null,max:null,date:null}; document.getElementById('tpSearch2').value=''; document.getElementById('tpMinPrice2').value=''; document.getElementById('tpMaxPrice2').value=''; document.getElementById('tpPriceRange2').value=''; document.querySelectorAll('input[name=tpDateFilter2]').forEach(r=>r.checked=false); document.querySelectorAll('.tpCatCheck2').forEach(c=>c.checked=false); tpRender2(); }
function tpRender2(){
  let shown=0;
  document.querySelectorAll('.tp-product2').forEach(card=>{
    const price=parseFloat(card.dataset.price), created=parseInt(card.dataset.created); let ok=true;
    if(tpFilters2.cats.size && !tpFilters2.cats.has(card.dataset.cat)) ok=false;
    if(tpFilters2.q && !card.dataset.name.includes(tpFilters2.q)) ok=false;
    if(tpFilters2.min!==null && price<tpFilters2.min) ok=false;
    if(tpFilters2.max!==null && price>tpFilters2.max) ok=false;
    if(tpFilters2.date!==null && created<tpFilters2.date) ok=false;
    card.style.display=ok?'':'none'; if(ok) shown++;
  });
  document.getElementById('tpNoResults2').classList.toggle('d-none', shown>0);
}

function tpAddToCart(btn){
  const id = +btn.dataset.id;
  const ex = tpCart.find(i => i.id === id);
  if (ex) ex.qty++; else tpCart.push({ id, name: btn.dataset.name, price: +btn.dataset.price, img: btn.dataset.img, qty: 1 });
  tpUpdateCart(); tpToast('Added to cart');
}
function tpChangeQty(id, d){ const it = tpCart.find(i => i.id === id); if (!it) return; it.qty = Math.max(1, it.qty + d); tpUpdateCart(); tpRenderCart(); }
function tpRemove(id){ tpCart = tpCart.filter(i => i.id !== id); tpUpdateCart(); tpRenderCart(); }
function tpCount(){ return tpCart.reduce((s, i) => s + i.qty, 0); }
function tpSub(){ return tpCart.reduce((s, i) => s + i.qty * i.price, 0); }
function tpUpdateCart(){ document.getElementById('tpCartCount').textContent = tpCount(); }
function tpOpenCart(){ tpRenderCart(); document.getElementById('tpCartDrawer').classList.add('open'); document.getElementById('tpCartOverlay').style.display = 'block'; }
function tpCloseCart(){ document.getElementById('tpCartDrawer').classList.remove('open'); document.getElementById('tpCartOverlay').style.display = 'none'; }
function tpRenderCart(){
  const items = document.getElementById('tpCartItems'), foot = document.getElementById('tpCartFoot');
  if (!tpCart.length){ items.innerHTML = '<p class="text-center py-5" style="color:#999;">Your cart is empty</p>'; foot.innerHTML = ''; return; }
  items.innerHTML = tpCart.map(i => `<div class="tp-ci"><img src="${i.img || ''}" onerror="this.style.visibility='hidden'"><div style="flex:1"><div class="n">${i.name}</div><div class="p">${TP.currency}${i.price.toFixed(2)}</div><div class="tp-qty"><button onclick="tpChangeQty(${i.id},-1)">−</button><span>${i.qty}</span><button onclick="tpChangeQty(${i.id},1)">+</button><button onclick="tpRemove(${i.id})" style="margin-left:auto;color:#e11">🗑</button></div></div></div>`).join('');
  const sub = tpSub(), total = sub + TP.deliveryFee;
  foot.innerHTML = `<div class="row-l"><span>Subtotal</span><span>${TP.currency}${sub.toFixed(2)}</span></div>${TP.deliveryFee > 0 ? `<div class="row-l"><span>Delivery</span><span>${TP.currency}${TP.deliveryFee.toFixed(2)}</span></div>` : ''}<div class="row-l tot"><span>Total</span><span>${TP.currency}${total.toFixed(2)}</span></div>${sub < TP.minOrder ? `<p style="color:#e11;font-size:.8rem;text-align:center;margin:6px 0">Min order ${TP.currency}${TP.minOrder.toFixed(2)}</p>` : `<div style="margin-top:12px"><div class="tp-field"><input id="tpName" placeholder="Your name *"></div><div class="tp-field"><input id="tpPhone" placeholder="Phone / WhatsApp *"></div><div class="tp-field"><textarea id="tpAddr" rows="2" placeholder="Delivery address"></textarea></div><div class="tp-field"><textarea id="tpNotes" rows="1" placeholder="Notes (optional)"></textarea></div><button class="tp-checkout" onclick="tpPlaceOrder()"><i class="fab fa-whatsapp"></i> Order on WhatsApp</button></div>`}`;
}
function tpPlaceOrder(){
  const name = (document.getElementById('tpName').value || '').trim(), phone = (document.getElementById('tpPhone').value || '').trim();
  if (!name || !phone){ tpToast('Enter name & phone', 'err'); return; }
  const addr = document.getElementById('tpAddr').value || '', notes = document.getElementById('tpNotes').value || '';
  const sub = tpSub(), total = sub + TP.deliveryFee;
  const items = tpCart.map(i => `• ${i.name} x${i.qty} = ${TP.currency}${(i.qty * i.price).toFixed(2)}`).join('\n');
  let msg = (TP.template || '️ NEW ORDER\n\nName: {customer_name}\nPhone: {customer_phone}\n\n{items}\n\nTotal: {total}').replace('{customer_name}', name).replace('{customer_phone}', phone).replace('{items}', items).replace('{total}', TP.currency + total.toFixed(2));
  if (addr) msg += '\nAddress: ' + addr; if (notes) msg += '\nNotes: ' + notes;
  fetch('/store-order-submit.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ store_id: TP.id, customer_name: name, customer_phone: phone, customer_address: addr, items: tpCart, subtotal: sub, delivery_charge: TP.deliveryFee, total_amount: total, notes: notes }) }).catch(() => {});
  window.location.href = `https://wa.me/${TP.whatsapp}?text=${encodeURIComponent(msg)}`;
}
function tpToast(m, t){ const e = document.createElement('div'); e.className = 'tp-toast'; if (t === 'err') e.style.background = '#c0392b'; e.textContent = m; document.body.appendChild(e); setTimeout(() => e.classList.add('show'), 10); setTimeout(() => { e.classList.remove('show'); setTimeout(() => e.remove(), 350); }, 2200); }
</script>
</body>
</html>
