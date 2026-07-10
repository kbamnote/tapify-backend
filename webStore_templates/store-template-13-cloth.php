<?php
/**
 * TAPIFY - WhatsApp Store Template 13 — "Cloth Store" (v2, full-width listing)
 * Converted from webStoreTemps/Theme 5. Renders shared $store/$categories/$products.
 */
$cfg   = $templateConfig ?? [];
$asset = '/webStore_templates/assets/store_template_13';
$defC  = $cfg['default_colors'] ?? ['primary' => '#27262e', 'secondary' => '#27262e', 'accent' => '#c9a24a'];
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

$cartAddIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 30 30" fill="none"><path d="M23.6158 19.09 24.49 14.35a.63.63 0 0 0-.73-.73c-.41.09-.84.13-1.26.13a6.25 6.25 0 0 1-6.22-5.68.62.62 0 0 0-.62-.67H6.86l-.09-.41A1.87 1.87 0 0 0 4.94 5.63H3.16a.62.62 0 0 0-.03 1.25h1.81c.29 0 .54.2.6.48l3.39 15.25a2.5 2.5 0 1 0 3.06 1.14h7.86a2.5 2.5 0 1 0 2.21-1.25H10.19l-.42-1.87h12a1.87 1.87 0 0 0 1.84-1.54Z" fill="currentColor"/></svg>';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $seoTitle ?></title>
<?php if (!empty($store['seo_description'])): ?><meta name="description" content="<?= htmlspecialchars($store['seo_description']) ?>"><?php endif; ?>
<link rel="icon" href="<?= $favicon ?>" type="image/png">
<link rel="stylesheet" href="<?= $asset ?>/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.d-none{display:none!important;}
:root{--tp:<?= $P ?>;--ts:<?= $S ?>;--ta:<?= $A ?>;}
.btn-primary{background-color:var(--tp)!important;border:1px solid var(--tp)!important;color:#fff!important;}
.btn-primary:hover,.btn-primary:focus{background-color:var(--tp)!important;border-color:var(--tp)!important;filter:brightness(.94);}
.form-check-input:checked{background-color:var(--tp)!important;border-color:var(--tp)!important;}
.form-control:focus{border-color:var(--tp)!important;box-shadow:none!important;}
#tpCartOverlay{display:none;position:fixed;inset:0;background:rgba(10,20,40,.5);z-index:1200;}
#tpCartDrawer{position:fixed;top:0;right:0;height:100%;width:100%;max-width:400px;background:#fff;z-index:1201;transform:translateX(105%);transition:transform .35s ease;display:flex;flex-direction:column;box-shadow:-10px 0 40px rgba(0,0,0,.15);}
#tpCartDrawer.open{transform:none;}
.tp-cart-head{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid #eee;}
.tp-cart-head h5{margin:0;font-weight:700;}
.tp-cart-close{background:#f3f3f3;border:none;width:34px;height:34px;border-radius:50%;font-size:1.2rem;cursor:pointer;}
.tp-cart-items{flex:1;overflow-y:auto;padding:10px 20px;}
.tp-ci{display:flex;gap:12px;padding:12px 0;border-bottom:1px solid #f4f4f4;}
.tp-ci img{width:56px;height:56px;object-fit:cover;border-radius:8px;background:#f4f4f4;}
.tp-ci .n{font-weight:600;font-size:.9rem;}
.tp-ci .p{color:var(--tp);font-weight:700;}
.tp-qty{display:flex;align-items:center;gap:8px;margin-top:6px;}
.tp-qty button{width:26px;height:26px;border:1px solid #ddd;background:#fff;border-radius:50%;cursor:pointer;}
.tp-cart-foot{padding:16px 20px;border-top:1px solid #eee;}
.tp-cart-foot .row-l{display:flex;justify-content:space-between;padding:3px 0;font-size:.92rem;}
.tp-cart-foot .tot{font-weight:700;font-size:1.05rem;}
.tp-field{margin-bottom:10px;}
.tp-field input,.tp-field textarea{width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:8px;font-size:.9rem;}
.tp-checkout{width:100%;padding:13px;border:none;border-radius:10px;background:#25D366;color:#fff;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;}
.tp-toast{position:fixed;top:20px;left:50%;transform:translateX(-50%) translateY(-90px);background:#111;color:#fff;padding:11px 22px;border-radius:50px;font-weight:600;z-index:9999;transition:transform .35s;}
.tp-toast.show{transform:translateX(-50%) translateY(0);}
.product-card .addToCartBtn{min-width:44px;height:44px;}
</style>
</head>
<body style="position:relative;min-height:100%;top:0px">
<div class="main-content mx-auto w-100 overflow-hidden d-flex flex-column justify-content-between">
  <div>
    <nav class="navbar navbar-expand-lg px-50 position-relative">
      <div class="container-fluid p-0">
        <div class="d-flex align-items-center gap-3">
          <a class="navbar-brand p-0 m-0" href="#">
            <?php if ($logo): ?><img src="<?= $logo ?>" alt="logo" class="w-100 h-100 object-fit-cover">
            <?php else: ?><span class="fw-7 fs-20" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;background:var(--tp);color:#fff;border-radius:50%"><?= $initial ?></span><?php endif; ?>
          </a>
          <span class="fw-5 fs-20"><a href="#" style="color:#212529;text-decoration:none"><?= $shopName ?></a></span>
        </div>
        <div class="d-flex align-items-center gap-lg-4 gap-sm-3 gap-2">
          <?php if ($enableTranslate): ?><div class="language-dropdown position-relative"><div id="google_translate_element"></div></div><?php endif; ?>
          <button id="addToCartViewBtn" class="add-to-cart-btn d-flex align-items-center justify-content-center position-relative" type="button" onclick="tpOpenCart()">
            <div class="position-absolute cart-count d-flex align-items-center justify-content-center product-count-badge" id="tpCartCount">0</div>
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M27.0834 11.6668C27.0834 11.9984 26.9517 12.3163 26.7172 12.5507C26.4828 12.7851 26.1649 12.9168 25.8334 12.9168C25.5018 12.9168 25.1839 12.7851 24.9495 12.5507C24.7151 12.3163 24.5834 11.9984 24.5834 11.6668V9.16683C24.5834 7.95125 24.1005 6.78546 23.2409 5.92592C22.3814 5.06638 21.2156 4.5835 20 4.5835C18.7844 4.5835 17.6187 5.06638 16.7591 5.92592C15.8996 6.78546 15.4167 7.95125 15.4167 9.16683V11.6668C15.4167 11.9984 15.285 12.3163 15.0506 12.5507C14.8161 12.7851 14.4982 12.9168 14.1667 12.9168C13.8352 12.9168 13.5172 12.7851 13.2828 12.5507C13.0484 12.3163 12.9167 11.9984 12.9167 11.6668V9.16683C12.9167 7.28821 13.663 5.48654 14.9913 4.15816C16.3197 2.82977 18.1214 2.0835 20 2.0835C21.8786 2.0835 23.6803 2.82977 25.0087 4.15816C26.3371 5.48654 27.0834 7.28821 27.0834 9.16683V11.6668Z" fill="black"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M32.2367 13.917L33.57 33.917C33.6035 34.4292 33.5316 34.9428 33.3588 35.4262C33.186 35.9095 32.9159 36.3523 32.5653 36.7272C32.2146 37.102 31.7908 37.401 31.3201 37.6057C30.8493 37.8103 30.3416 37.9163 29.8283 37.917H10.1717C9.65823 37.9167 9.1503 37.8111 8.67933 37.6066C8.20836 37.4022 7.78437 37.1032 7.43362 36.7282C7.08287 36.3532 6.81282 35.9103 6.64019 35.4267C6.46756 34.9432 6.39603 34.4293 6.43001 33.917L7.76334 13.917C7.82676 12.9678 8.24855 12.0782 8.94327 11.4284C9.63799 10.7785 10.5537 10.417 11.505 10.417H28.495C29.4463 10.417 30.362 10.7785 31.0568 11.4284C31.7515 12.0782 32.1733 12.9678 32.2367 13.917ZM24.1433 17.797C23.7707 18.5803 23.1837 19.2418 22.4504 19.7051C21.717 20.1683 20.8674 20.4141 20 20.4141C19.1326 20.4141 18.283 20.1683 17.5497 19.7051C16.8163 19.2418 16.2293 18.5803 15.8567 17.797C15.7862 17.6487 15.6872 17.5158 15.5654 17.4057C15.4435 17.2957 15.3012 17.2108 15.1465 17.1557C14.9918 17.1007 14.8278 17.0767 14.6638 17.0851C14.4998 17.0934 14.3391 17.134 14.1908 17.2045C14.0426 17.275 13.9096 17.374 13.7996 17.4958C13.6896 17.6177 13.6046 17.76 13.5496 17.9147C13.4946 18.0694 13.4706 18.2334 13.4789 18.3974C13.4873 18.5613 13.5279 18.722 13.5983 18.8703C14.1724 20.0824 15.0788 21.1066 16.212 21.8238C17.3453 22.541 18.6589 22.9218 20 22.9218C21.3412 22.9218 22.6547 22.541 23.788 21.8238C24.9213 21.1066 25.8276 20.0824 26.4017 18.8703C26.4722 18.722 26.5127 18.5613 26.5211 18.3974C26.5295 18.2334 26.5055 18.0694 26.4504 17.9147C26.3954 17.76 26.3105 17.6177 26.2004 17.4958C26.0904 17.374 25.9575 17.275 25.8092 17.2045C25.6609 17.134 25.5002 17.0934 25.3362 17.0851C25.1722 17.0767 25.0082 17.1007 24.8536 17.1557C24.6989 17.2108 24.5565 17.2957 24.4347 17.4057C24.3128 17.5158 24.2138 17.6487 24.1433 17.797Z" fill="black"></path></svg>
          </button>
        </div>
      </div>
    </nav>

    <div class="banner-section position-relative mb-30"><div class="banner-img"><img src="<?= $bannerImg ?>" class="w-100 h-100 object-fit-cover" alt="banner" onerror="this.style.display='none'"></div></div>

    <div class="items-section px-3 pt-3 mt-1 position-relative">
      <div class="row">
        <div class="col-xl-3 col-lg-4 mb-40">
          <div class="items-tabs bg-white">
            <div class="tabs-heading mb-20"><p class="fs-20 fw-6 text-black mb-0 lh-sm">Filter</p></div>
            <div class="row mx-0 mb-30">
              <div class="col-4 ps-0 px-1"><input type="number" min="0" id="tpMinPrice" class="form-control" placeholder="Min"></div>
              <div class="col-4 px-1"><input type="number" min="1" id="tpMaxPrice" class="form-control" placeholder="Max"></div>
              <div class="col-4 pe-0 px-1"><button class="apply-btn btn btn-primary w-100 h-100" type="button" onclick="tpApplyPrice()">Apply</button></div>
            </div>
            <div class="mb-20 date-posted">
              <div class="heading-text mb-20"><h3 class="mb-0 fs-20 fw-6">Date Posted</h3></div>
              <div>
                <?php $__dates = ['3_days'=>'3 Days Ago','1_week'=>'1 Week Ago','1_month'=>'1 Month Ago','6_months'=>'6 Months Ago','1_year'=>'1 Year Ago']; $__i=0; foreach ($__dates as $val=>$lbl): $__i++; ?>
                <div class="form-check mb-2"><input class="form-check-input" type="radio" name="tpDateFilter" id="tpDate<?= $__i ?>" value="<?= $val ?>" onchange="tpApplyDate('<?= $val ?>')"><label class="form-check-label fs-20 fw-5 text-black lh-sm" for="tpDate<?= $__i ?>"><?= $lbl ?></label></div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="date-posted">
              <div class="heading-text mb-20"><h3 class="mb-0 fs-20 fw-6">All Categories</h3></div>
              <div id="tpCategoryList">
                <?php foreach ($categories as $c): ?>
                <div class="form-check mb-2">
                  <input class="form-check-input tpCatCheck" type="checkbox" value="<?= (int)$c['id'] ?>" id="tpCat<?= (int)$c['id'] ?>" onchange="tpApplyCats()">
                  <label class="form-check-label fs-20 fw-5 text-black lh-sm" for="tpCat<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></label>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="mt-4"><button type="button" class="apply-btn btn btn-primary w-100" onclick="tpResetFilters()">Reset Filters</button></div>
          </div>
        </div>

        <div class="col-xl-9 col-lg-8">
          <div class="row justify-content-end">
            <div class="col-xl-4 col-sm-6 mb-20">
              <div class="position-relative">
                <input type="search" id="tpSearch" placeholder="Search For Products" class="form-control ps-45" oninput="tpSearch()">
                <div class="search-icon"><i class="fas fa-search" style="color:#999"></i></div>
              </div>
            </div>
            <div class="col-xl-4 col-sm-6 mb-20">
              <div class="dropdown">
                <button class="btn btn-light text-start w-100 border" type="button" id="customSelectBtn" data-bs-toggle="dropdown" style="height:100%">Select Price Range</button>
                <ul class="dropdown-menu w-100" id="customSelectMenu"></ul>
              </div>
            </div>
            <div class="section-heading position-relative"><h2>All Items</h2></div>
            <div class="row mb-40 product-section" id="tpProductsRow">
              <?php if (!empty($products)): foreach ($products as $p): $pimg = $p['img'] ?? ''; ?>
              <div class="col-xl-3 col-sm-6 mb-20 tp-product"
                   data-cat="<?= (int)$p['category_id'] ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
                   data-price="<?= (float)$p['effective_price'] ?>" data-created="<?= strtotime($p['created_at'] ?? 'now') ?>">
                <div class="h-100 product-card bg-white d-flex flex-column">
                  <div class="d-flex flex-column h-100 text-black" style="text-decoration:none">
                    <div class="product-img w-100 h-100 m-auto">
                      <?php if ($pimg): ?><img src="<?= $pimg ?>" alt="product" class="w-100 h-100 object-fit-cover product-image">
                      <?php else: ?><div class="w-100 d-flex align-items-center justify-content-center" style="aspect-ratio:1;background:#eef1fb;color:#9fb0e0;font-size:2.4rem"><i class="fas fa-box-open"></i></div><?php endif; ?>
                    </div>
                    <div class="product-details" style="flex-grow:1">
                      <div class="d-flex justify-content-between h-100 flex-column">
                        <div>
                          <h5 class="fs-20 fw-6 mb-1 product-name"><?= htmlspecialchars($p['name']) ?></h5>
                          <?php if (!empty($p['category_name'])): ?><p class="fs-14 fw-5 mb-2 text-gray-200 lh-sm product-category"><?= htmlspecialchars($p['category_name']) ?></p><?php endif; ?>
                        </div>
                        <div class="d-flex gap-2 align-items-center justify-content-between">
                          <p class="fs-30 fw-6 lh-sm mb-0">
                            <span class="currency_icon"><?= $currency ?></span>
                            <span class="selling_price"><?= number_format($p['effective_price'], 2) ?></span>
                            <?php if (!empty($p['has_discount'])): ?><del class="fs-20 fw-5 text-gray-200 text-nowrap"><?= $currency ?> <?= number_format((float)$p['price'], 2) ?></del><?php endif; ?>
                          </p>
                          <?php if (!empty($p['in_stock'])): ?>
                          <button class="btn btn-primary d-flex justify-content-center align-items-center addToCartBtn" title="<?= htmlspecialchars($cta, ENT_QUOTES) ?>"
                                  data-id="<?= (int)$p['id'] ?>" data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                                  data-price="<?= (float)$p['effective_price'] ?>" data-img="<?= htmlspecialchars($pimg, ENT_QUOTES) ?>" onclick="tpAddToCart(this)"><?= $cartAddIcon ?></button>
                          <?php else: ?><button class="btn btn-primary addToCartBtn" disabled style="opacity:.5">✕</button><?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; else: ?>
              <div class="col-12 text-center py-5"><i class="fas fa-box-open" style="font-size:3rem;color:#9fb0e0"></i><p class="mt-3 fs-18">No products available yet</p></div>
              <?php endif; ?>
              <div class="col-12 text-center py-5 d-none" id="tpNoResults"><i class="fas fa-search" style="font-size:2.4rem;color:#9fb0e0"></i><p class="mt-3 fs-16">No products match your filters</p></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="position-relative">
    <div class="text-center fw-5 fs-16">
      <?php if ($address): ?><div class="mb-2"><i class="fas fa-map-marker-alt"></i> <?= $address ?></div><?php endif; ?>
      <div>© Copyright <?= date('Y') ?> Tapify. All Rights Reserved.</div>
    </div>
  </footer>
</div>

<div id="tpCartOverlay" onclick="tpCloseCart()"></div>
<div id="tpCartDrawer">
  <div class="tp-cart-head"><h5>🛒 Your Cart</h5><button class="tp-cart-close" onclick="tpCloseCart()">×</button></div>
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
let tpFilters = { cats: new Set(), q:'', min:null, max:null, date:null };
(function(){
  const menu = document.getElementById('customSelectMenu'); if(!menu) return;
  const max = Math.ceil(TP.priceMax); if(max<=0) return;
  const step = Math.max(1, Math.ceil(max/4));
  const ranges = [[0,step],[step,step*2],[step*2,step*3],[step*3,max+1]];
  menu.innerHTML = '<li><a class="dropdown-item" href="#" data-min="" data-max="">All Prices</a></li>' +
    ranges.map(r=>`<li><a class="dropdown-item" href="#" data-min="${r[0]}" data-max="${r[1]}">${TP.currency}${r[0]} - ${TP.currency}${r[1]>max?max:r[1]}</a></li>`).join('');
  menu.querySelectorAll('a').forEach(a=>a.addEventListener('click',e=>{e.preventDefault();document.getElementById('customSelectBtn').textContent=a.textContent;tpFilters.min=a.dataset.min===''?null:parseFloat(a.dataset.min);tpFilters.max=a.dataset.max===''?null:parseFloat(a.dataset.max);tpRender();}));
})();
function tpApplyCats(){ tpFilters.cats = new Set([...document.querySelectorAll('.tpCatCheck:checked')].map(c=>c.value)); tpRender(); }
function tpSearch(){ tpFilters.q=(document.getElementById('tpSearch').value||'').toLowerCase(); tpRender(); }
function tpApplyPrice(){ const mn=parseFloat(document.getElementById('tpMinPrice').value),mx=parseFloat(document.getElementById('tpMaxPrice').value); tpFilters.min=isNaN(mn)?null:mn; tpFilters.max=isNaN(mx)?null:mx; tpRender(); }
function tpApplyDate(v){ const now=Date.now()/1000, m={'3_days':3*86400,'1_week':7*86400,'1_month':30*86400,'6_months':182*86400,'1_year':365*86400}; tpFilters.date=now-(m[v]||0); tpRender(); }
function tpResetFilters(){ tpFilters={cats:new Set(),q:'',min:null,max:null,date:null}; document.getElementById('tpSearch').value=''; document.getElementById('tpMinPrice').value=''; document.getElementById('tpMaxPrice').value=''; document.querySelectorAll('input[name=tpDateFilter]').forEach(r=>r.checked=false); document.querySelectorAll('.tpCatCheck').forEach(c=>c.checked=false); document.getElementById('customSelectBtn').textContent='Select Price Range'; tpRender(); }
function tpRender(){
  let shown=0;
  document.querySelectorAll('.tp-product').forEach(card=>{
    const price=parseFloat(card.dataset.price), created=parseInt(card.dataset.created); let ok=true;
    if(tpFilters.cats.size && !tpFilters.cats.has(card.dataset.cat)) ok=false;
    if(tpFilters.q && !card.dataset.name.includes(tpFilters.q)) ok=false;
    if(tpFilters.min!==null && price<tpFilters.min) ok=false;
    if(tpFilters.max!==null && price>tpFilters.max) ok=false;
    if(tpFilters.date!==null && created<tpFilters.date) ok=false;
    card.style.display=ok?'':'none'; if(ok) shown++;
  });
  document.getElementById('tpNoResults').classList.toggle('d-none', shown>0);
}
function tpAddToCart(btn){ const id=+btn.dataset.id; const ex=tpCart.find(i=>i.id===id); if(ex) ex.qty++; else tpCart.push({id,name:btn.dataset.name,price:+btn.dataset.price,img:btn.dataset.img,qty:1}); tpUpdateCart(); tpToast('✓ Added to cart'); }
function tpChangeQty(id,d){ const it=tpCart.find(i=>i.id===id); if(!it)return; it.qty=Math.max(1,it.qty+d); tpUpdateCart(); tpRenderCart(); }
function tpRemove(id){ tpCart=tpCart.filter(i=>i.id!==id); tpUpdateCart(); tpRenderCart(); }
function tpCount(){ return tpCart.reduce((s,i)=>s+i.qty,0); }
function tpSub(){ return tpCart.reduce((s,i)=>s+i.qty*i.price,0); }
function tpUpdateCart(){ document.getElementById('tpCartCount').textContent=tpCount(); }
function tpOpenCart(){ tpRenderCart(); document.getElementById('tpCartDrawer').classList.add('open'); document.getElementById('tpCartOverlay').style.display='block'; }
function tpCloseCart(){ document.getElementById('tpCartDrawer').classList.remove('open'); document.getElementById('tpCartOverlay').style.display='none'; }
function tpRenderCart(){
  const items=document.getElementById('tpCartItems'), foot=document.getElementById('tpCartFoot');
  if(!tpCart.length){ items.innerHTML='<p class="text-center text-muted py-5">Your cart is empty</p>'; foot.innerHTML=''; return; }
  items.innerHTML=tpCart.map(i=>`<div class="tp-ci"><img src="${i.img||''}" onerror="this.style.visibility='hidden'"><div style="flex:1"><div class="n">${i.name}</div><div class="p">${TP.currency}${i.price.toFixed(2)}</div><div class="tp-qty"><button onclick="tpChangeQty(${i.id},-1)">−</button><span>${i.qty}</span><button onclick="tpChangeQty(${i.id},1)">+</button><button onclick="tpRemove(${i.id})" style="margin-left:auto;color:#e11">🗑</button></div></div></div>`).join('');
  const sub=tpSub(), total=sub+TP.deliveryFee;
  foot.innerHTML=`<div class="row-l"><span>Subtotal</span><span>${TP.currency}${sub.toFixed(2)}</span></div>${TP.deliveryFee>0?`<div class="row-l"><span>Delivery</span><span>${TP.currency}${TP.deliveryFee.toFixed(2)}</span></div>`:''}<div class="row-l tot"><span>Total</span><span>${TP.currency}${total.toFixed(2)}</span></div>${sub<TP.minOrder?`<p style="color:#e11;font-size:.8rem;text-align:center;margin:6px 0">Min order ${TP.currency}${TP.minOrder.toFixed(2)}</p>`:`<div style="margin-top:12px"><div class="tp-field"><input id="tpName" placeholder="Your name *"></div><div class="tp-field"><input id="tpPhone" placeholder="Phone / WhatsApp *"></div><div class="tp-field"><textarea id="tpAddr" rows="2" placeholder="Delivery address"></textarea></div><div class="tp-field"><textarea id="tpNotes" rows="1" placeholder="Notes (optional)"></textarea></div><button class="tp-checkout" onclick="tpPlaceOrder()"><i class="fab fa-whatsapp"></i> Order on WhatsApp</button></div>`}`;
}
function tpPlaceOrder(){
  const name=(document.getElementById('tpName').value||'').trim(), phone=(document.getElementById('tpPhone').value||'').trim();
  if(!name||!phone){ tpToast('Enter name & phone','err'); return; }
  const addr=document.getElementById('tpAddr').value||'', notes=document.getElementById('tpNotes').value||'';
  const sub=tpSub(), total=sub+TP.deliveryFee;
  const items=tpCart.map(i=>`• ${i.name} x${i.qty} = ${TP.currency}${(i.qty*i.price).toFixed(2)}`).join('\n');
  let msg=(TP.template||'🛍️ NEW ORDER\n\nName: {customer_name}\nPhone: {customer_phone}\n\n{items}\n\nTotal: {total}').replace('{customer_name}',name).replace('{customer_phone}',phone).replace('{items}',items).replace('{total}',TP.currency+total.toFixed(2));
  if(addr) msg+='\nAddress: '+addr; if(notes) msg+='\nNotes: '+notes;
  fetch('/store-order-submit.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({store_id:TP.id,customer_name:name,customer_phone:phone,customer_address:addr,items:tpCart,subtotal:sub,delivery_charge:TP.deliveryFee,total_amount:total,notes:notes})}).catch(()=>{});
  window.location.href=`https://wa.me/${TP.whatsapp}?text=${encodeURIComponent(msg)}`;
}
function tpToast(m,t){ const e=document.createElement('div'); e.className='tp-toast'; if(t==='err') e.style.background='#c0392b'; e.textContent=m; document.body.appendChild(e); setTimeout(()=>e.classList.add('show'),10); setTimeout(()=>{e.classList.remove('show');setTimeout(()=>e.remove(),350);},2200); }
</script>
</body>
</html>
