<?php
/**
 * TAPIFY - WhatsApp Store Template 9 — "Ethereal Beauty" (v2, full-width listing)
 * Converted from webStoreTemps/Theme 1 (static export) to a dynamic template.
 * Renders the SHARED store data ($store/$categories/$products) — switching to
 * this template changes only the UI, never the data.
 *
 * Provided by store.php: $store, $categories, $products (enriched: img,
 * category_name, effective_price, has_discount, disc_pct), $currency,
 * $storeId, $templateConfig, $priceMin, $priceMax, imgUrl().
 */

$cfg      = $templateConfig ?? [];
$asset    = '/webStore_templates/assets/store_template_9';
$defC     = $cfg['default_colors'] ?? ['primary' => '#c29c77', 'secondary' => '#3d1f2b', 'accent' => '#e9c2cf', 'text' => '#2d0a18'];

// Treat the legacy WhatsApp-green scheme as "unset" so the theme's native palette wins.
$legacy   = ['#25D366', '#128C7E', '', null];
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

// Cart add-to-cart SVG (matches export)
$cartIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none"><path d="M8.28564 7.41444C8.28564 5.36304 9.94849 3.7002 11.9999 3.7002C14.0513 3.7002 15.7141 5.36304 15.7141 7.41444C15.7141 7.56599 15.6539 7.71134 15.5468 7.8185C15.4396 7.92566 15.2943 7.98587 15.1427 7.98587C14.9912 7.98587 14.8458 7.92566 14.7387 7.8185C14.6315 7.71134 14.5713 7.56599 14.5713 7.41444C14.5713 6.73247 14.3004 6.07842 13.8181 5.59619C13.3359 5.11396 12.6819 4.84304 11.9999 4.84304C11.3179 4.84304 10.6639 5.11396 10.1816 5.59619C9.69941 6.07842 9.42849 6.73247 9.42849 7.41444C9.42849 7.56599 9.36829 7.71134 9.26112 7.8185C9.15396 7.92566 9.00862 7.98587 8.85707 7.98587C8.70552 7.98587 8.56017 7.92566 8.45301 7.8185C8.34585 7.71134 8.28564 7.56599 8.28564 7.41444ZM12.5713 12.5572C12.5713 12.4057 12.5111 12.2604 12.404 12.1532C12.2968 12.046 12.1514 11.9858 11.9999 11.9858C11.8483 11.9858 11.703 12.046 11.5958 12.1532C11.4887 12.2604 11.4285 12.4057 11.4285 12.5572V14.2715H9.7142C9.56265 14.2715 9.41731 14.3317 9.31014 14.4389C9.20298 14.546 9.14278 14.6914 9.14278 14.8429C9.14278 14.9945 9.20298 15.1398 9.31014 15.247C9.41731 15.3542 9.56265 15.4144 9.7142 15.4144H11.4285V17.1286C11.4285 17.2802 11.4887 17.4255 11.5958 17.5327C11.703 17.6399 11.8483 17.7001 11.9999 17.7001C12.1514 17.7001 12.2968 17.6399 12.404 17.5327C12.5111 17.4255 12.5713 17.2802 12.5713 17.1286V15.4144H14.2856C14.4371 15.4144 14.5825 15.3542 14.6896 15.247C14.7968 15.1398 14.857 14.9945 14.857 14.8429C14.857 14.6914 14.7968 14.546 14.6896 14.4389C14.5825 14.3317 14.4371 14.2715 14.2856 14.2715H12.5713V12.5572Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M5.20225 10.7719C5.28737 10.234 5.56165 9.74422 5.97574 9.39058C6.38982 9.03693 6.91653 8.84268 7.46108 8.84277H16.5384C17.083 8.84261 17.6098 9.03683 18.0239 9.39049C18.4381 9.74414 18.7124 10.234 18.7975 10.7719L19.9715 18.2004C20.1907 19.5884 19.117 20.8427 17.7124 20.8427H6.28738C4.88282 20.8427 3.80912 19.5884 4.02854 18.2004L5.20225 10.7719ZM7.46108 9.98562C7.18873 9.98552 6.92529 10.0826 6.71814 10.2594C6.511 10.4363 6.37375 10.6812 6.33109 10.9502L5.1571 18.3787C5.13137 18.5419 5.14134 18.7088 5.18632 18.8678C5.23131 19.0269 5.31024 19.1742 5.41767 19.2998C5.52511 19.4254 5.65849 19.5262 5.80864 19.5952C5.95878 19.6643 6.12211 19.7 6.28738 19.6998H17.7124C17.8777 19.6999 18.041 19.6642 18.1911 19.5951C18.3412 19.5261 18.4746 19.4253 18.582 19.2997C18.6894 19.1741 18.7684 19.0268 18.8134 18.8678C18.8584 18.7088 18.8684 18.5419 18.8427 18.3787L17.6687 10.9502C17.626 10.6811 17.4887 10.4362 17.2815 10.2593C17.0743 10.0825 16.8108 9.98546 16.5384 9.98562H7.46108Z" fill="currentColor"></path></svg>';
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
/* ── utilities the tree-shaken export CSS may omit ── */
.d-none{display:none!important;}
/* ── Dynamic brand colours (native theme default = #c29c77) ── */
:root{--tp:<?= $P ?>;--ts:<?= $S ?>;--ta:<?= $A ?>;}
.btn-primary{background-color:var(--tp)!important;border:1px solid var(--tp)!important;color:#fff!important;}
.btn-primary:hover,.btn-primary:focus{background-color:var(--tp)!important;border-color:var(--tp)!important;filter:brightness(.94);}
.category-button:hover,.category-button.active{color:var(--tp)!important;}
.category-button.active{font-weight:700;}
.serach-dropdown:focus,.form-control:focus{border-color:var(--tp)!important;box-shadow:none!important;}
/* ── Cart drawer (added — export rendered it via Livewire) ── */
#tpCartOverlay{display:none;position:fixed;inset:0;background:rgba(20,10,15,.5);z-index:1200;}
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
.tp-toast{position:fixed;top:20px;left:50%;transform:translateX(-50%) translateY(-90px);background:#2d0a18;color:#fff;padding:11px 22px;border-radius:50px;font-weight:600;z-index:9999;transition:transform .35s;}
.tp-toast.show{transform:translateX(-50%) translateY(0);}
.item-card{cursor:default;}
</style>
</head>
<body style="position:relative;min-height:100%;top:0px">
<div class="main-content mx-auto w-100 overflow-hidden d-flex flex-column justify-content-between">

  <!-- decorative background vectors -->
  <div class="bg-vector bg-vector-1"><img src="<?= $asset ?>/bg-vector.webp" alt="vector" loading="lazy"></div>
  <div class="bg-vector bg-vector-3"><img src="<?= $asset ?>/bg-vector_2.webp" alt="vector" loading="lazy"></div>
  <div class="bg-vector bg-vector-4"><img src="<?= $asset ?>/bg-vector_3.webp" alt="vector" loading="lazy"></div>
  <div class="bg-vector bg-vector-5"><img src="<?= $asset ?>/bg-vector_4.webp" alt="vector" loading="lazy"></div>
  <div class="bg-vector bg-vector-6"><img src="<?= $asset ?>/bg-vector_5.webp" alt="vector" loading="lazy"></div>

  <div>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg px-50 position-relative">
      <div class="container-fluid p-0">
        <div class="d-flex align-items-center gap-3">
          <a class="navbar-brand p-0 m-0" href="#">
            <?php if ($logo): ?>
              <img src="<?= $logo ?>" alt="logo" class="w-100 h-100 object-fit-cover">
            <?php else: ?>
              <span class="fw-7 fs-20" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;background:var(--tp);color:#fff;border-radius:50%"><?= $initial ?></span>
            <?php endif; ?>
          </a>
          <span class="fw-6 fs-18"><a href="#" style="color:#212529;text-decoration:none"><?= $shopName ?></a></span>
        </div>
        <div class="d-flex align-items-center gap-lg-4 gap-sm-3 gap-2">
          <?php if ($enableTranslate): ?>
          <div class="language-dropdown position-relative">
            <div id="google_translate_element"></div>
          </div>
          <?php endif; ?>
          <button class="add-to-cart-btn d-flex align-items-center justify-content-center position-relative" id="addToCartViewBtn" type="button" onclick="tpOpenCart()">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M20.0048 9.03985C20.0048 9.27694 19.9106 9.50433 19.7429 9.67198C19.5753 9.83964 19.3479 9.93382 19.1108 9.93382C18.8737 9.93382 18.6463 9.83964 18.4787 9.67198C18.311 9.50433 18.2168 9.27694 18.2168 9.03985V7.2519C18.2168 6.38254 17.8715 5.54879 17.2567 4.93406C16.642 4.31934 15.8083 3.97399 14.9389 3.97399C14.0696 3.97399 13.2358 4.31934 12.6211 4.93406C12.0063 5.54879 11.661 6.38254 11.661 7.2519V9.03985C11.661 9.27694 11.5668 9.50433 11.3992 9.67198C11.2315 9.83964 11.0041 9.93382 10.767 9.93382C10.5299 9.93382 10.3025 9.83964 10.1349 9.67198C9.96723 9.50433 9.87305 9.27694 9.87305 9.03985V7.2519C9.87305 5.90835 10.4068 4.61982 11.3568 3.66979C12.3068 2.71976 13.5954 2.18604 14.9389 2.18604C16.2825 2.18604 17.571 2.71976 18.521 3.66979C19.471 4.61982 20.0048 5.90835 20.0048 7.2519V9.03985Z" fill="#292929"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M23.6898 10.6489L24.6434 24.9525C24.6674 25.3188 24.616 25.6862 24.4924 26.0318C24.3688 26.3775 24.1756 26.6942 23.9249 26.9623C23.6741 27.2304 23.371 27.4442 23.0343 27.5905C22.6977 27.7369 22.3346 27.8127 21.9675 27.8132H7.90939C7.54218 27.813 7.17892 27.7375 6.84209 27.5913C6.50526 27.445 6.20204 27.2312 5.95119 26.963C5.70034 26.6948 5.5072 26.378 5.38374 26.0322C5.26028 25.6864 5.20912 25.3189 5.23342 24.9525L6.187 10.6489C6.23235 9.97006 6.534 9.33384 7.03086 8.86907C7.52771 8.40431 8.18262 8.14575 8.86296 8.14575H21.0139C21.6942 8.14575 22.3491 8.40431 22.846 8.86907C23.3428 9.33384 23.6445 9.97006 23.6898 10.6489ZM17.9017 13.4238C17.6351 13.984 17.2153 14.4571 16.6909 14.7884C16.1664 15.1197 15.5588 15.2955 14.9384 15.2955C14.3181 15.2955 13.7105 15.1197 13.186 14.7884C12.6615 14.4571 12.2417 13.984 11.9752 13.4238C11.9248 13.3177 11.854 13.2227 11.7668 13.144C11.6797 13.0653 11.5779 13.0045 11.4673 12.9652C11.3566 12.9258 11.2393 12.9086 11.1221 12.9146C11.0048 12.9206 10.8899 12.9496 10.7838 13C10.6778 13.0504 10.5827 13.1212 10.504 13.2084C10.4253 13.2955 10.3646 13.3973 10.3252 13.508C10.2859 13.6186 10.2687 13.7359 10.2747 13.8532C10.2807 13.9704 10.3097 14.0854 10.3601 14.1914C10.7706 15.0583 11.4188 15.7908 12.2293 16.3037C13.0398 16.8166 13.9793 17.0889 14.9384 17.0889C15.8976 17.0889 16.837 16.8166 17.6475 16.3037C18.458 15.7908 19.1062 15.0583 19.5168 14.1914C19.5672 14.0854 19.5962 13.9704 19.6022 13.8532C19.6082 13.7359 19.591 13.6186 19.5516 13.508C19.5123 13.3973 19.4515 13.2955 19.3728 13.2084C19.2942 13.1212 19.1991 13.0504 19.093 13C18.987 12.9496 18.872 12.9206 18.7548 12.9146C18.6375 12.9086 18.5202 12.9258 18.4096 12.9652C18.2989 13.0045 18.1972 13.0653 18.11 13.144C18.0229 13.2226 17.9521 13.3177 17.9017 13.4238Z" fill="#292929"></path></svg>
            <span class="position-absolute start-100 translate-middle badge rounded-pill bg-danger product-count-badge" style="font-size:12px;padding:3px 6px;top:7px" id="tpCartCount">0</span>
          </button>
        </div>
      </div>
    </nav>

    <!-- BANNER -->
    <div class="banner-section position-relative">
      <div class="banner-img"><img src="<?= $bannerImg ?>" class="w-100 h-100 object-fit-cover" alt="banner"></div>
    </div>

    <!-- ITEMS -->
    <div class="items-section px-3 pt-3 mt-1 position-relative">
      <div class="row mb-40">

        <!-- FILTER SIDEBAR -->
        <div class="col-xl-3 col-lg-4 mb-40">
          <div class="items-tabs">
            <div class="row mx-0 mb-30">
              <div class="col-4 ps-0 px-1"><input type="number" min="0" id="tpMinPrice" class="form-control" placeholder="Min"></div>
              <div class="col-4 px-1"><input type="number" min="1" id="tpMaxPrice" class="form-control" placeholder="Max"></div>
              <div class="col-4 pe-0 px-1"><button class="apply-btn btn btn-primary w-100" type="button" onclick="tpApplyPrice()">Apply</button></div>
            </div>
            <div class="mb-30">
              <div class="heading-text mb-4"><h3>Date Posted</h3></div>
              <div>
                <?php $__dates = ['3_days'=>'3 Days Ago','1_week'=>'1 Week Ago','1_month'=>'1 Month Ago','6_months'=>'6 Months Ago','1_year'=>'1 Year Ago']; $__i=0; foreach ($__dates as $val=>$lbl): $__i++; ?>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="tpDateFilter" id="tpDate<?= $__i ?>" value="<?= $val ?>" onchange="tpApplyDate('<?= $val ?>')">
                  <label class="form-check-label fs-16 fw-5" for="tpDate<?= $__i ?>"><?= $lbl ?></label>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div>
              <div class="heading-text mb-3"><h3>All Categories</h3></div>
              <div id="tpCategoryList">
                <?php foreach ($categories as $c): $cimg = !empty($c['image']) ? imgUrl($c['image']) : ''; ?>
                <div class="category-item">
                  <button class="category-button w-100" type="button" data-cat="<?= (int)$c['id'] ?>" onclick="tpSetCategory(<?= (int)$c['id'] ?>, this)">
                    <div class="category-category-img">
                      <?php if ($cimg): ?><img src="<?= $cimg ?>" class="w-100 rounded" alt="category"><?php else: ?><i class="fas fa-tag" style="color:var(--tp)"></i><?php endif; ?>
                    </div>
                    <span class="text-start flex-grow-1"><?= htmlspecialchars($c['name']) ?></span>
                  </button>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="mt-5"><button type="button" class="apply-btn btn btn-primary w-100" onclick="tpResetFilters()">Reset Filters</button></div>
          </div>
        </div>

        <!-- PRODUCTS -->
        <div class="col-xl-9 col-lg-8">
          <div class="row justify-content-end">
            <div class="col-xl-4 col-sm-6 mb-20">
              <div class="position-relative">
                <input type="text" id="tpSearch" placeholder="Search For Products" class="form-control ps-45" oninput="tpSearch()">
                <div class="search-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none"><path d="M20.6677 19.8511L16.1664 15.3497C16.1661 15.3494 16.1661 15.349 16.1664 15.3488C17.1299 14.2124 17.6898 12.7888 17.7586 11.3005C17.9523 7.26831 14.7436 4.0226 10.7095 4.17169C7.15613 4.30618 4.30643 7.15589 4.17193 10.7093C4.02288 14.7434 7.26862 17.9521 11.3008 17.7582C12.7891 17.6894 14.2126 17.1295 15.349 16.1661C15.3491 16.166 15.3493 16.1659 15.3494 16.1659C15.3496 16.1659 15.3498 16.166 15.3499 16.1661L19.8513 20.6675C20.0785 20.8912 20.4441 20.8883 20.6677 20.6611C20.889 20.4364 20.889 20.0758 20.6677 19.8511ZM5.4683 12.2762C5.02515 10.2981 5.60401 8.34583 6.97507 6.9748C8.34614 5.60376 10.2983 5.02488 12.2765 5.46806C14.1481 5.88748 16.0458 7.78498 16.4652 9.65637C16.9087 11.6347 16.3297 13.5872 14.9586 14.9584C13.5875 16.3295 11.6349 16.9084 9.65651 16.4649C7.78512 16.0454 5.88772 14.1477 5.4683 12.2762Z" fill="#999999"></path></svg>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-sm-6 mb-20">
              <div class="dropdown">
                <button class="serach-dropdown text-start w-100" type="button" id="customSelectBtn" data-bs-toggle="dropdown" aria-expanded="false">Select Price Range</button>
                <ul class="dropdown-menu w-100" id="customSelectMenu"></ul>
              </div>
            </div>
            <div class="section-heading"><h2>All Items</h2></div>
            <div class="row" id="tpProductsRow">
              <?php if (!empty($products)): foreach ($products as $p):
                    $pimg = $p['img'] ?? (!empty($p['image']) ? imgUrl($p['image']) : ''); ?>
              <div class="col-xl-3 col-sm-6 mb-20 tp-product"
                   data-cat="<?= (int)$p['category_id'] ?>"
                   data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
                   data-price="<?= (float)$p['effective_price'] ?>"
                   data-created="<?= strtotime($p['created_at'] ?? 'now') ?>">
                <div class="item-card h-100">
                  <div class="d-flex flex-column h-100">
                    <a href="#" onclick="return false" style="color:#212529" class="d-block h-100">
                      <div class="flex-grow-1 h-100">
                        <div class="item-img">
                          <?php if ($pimg): ?><img src="<?= $pimg ?>" alt="item" class="w-100 h-100 object-fit-cover product-image">
                          <?php else: ?><div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background:#fce8f0;color:#e8a0bc;font-size:2.4rem"><i class="fas fa-spa"></i></div><?php endif; ?>
                        </div>
                        <div class="item-details">
                          <h5 class="fs-20 fw-6 mb-1 product-name"><?= htmlspecialchars($p['name']) ?></h5>
                          <?php if (!empty($p['category_name'])): ?><p class="fs-16 fw-5 mb-1 product-category"><?= htmlspecialchars($p['category_name']) ?></p><?php endif; ?>
                          <p class="fs-18 fw-7">
                            <span class="currency_icon"><?= $currency ?></span>
                            <span class="selling_price"><?= number_format($p['effective_price'], 2) ?></span>
                            <?php if (!empty($p['has_discount'])): ?><del class="fs-14 fw-7 text-gray-200"><?= $currency ?> <?= number_format((float)$p['price'], 2) ?></del><?php endif; ?>
                          </p>
                        </div>
                      </div>
                    </a>
                    <?php if (!empty($p['in_stock'])): ?>
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center mx-auto gap-2 addToCartBtn"
                            data-id="<?= (int)$p['id'] ?>" data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                            data-price="<?= (float)$p['effective_price'] ?>" data-img="<?= htmlspecialchars($pimg, ENT_QUOTES) ?>"
                            onclick="tpAddToCart(this)">
                      <span><?= $cartIcon ?></span> <?= htmlspecialchars($cta) ?>
                    </button>
                    <?php else: ?>
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center mx-auto gap-2 addToCartBtn" disabled style="opacity:.5">Sold Out</button>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <?php endforeach; else: ?>
              <div class="col-12 text-center py-5"><i class="fas fa-box-open" style="font-size:3rem;color:#e8a0bc"></i><p class="mt-3 fs-18">No products available yet</p></div>
              <?php endif; ?>
              <div class="col-12 text-center py-5 d-none" id="tpNoResults"><i class="fas fa-search" style="font-size:2.4rem;color:#e8a0bc"></i><p class="mt-3 fs-16">No products match your filters</p></div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="position-relative">
    <div class="text-center fw-5 fs-16">
      <?php if ($address): ?><div class="mb-2"><i class="fas fa-map-marker-alt"></i> <?= $address ?></div><?php endif; ?>
      <div>© Copyright <?= date('Y') ?> Tapify. All Rights Reserved.</div>
    </div>
  </footer>
</div>

<!-- CART DRAWER -->
<div id="tpCartOverlay" onclick="tpCloseCart()"></div>
<div id="tpCartDrawer">
  <div class="tp-cart-head"><h5>🛍️ Your Cart</h5><button class="tp-cart-close" onclick="tpCloseCart()">×</button></div>
  <div class="tp-cart-items" id="tpCartItems"></div>
  <div class="tp-cart-foot" id="tpCartFoot"></div>
</div>

<?php if ($enableTranslate): ?>
<script>function googleTranslateElementInit(){new google.translate.TranslateElement({pageLanguage:'en'},'google_translate_element');}</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php endif; ?>
<script>
const TP = {
  id: <?= (int)$storeId ?>,
  whatsapp: '<?= $waPhone ?>',
  currency: <?= json_encode($currency) ?>,
  deliveryFee: <?= $deliveryFee ?>,
  minOrder: <?= $minOrder ?>,
  template: <?= json_encode($waTemplate) ?>,
  priceMin: <?= (float)$priceMin ?>,
  priceMax: <?= (float)$priceMax ?>
};
let tpCart = [];
let tpFilters = { cat: 'all', q: '', min: null, max: null, date: null };

// ── Price-range dropdown (derived from products) ──
(function(){
  const menu = document.getElementById('customSelectMenu');
  if (!menu) return;
  const max = Math.ceil(TP.priceMax);
  if (max <= 0) return;
  const step = Math.max(1, Math.ceil(max / 4));
  const ranges = [[0, step],[step, step*2],[step*2, step*3],[step*3, max+1]];
  menu.innerHTML = '<li><a class="dropdown-item" href="#" data-min="" data-max="">All Prices</a></li>' +
    ranges.map(r => `<li><a class="dropdown-item" href="#" data-min="${r[0]}" data-max="${r[1]}">${TP.currency}${r[0]} - ${TP.currency}${r[1]>max?max:r[1]}</a></li>`).join('');
  menu.querySelectorAll('a').forEach(a => a.addEventListener('click', e => {
    e.preventDefault();
    document.getElementById('customSelectBtn').textContent = a.textContent;
    tpFilters.min = a.dataset.min === '' ? null : parseFloat(a.dataset.min);
    tpFilters.max = a.dataset.max === '' ? null : parseFloat(a.dataset.max);
    tpRender();
  }));
})();

function tpSetCategory(id, el){
  tpFilters.cat = (tpFilters.cat == id) ? 'all' : id;
  document.querySelectorAll('.category-button').forEach(b => b.classList.remove('active'));
  if (tpFilters.cat != 'all') el.classList.add('active');
  tpRender();
}
function tpSearch(){ tpFilters.q = (document.getElementById('tpSearch').value || '').toLowerCase(); tpRender(); }
function tpApplyPrice(){
  const mn = parseFloat(document.getElementById('tpMinPrice').value);
  const mx = parseFloat(document.getElementById('tpMaxPrice').value);
  tpFilters.min = isNaN(mn) ? null : mn;
  tpFilters.max = isNaN(mx) ? null : mx;
  tpRender();
}
function tpApplyDate(val){
  const now = Date.now()/1000;
  const map = { '3_days':3*86400, '1_week':7*86400, '1_month':30*86400, '6_months':182*86400, '1_year':365*86400 };
  tpFilters.date = now - (map[val] || 0);
  tpRender();
}
function tpResetFilters(){
  tpFilters = { cat:'all', q:'', min:null, max:null, date:null };
  document.getElementById('tpSearch').value = '';
  document.getElementById('tpMinPrice').value = '';
  document.getElementById('tpMaxPrice').value = '';
  document.querySelectorAll('input[name="tpDateFilter"]').forEach(r => r.checked = false);
  document.querySelectorAll('.category-button').forEach(b => b.classList.remove('active'));
  document.getElementById('customSelectBtn').textContent = 'Select Price Range';
  tpRender();
}
function tpRender(){
  let shown = 0;
  document.querySelectorAll('.tp-product').forEach(card => {
    const cat = card.dataset.cat, name = card.dataset.name;
    const price = parseFloat(card.dataset.price), created = parseInt(card.dataset.created);
    let ok = true;
    if (tpFilters.cat !== 'all' && cat != tpFilters.cat) ok = false;
    if (tpFilters.q && !name.includes(tpFilters.q)) ok = false;
    if (tpFilters.min !== null && price < tpFilters.min) ok = false;
    if (tpFilters.max !== null && price > tpFilters.max) ok = false;
    if (tpFilters.date !== null && created < tpFilters.date) ok = false;
    card.style.display = ok ? '' : 'none';
    if (ok) shown++;
  });
  document.getElementById('tpNoResults').classList.toggle('d-none', shown > 0);
}

// ── Cart ──
function tpAddToCart(btn){
  const id = +btn.dataset.id;
  const ex = tpCart.find(i => i.id === id);
  if (ex) ex.qty++;
  else tpCart.push({ id, name: btn.dataset.name, price: +btn.dataset.price, img: btn.dataset.img, qty: 1 });
  tpUpdateCart(); tpToast('✓ Added to cart');
}
function tpChangeQty(id, d){ const it = tpCart.find(i=>i.id===id); if(!it) return; it.qty = Math.max(1, it.qty+d); tpUpdateCart(); tpRenderCart(); }
function tpRemove(id){ tpCart = tpCart.filter(i=>i.id!==id); tpUpdateCart(); tpRenderCart(); }
function tpCount(){ return tpCart.reduce((s,i)=>s+i.qty,0); }
function tpSub(){ return tpCart.reduce((s,i)=>s+i.qty*i.price,0); }
function tpUpdateCart(){ document.getElementById('tpCartCount').textContent = tpCount(); }
function tpOpenCart(){ tpRenderCart(); document.getElementById('tpCartDrawer').classList.add('open'); document.getElementById('tpCartOverlay').style.display='block'; }
function tpCloseCart(){ document.getElementById('tpCartDrawer').classList.remove('open'); document.getElementById('tpCartOverlay').style.display='none'; }
function tpRenderCart(){
  const items = document.getElementById('tpCartItems'), foot = document.getElementById('tpCartFoot');
  if (!tpCart.length){ items.innerHTML='<p class="text-center text-muted py-5">Your cart is empty</p>'; foot.innerHTML=''; return; }
  items.innerHTML = tpCart.map(i=>`
    <div class="tp-ci">
      <img src="${i.img||''}" onerror="this.style.visibility='hidden'">
      <div style="flex:1">
        <div class="n">${i.name}</div><div class="p">${TP.currency}${i.price.toFixed(2)}</div>
        <div class="tp-qty"><button onclick="tpChangeQty(${i.id},-1)">−</button><span>${i.qty}</span><button onclick="tpChangeQty(${i.id},1)">+</button>
        <button onclick="tpRemove(${i.id})" style="margin-left:auto;color:#e11">🗑</button></div>
      </div>
    </div>`).join('');
  const sub = tpSub(), total = sub + TP.deliveryFee;
  foot.innerHTML = `
    <div class="row-l"><span>Subtotal</span><span>${TP.currency}${sub.toFixed(2)}</span></div>
    ${TP.deliveryFee>0?`<div class="row-l"><span>Delivery</span><span>${TP.currency}${TP.deliveryFee.toFixed(2)}</span></div>`:''}
    <div class="row-l tot"><span>Total</span><span>${TP.currency}${total.toFixed(2)}</span></div>
    ${sub<TP.minOrder?`<p style="color:#e11;font-size:.8rem;text-align:center;margin:6px 0">Min order ${TP.currency}${TP.minOrder.toFixed(2)}</p>`:`
    <div style="margin-top:12px">
      <div class="tp-field"><input id="tpName" placeholder="Your name *"></div>
      <div class="tp-field"><input id="tpPhone" placeholder="Phone / WhatsApp *"></div>
      <div class="tp-field"><textarea id="tpAddr" rows="2" placeholder="Delivery address"></textarea></div>
      <div class="tp-field"><textarea id="tpNotes" rows="1" placeholder="Notes (optional)"></textarea></div>
      <button class="tp-checkout" onclick="tpPlaceOrder()"><i class="fab fa-whatsapp"></i> Order on WhatsApp</button>
    </div>`}`;
}
function tpPlaceOrder(){
  const name=(document.getElementById('tpName').value||'').trim(), phone=(document.getElementById('tpPhone').value||'').trim();
  if(!name||!phone){ tpToast('Enter name & phone','err'); return; }
  const addr=document.getElementById('tpAddr').value||'', notes=document.getElementById('tpNotes').value||'';
  const sub=tpSub(), total=sub+TP.deliveryFee;
  const items=tpCart.map(i=>`• ${i.name} x${i.qty} = ${TP.currency}${(i.qty*i.price).toFixed(2)}`).join('\n');
  let msg=(TP.template||'🛍️ NEW ORDER\n\nName: {customer_name}\nPhone: {customer_phone}\n\n{items}\n\nTotal: {total}')
    .replace('{customer_name}',name).replace('{customer_phone}',phone).replace('{items}',items).replace('{total}',TP.currency+total.toFixed(2));
  if(addr) msg+='\nAddress: '+addr; if(notes) msg+='\nNotes: '+notes;
  fetch('/store-order-submit.php',{method:'POST',headers:{'Content-Type':'application/json'},
    body:JSON.stringify({store_id:TP.id,customer_name:name,customer_phone:phone,customer_address:addr,items:tpCart,subtotal:sub,delivery_charge:TP.deliveryFee,total_amount:total,notes:notes})}).catch(()=>{});
  window.location.href=`https://wa.me/${TP.whatsapp}?text=${encodeURIComponent(msg)}`;
}
function tpToast(m,t){ const e=document.createElement('div'); e.className='tp-toast'; if(t==='err') e.style.background='#c0392b'; e.textContent=m; document.body.appendChild(e); setTimeout(()=>e.classList.add('show'),10); setTimeout(()=>{e.classList.remove('show');setTimeout(()=>e.remove(),350);},2200); }
</script>
</body>
</html>
