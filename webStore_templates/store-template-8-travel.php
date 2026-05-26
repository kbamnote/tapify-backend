<?php
/* ============================================================
   Tapify WhatsApp Web Store — Template 8: Travel & Packages
   Self-contained | No Bootstrap | CDN: Google Fonts + FA 6.5
   ============================================================ */

$shopName    = htmlspecialchars($store['store_name']       ?? 'Travel Store');
$shopTagline = htmlspecialchars($store['tagline']          ?? 'Explore the World with Us');
$shopDesc    = $store['description']                       ?? '';
$logo        = !empty($store['logo_image'])  ? imgUrl($store['logo_image'])  : '';
$bannerImg   = !empty($store['cover_image']) ? imgUrl($store['cover_image']) : '';
$waPhone     = preg_replace('/\D/', '', $store['whatsapp_number'] ?? '');
$primary     = $store['primary_color']    ?? '#0369a1';
$secondary   = $store['secondary_color']  ?? '#0284c7';
$currency    = $store['currency_symbol']  ?? '₹';
$deliveryFee = (float)($store['delivery_charge']    ?? 0);
$minOrder    = (float)($store['min_order_amount']   ?? 0);
$waTemplate  = $store['order_message_template']     ?? '';
$storeAddress = htmlspecialchars($store['address']  ?? '');
$storePhone  = htmlspecialchars($store['phone']     ?? '');
$storeEmail  = htmlspecialchars($store['email']     ?? '');

foreach ($products as &$p) {
    $p['img']          = !empty($p['image'])       ? imgUrl($p['image'])
                       : (!empty($p['image_url'])  ? imgUrl($p['image_url']) : '');
    $p['effective']    = ($p['discount_price'] > 0) ? (float)$p['discount_price'] : (float)$p['price'];
    $p['has_discount'] = ($p['discount_price'] !== null && $p['discount_price'] > 0);
    $p['disc_pct']     = $p['has_discount'] ? round((1 - $p['discount_price'] / $p['price']) * 100) : 0;
} unset($p);

/* Category list */
$catList = [];
foreach ($categories as $cat) {
    $catList[] = [
        'id'   => $cat['id']   ?? $cat['category_id']   ?? '',
        'name' => htmlspecialchars($cat['name']          ?? $cat['category_name'] ?? ''),
        'icon' => !empty($cat['image']) ? imgUrl($cat['image']) : '',
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $shopName ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
/* ── Reset & Root ───────────────────────────────────────── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --p:<?= $primary ?>;
  --s:<?= $secondary ?>;
  --bg:#f0f9ff;
  --surface:#ffffff;
  --text:#0c1a2e;
  --muted:#64748b;
  --border:#bae6fd;
  --hero-h:260px;
  --radius:14px;
  --card-radius:16px;
  --font:'Plus Jakarta Sans',sans-serif;
}
html{scroll-behavior:smooth}
body{font-family:var(--font);background:var(--bg);color:var(--text);min-height:100vh;
     padding-bottom:90px;-webkit-font-smoothing:antialiased}

/* ── Animations ─────────────────────────────────────────── */
@keyframes fadeUp{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:none}}
@keyframes shimmer{0%{left:-100%}100%{left:200%}}
@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.07)}}
@keyframes slideInDown{from{opacity:0;transform:translateY(-16px)}to{opacity:1;transform:none}}
@keyframes toastIn{from{opacity:0;transform:translateX(110%)}to{opacity:1;transform:translateX(0)}}
@keyframes toastOut{to{opacity:0;transform:translateX(110%)}}
@keyframes spin{to{transform:rotate(360deg)}}
.fade-up{opacity:0;transform:translateY(22px);transition:opacity .55s ease,transform .55s ease}
.fade-up.visible{opacity:1;transform:none}

/* ── Sticky Nav ─────────────────────────────────────────── */
.t8-nav{
  position:sticky;top:0;z-index:120;
  background:rgba(255,255,255,.88);
  backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);
  border-bottom:1px solid var(--border);
  padding:10px 16px;
  display:flex;align-items:center;gap:10px;
}
.t8-nav-logo{width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--p)}
.t8-nav-logo-ph{width:38px;height:38px;border-radius:50%;background:var(--p);
  display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.9rem}
.t8-nav-name{font-weight:700;font-size:1rem;color:var(--text);flex:1;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.t8-nav-cart-btn{
  position:relative;background:var(--p);border:none;border-radius:50%;
  width:42px;height:42px;display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:1rem;cursor:pointer;flex-shrink:0;
  box-shadow:0 4px 12px rgba(3,105,161,.32);transition:transform .2s,box-shadow .2s
}
.t8-nav-cart-btn:hover{transform:scale(1.08);box-shadow:0 6px 18px rgba(3,105,161,.42)}
.t8-cart-bubble{
  position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;
  font-size:.6rem;font-weight:700;width:18px;height:18px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;display:none
}

/* ── Hero ───────────────────────────────────────────────── */
.t8-hero{
  position:relative;height:var(--hero-h);overflow:hidden;
  background:linear-gradient(135deg,#0c4a6e 0%,#075985 40%,#0369a1 70%,#0284c7 100%);
}
.t8-hero-img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.t8-hero-overlay{
  position:absolute;inset:0;
  background:linear-gradient(to bottom,rgba(7,46,80,.55) 0%,rgba(3,105,161,.72) 60%,rgba(2,132,199,.82) 100%)
}
.t8-hero-content{
  position:relative;z-index:2;height:100%;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:20px 20px 60px;text-align:center
}
.t8-hero-logo{
  width:62px;height:62px;border-radius:50%;object-fit:cover;
  border:3px solid rgba(255,255,255,.8);margin-bottom:10px;
  box-shadow:0 4px 20px rgba(0,0,0,.3)
}
.t8-hero-logo-ph{
  width:62px;height:62px;border-radius:50%;margin-bottom:10px;
  background:rgba(255,255,255,.18);border:3px solid rgba(255,255,255,.6);
  display:flex;align-items:center;justify-content:center;
  font-size:1.4rem;font-weight:800;color:#fff;backdrop-filter:blur(6px)
}
.t8-hero-name{
  font-size:1.55rem;font-weight:800;color:#fff;
  text-shadow:0 2px 10px rgba(0,0,0,.4);letter-spacing:-.02em;
  animation:slideInDown .55s ease
}
.t8-hero-tag{
  font-size:.82rem;color:rgba(255,255,255,.82);margin-top:5px;
  font-weight:400;text-shadow:0 1px 6px rgba(0,0,0,.3)
}
/* Search bar floating over hero bottom */
.t8-search-wrap{
  position:relative;z-index:10;margin:-30px 16px 0;
  animation:fadeUp .6s ease .1s both
}
.t8-search-inner{
  display:flex;align-items:center;gap:10px;
  background:#fff;border-radius:50px;
  padding:10px 16px;
  box-shadow:0 8px 30px rgba(3,105,161,.2),0 2px 8px rgba(0,0,0,.1);
  border:1.5px solid var(--border)
}
.t8-search-inner i{color:var(--p);font-size:.95rem;flex-shrink:0}
#searchInput{
  border:none;outline:none;flex:1;font-family:var(--font);font-size:.9rem;
  color:var(--text);background:transparent
}
#searchInput::placeholder{color:var(--muted)}
.t8-search-clear{
  background:none;border:none;cursor:pointer;color:var(--muted);
  font-size:.85rem;padding:2px 4px;display:none
}

/* ── Section header ─────────────────────────────────────── */
.t8-section{padding:22px 16px 6px}
.t8-section-head{
  display:flex;align-items:center;justify-content:space-between;margin-bottom:14px
}
.t8-section-title{
  font-size:1.05rem;font-weight:700;color:var(--text);
  display:flex;align-items:center;gap:8px
}
.t8-section-title i{color:var(--p);font-size:.95rem}
.t8-see-all{font-size:.78rem;color:var(--p);font-weight:600;text-decoration:none}

/* ── Destination / Category pills ──────────────────────── */
.t8-cats{padding:20px 0 4px;margin-top:22px}
.t8-cats-label{
  font-size:.72rem;font-weight:700;color:var(--muted);
  letter-spacing:.06em;text-transform:uppercase;padding:0 16px;margin-bottom:10px
}
.t8-cats-scroll{
  display:flex;gap:10px;overflow-x:auto;padding:4px 16px 8px;
  scrollbar-width:none
}
.t8-cats-scroll::-webkit-scrollbar{display:none}
.cat-btn{
  flex-shrink:0;display:flex;align-items:center;gap:7px;
  background:#fff;border:1.5px solid var(--border);
  border-radius:50px;padding:8px 16px;
  font-family:var(--font);font-size:.8rem;font-weight:600;color:var(--muted);
  cursor:pointer;white-space:nowrap;transition:all .22s ease;
  box-shadow:0 2px 6px rgba(0,0,0,.06)
}
.cat-btn img{width:20px;height:20px;border-radius:50%;object-fit:cover}
.cat-btn:hover{border-color:var(--p);color:var(--p);background:#f0f9ff}
.cat-btn.active{
  background:linear-gradient(135deg,var(--p),var(--s));
  border-color:transparent;color:#fff;
  box-shadow:0 4px 14px rgba(3,105,161,.32)
}

/* ── Info strips ────────────────────────────────────────── */
.t8-stats{
  display:flex;gap:8px;padding:16px 16px 0;overflow-x:auto;scrollbar-width:none
}
.t8-stats::-webkit-scrollbar{display:none}
.t8-stat{
  flex-shrink:0;display:flex;align-items:center;gap:8px;
  background:#fff;border-radius:12px;padding:10px 14px;
  border:1px solid var(--border);
  box-shadow:0 2px 8px rgba(0,0,0,.05)
}
.t8-stat i{font-size:1rem;color:var(--p)}
.t8-stat-val{font-size:.88rem;font-weight:700;color:var(--text)}
.t8-stat-lbl{font-size:.68rem;color:var(--muted)}

/* ── Package cards ──────────────────────────────────────── */
.t8-grid{
  display:grid;grid-template-columns:1fr 1fr;gap:14px;
  padding:10px 16px 20px
}
.prod-card{
  background:var(--surface);border-radius:var(--card-radius);
  overflow:hidden;box-shadow:0 3px 14px rgba(0,0,0,.07);
  border:1px solid var(--border);
  transition:transform .25s ease,box-shadow .25s ease;
  border-top:4px solid var(--p);
  display:flex;flex-direction:column
}
.prod-card:hover{transform:translateY(-4px);box-shadow:0 10px 28px rgba(3,105,161,.16)}
.t8-card-img-wrap{
  position:relative;width:100%;aspect-ratio:3/2;overflow:hidden;background:#e0f2fe
}
.t8-card-img-wrap img{
  width:100%;height:100%;object-fit:cover;
  transition:transform .4s ease
}
.prod-card:hover .t8-card-img-wrap img{transform:scale(1.06)}
.t8-card-img-ph{
  width:100%;height:100%;display:flex;align-items:center;justify-content:center;
  font-size:2rem;background:linear-gradient(135deg,#e0f2fe,#bae6fd);color:var(--p)
}
.t8-discount-tag{
  position:absolute;top:8px;left:8px;background:#ef4444;color:#fff;
  font-size:.62rem;font-weight:700;padding:3px 8px;border-radius:50px;
  letter-spacing:.02em
}
.t8-badge-wrap{
  position:absolute;top:8px;right:8px;display:flex;flex-direction:column;gap:4px
}
.t8-badge{
  background:rgba(3,105,161,.85);color:#fff;
  font-size:.58rem;font-weight:600;padding:3px 7px;border-radius:50px;
  backdrop-filter:blur(4px);letter-spacing:.03em;
  display:flex;align-items:center;gap:3px
}
.t8-card-body{padding:10px 11px 12px;flex:1;display:flex;flex-direction:column;gap:6px}
.t8-card-name{
  font-size:.82rem;font-weight:700;color:var(--text);
  line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;
  -webkit-box-orient:vertical;overflow:hidden
}
.t8-card-desc{
  font-size:.7rem;color:var(--muted);line-height:1.4;
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden
}
.t8-card-price-row{display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:auto}
.t8-price{font-size:.95rem;font-weight:800;color:var(--p)}
.t8-price-old{font-size:.7rem;color:var(--muted);text-decoration:line-through}
.t8-card-footer{
  padding:0 11px 12px;display:flex;gap:6px;align-items:center
}
.t8-book-btn{
  flex:1;background:linear-gradient(135deg,var(--p) 0%,var(--s) 100%);
  border:none;border-radius:50px;padding:9px 12px;
  color:#fff;font-family:var(--font);font-size:.75rem;font-weight:700;
  cursor:pointer;letter-spacing:.03em;
  display:flex;align-items:center;justify-content:center;gap:5px;
  transition:opacity .2s,transform .2s;position:relative;overflow:hidden
}
.t8-book-btn::after{
  content:'';position:absolute;top:0;left:-100%;width:60%;height:100%;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.25),transparent);
  transition:none
}
.t8-book-btn:hover::after{animation:shimmer .55s ease forwards}
.t8-book-btn:hover{opacity:.92;transform:scale(1.03)}
.t8-book-btn:disabled{opacity:.55;cursor:not-allowed;transform:none}
.t8-wishlist-btn{
  width:34px;height:34px;border-radius:50%;border:1.5px solid var(--border);
  background:#fff;display:flex;align-items:center;justify-content:center;
  cursor:pointer;color:var(--muted);font-size:.85rem;flex-shrink:0;
  transition:all .2s
}
.t8-wishlist-btn:hover{border-color:var(--p);color:var(--p);background:#f0f9ff}
.t8-out-badge{
  background:#f1f5f9;color:var(--muted);font-size:.7rem;font-weight:600;
  border-radius:50px;padding:6px 12px;text-align:center;width:100%
}

/* ── No results ─────────────────────────────────────────── */
.t8-empty{
  grid-column:1/-1;text-align:center;padding:40px 20px;
  display:flex;flex-direction:column;align-items:center;gap:10px
}
.t8-empty i{font-size:2.5rem;color:var(--border)}
.t8-empty p{font-size:.9rem;color:var(--muted)}

/* ── Float cart bar ─────────────────────────────────────── */
#floatCart{
  display:none;position:fixed;bottom:0;left:0;right:0;z-index:200;
  padding:10px 16px;
  background:linear-gradient(135deg,var(--p),var(--s));
}
.t8-float-inner{
  display:flex;align-items:center;justify-content:space-between;
  background:rgba(255,255,255,.15);border-radius:50px;
  padding:10px 16px;backdrop-filter:blur(8px);
  border:1px solid rgba(255,255,255,.25)
}
.t8-float-left{display:flex;align-items:center;gap:10px}
.t8-float-icon{
  width:34px;height:34px;background:rgba(255,255,255,.2);
  border-radius:50%;display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.9rem
}
#cartBubble{
  background:#fff;color:var(--p);font-size:.72rem;font-weight:800;
  width:20px;height:20px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  animation:pulse .4s ease
}
.t8-float-label{color:rgba(255,255,255,.9);font-size:.78rem;font-weight:500}
.t8-float-price{color:#fff;font-size:1.05rem;font-weight:800}
.t8-view-cart-btn{
  background:#fff;color:var(--p);border:none;border-radius:50px;
  padding:8px 18px;font-family:var(--font);font-size:.8rem;font-weight:700;
  cursor:pointer;transition:transform .2s;white-space:nowrap
}
.t8-view-cart-btn:hover{transform:scale(1.04)}

/* ── Cart overlay & drawer ──────────────────────────────── */
#cartOverlay{
  display:none;position:fixed;inset:0;z-index:300;
  background:rgba(7,46,80,.55);backdrop-filter:blur(4px)
}
#cartDrawer{
  position:fixed;bottom:0;left:0;right:0;z-index:310;
  background:#fff;border-radius:24px 24px 0 0;
  max-height:88vh;display:flex;flex-direction:column;
  transform:translateY(100%);transition:transform .38s cubic-bezier(.32,1,.55,1);
  box-shadow:0 -8px 40px rgba(3,105,161,.2)
}
#cartDrawer.open{transform:translateY(0)}
.t8-drawer-handle{
  width:40px;height:4px;background:#cbd5e1;border-radius:2px;
  margin:12px auto 0;flex-shrink:0
}
.t8-drawer-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:14px 20px 10px;border-bottom:1px solid #f0f9ff;flex-shrink:0
}
.t8-drawer-title{font-size:1rem;font-weight:700;color:var(--text)}
.t8-close-btn{
  background:none;border:none;cursor:pointer;color:var(--muted);
  font-size:1.1rem;padding:4px;border-radius:50%;
  width:30px;height:30px;display:flex;align-items:center;justify-content:center;
  transition:background .2s
}
.t8-close-btn:hover{background:#f0f9ff;color:var(--text)}
.t8-drawer-body{flex:1;overflow-y:auto;padding:14px 20px}
.t8-drawer-body::-webkit-scrollbar{width:4px}
.t8-drawer-body::-webkit-scrollbar-thumb{background:#bae6fd;border-radius:2px}

/* Cart items */
.t8-cart-item{
  display:flex;align-items:center;gap:12px;padding:10px 0;
  border-bottom:1px solid #f0f9ff
}
.t8-cart-item:last-child{border-bottom:none}
.t8-ci-img{width:54px;height:54px;border-radius:10px;object-fit:cover;flex-shrink:0;
  border:1px solid var(--border)}
.t8-ci-img-ph{
  width:54px;height:54px;border-radius:10px;flex-shrink:0;
  background:#e0f2fe;display:flex;align-items:center;justify-content:center;
  font-size:1.2rem;color:var(--p);border:1px solid var(--border)
}
.t8-ci-info{flex:1;min-width:0}
.t8-ci-name{font-size:.82rem;font-weight:600;color:var(--text);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.t8-ci-price{font-size:.78rem;color:var(--p);font-weight:700;margin-top:2px}
.t8-qty-ctrl{display:flex;align-items:center;gap:6px}
.t8-qty-btn{
  width:26px;height:26px;border-radius:50%;border:1.5px solid var(--border);
  background:#f0f9ff;color:var(--text);font-size:.85rem;font-weight:700;
  display:flex;align-items:center;justify-content:center;cursor:pointer;
  transition:all .2s;flex-shrink:0
}
.t8-qty-btn:hover{background:var(--p);border-color:var(--p);color:#fff}
.t8-qty-val{font-size:.85rem;font-weight:700;color:var(--text);min-width:18px;text-align:center}
.t8-ci-rm{
  background:none;border:none;cursor:pointer;color:#ef4444;
  font-size:.75rem;padding:4px;opacity:.7;
  transition:opacity .2s;flex-shrink:0
}
.t8-ci-rm:hover{opacity:1}

/* Cart summary */
.t8-cart-summary{
  border-top:1.5px solid var(--border);padding:14px 0 0;margin-top:6px
}
.t8-sum-row{display:flex;justify-content:space-between;align-items:center;
  font-size:.82rem;color:var(--muted);margin-bottom:7px}
.t8-sum-row.total{
  font-size:.95rem;font-weight:800;color:var(--text);
  border-top:1px solid var(--border);padding-top:10px;margin-top:4px
}
.t8-sum-row.total span:last-child{color:var(--p)}

/* Checkout form */
#checkoutSection{display:none;margin-top:16px;border-top:1.5px solid var(--border);padding-top:14px}
.t8-form-title{font-size:.9rem;font-weight:700;color:var(--text);margin-bottom:12px;
  display:flex;align-items:center;gap:7px}
.t8-form-title i{color:var(--p)}
.t8-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.t8-form-grid .full{grid-column:1/-1}
.t8-input{
  width:100%;padding:10px 13px;border:1.5px solid var(--border);border-radius:10px;
  font-family:var(--font);font-size:.82rem;color:var(--text);
  background:#f8fcff;outline:none;transition:border .2s,box-shadow .2s
}
.t8-input:focus{border-color:var(--p);box-shadow:0 0 0 3px rgba(3,105,161,.1);background:#fff}
.t8-input::placeholder{color:#94a3b8}
.t8-checkout-btn{
  width:100%;margin-top:14px;
  background:linear-gradient(135deg,var(--p) 0%,var(--s) 100%);
  border:none;border-radius:12px;padding:14px;
  color:#fff;font-family:var(--font);font-size:.9rem;font-weight:700;
  cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;
  transition:opacity .2s,transform .2s;letter-spacing:.02em
}
.t8-checkout-btn:hover{opacity:.92;transform:scale(1.01)}
.t8-checkout-btn:disabled{opacity:.5;cursor:not-allowed;transform:none}
.t8-min-warn{
  text-align:center;padding:10px;background:#fef3c7;border-radius:8px;
  font-size:.78rem;color:#92400e;border:1px solid #fde68a;margin-top:10px
}
.t8-empty-cart{
  text-align:center;padding:30px 20px;
  display:flex;flex-direction:column;align-items:center;gap:10px
}
.t8-empty-cart i{font-size:2.8rem;color:#bae6fd}
.t8-empty-cart p{font-size:.9rem;color:var(--muted)}

/* ── Footer ─────────────────────────────────────────────── */
.t8-footer{
  background:linear-gradient(135deg,#0c4a6e 0%,#075985 50%,#0369a1 100%);
  color:rgba(255,255,255,.9);padding:28px 20px 20px;margin-top:24px
}
.t8-footer-logo-row{display:flex;align-items:center;gap:12px;margin-bottom:16px}
.t8-footer-logo{width:48px;height:48px;border-radius:50%;object-fit:cover;
  border:2px solid rgba(255,255,255,.4)}
.t8-footer-logo-ph{width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.15);
  display:flex;align-items:center;justify-content:center;font-size:1.1rem;
  font-weight:800;color:#fff;border:2px solid rgba(255,255,255,.3)}
.t8-footer-bname{font-size:1.05rem;font-weight:700;color:#fff}
.t8-footer-btag{font-size:.75rem;color:rgba(255,255,255,.65)}
.t8-footer-divider{height:1px;background:rgba(255,255,255,.15);margin:14px 0}
.t8-footer-info{display:flex;flex-direction:column;gap:9px}
.t8-footer-row{display:flex;align-items:flex-start;gap:10px;font-size:.8rem;color:rgba(255,255,255,.78)}
.t8-footer-row i{font-size:.85rem;color:rgba(255,255,255,.55);width:16px;flex-shrink:0;margin-top:1px}
.t8-footer-row a{color:rgba(255,255,255,.78);text-decoration:none}
.t8-footer-row a:hover{color:#fff}
.t8-footer-bottom{
  margin-top:20px;padding-top:14px;border-top:1px solid rgba(255,255,255,.12);
  text-align:center;font-size:.72rem;color:rgba(255,255,255,.45)
}
.t8-footer-bottom a{color:rgba(255,255,255,.6);text-decoration:none}
.t8-footer-bottom a:hover{color:#fff}

/* ── Toast ──────────────────────────────────────────────── */
.tap-toast{
  position:fixed;top:16px;right:16px;z-index:999;
  background:var(--p);color:#fff;
  padding:11px 16px;border-radius:12px;font-size:.82rem;font-weight:600;
  display:flex;align-items:center;gap:8px;
  box-shadow:0 6px 20px rgba(3,105,161,.35);
  animation:toastIn .3s ease;max-width:280px
}
.tap-toast.out{animation:toastOut .3s ease forwards}
.tap-toast i{font-size:.9rem}

/* ── Responsive ─────────────────────────────────────────── */
@media(min-width:480px){
  .t8-grid{grid-template-columns:repeat(3,1fr)}
  :root{--hero-h:300px}
}
@media(min-width:720px){
  .t8-grid{grid-template-columns:repeat(4,1fr)}
  body{max-width:800px;margin:0 auto}
  .t8-nav,.t8-hero,#floatCart,.t8-search-wrap,
  .t8-cats,.t8-stats,.t8-section{max-width:800px}
}
</style>
</head>
<body>

<!-- ══ NAV ══════════════════════════════════════════════════ -->
<nav class="t8-nav">
  <?php if ($logo): ?>
    <img class="t8-nav-logo" src="<?= $logo ?>" alt="<?= $shopName ?>">
  <?php else: ?>
    <div class="t8-nav-logo-ph"><?= mb_substr($shopName,0,1) ?></div>
  <?php endif; ?>
  <span class="t8-nav-name"><?= $shopName ?></span>
  <button class="t8-nav-cart-btn" onclick="openCart()" aria-label="Cart">
    <i class="fas fa-shopping-bag"></i>
    <span class="t8-cart-bubble" id="cartBubble">0</span>
  </button>
</nav>

<!-- ══ HERO ═════════════════════════════════════════════════ -->
<section class="t8-hero">
  <?php if ($bannerImg): ?>
    <img class="t8-hero-img" src="<?= $bannerImg ?>" alt="<?= $shopName ?>">
  <?php endif; ?>
  <div class="t8-hero-overlay"></div>
  <div class="t8-hero-content">
    <?php if ($logo): ?>
      <img class="t8-hero-logo" src="<?= $logo ?>" alt="<?= $shopName ?>">
    <?php else: ?>
      <div class="t8-hero-logo-ph"><?= mb_substr($shopName,0,1) ?></div>
    <?php endif; ?>
    <h1 class="t8-hero-name"><?= $shopName ?></h1>
    <?php if ($shopTagline): ?>
      <p class="t8-hero-tag"><?= $shopTagline ?></p>
    <?php endif; ?>
  </div>
</section>

<!-- ══ SEARCH (floats over hero bottom) ═════════════════════ -->
<div class="t8-search-wrap">
  <div class="t8-search-inner">
    <i class="fas fa-search"></i>
    <input type="text" id="searchInput" placeholder="Search destinations, packages…" oninput="searchProducts(this.value)">
    <button class="t8-search-clear" id="searchClear" onclick="clearSearch()"><i class="fas fa-times"></i></button>
  </div>
</div>

<!-- ══ STATS STRIP ══════════════════════════════════════════ -->
<div class="t8-stats fade-up">
  <div class="t8-stat">
    <i class="fas fa-globe-asia"></i>
    <div><div class="t8-stat-val"><?= count($products) ?>+</div><div class="t8-stat-lbl">Packages</div></div>
  </div>
  <?php if ($deliveryFee == 0): ?>
  <div class="t8-stat">
    <i class="fas fa-shipping-fast"></i>
    <div><div class="t8-stat-val">Free</div><div class="t8-stat-lbl">Delivery</div></div>
  </div>
  <?php elseif ($deliveryFee > 0): ?>
  <div class="t8-stat">
    <i class="fas fa-shipping-fast"></i>
    <div><div class="t8-stat-val"><?= $currency . number_format($deliveryFee) ?></div><div class="t8-stat-lbl">Delivery</div></div>
  </div>
  <?php endif; ?>
  <?php if ($minOrder > 0): ?>
  <div class="t8-stat">
    <i class="fas fa-wallet"></i>
    <div><div class="t8-stat-val"><?= $currency . number_format($minOrder) ?></div><div class="t8-stat-lbl">Min Order</div></div>
  </div>
  <?php endif; ?>
  <div class="t8-stat">
    <i class="fab fa-whatsapp"></i>
    <div><div class="t8-stat-val">WhatsApp</div><div class="t8-stat-lbl">Booking</div></div>
  </div>
</div>

<!-- ══ CATEGORIES ════════════════════════════════════════════ -->
<?php if (!empty($catList)): ?>
<div class="t8-cats fade-up">
  <div class="t8-cats-label"><i class="fas fa-map-marked-alt" style="margin-right:5px;color:var(--p)"></i>Browse By Destination</div>
  <div class="t8-cats-scroll">
    <button class="cat-btn active" data-cat="all" onclick="filterByCategory('all',this)">
      <i class="fas fa-th" style="font-size:.75rem"></i> All
    </button>
    <?php foreach ($catList as $cat): ?>
    <button class="cat-btn" data-cat="<?= htmlspecialchars($cat['id']) ?>" onclick="filterByCategory(<?= json_encode($cat['id']) ?>,this)">
      <?php if ($cat['icon']): ?>
        <img src="<?= $cat['icon'] ?>" alt="">
      <?php else: ?>
        <i class="fas fa-map-pin" style="font-size:.75rem"></i>
      <?php endif; ?>
      <?= $cat['name'] ?>
    </button>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- ══ PRODUCTS ══════════════════════════════════════════════ -->
<section class="t8-section">
  <div class="t8-section-head">
    <div class="t8-section-title"><i class="fas fa-suitcase-rolling"></i> Available Packages</div>
  </div>
</section>

<div class="t8-grid" id="productGrid">
  <?php if (empty($products)): ?>
    <div class="t8-empty">
      <i class="fas fa-plane-slash"></i>
      <p>No packages available yet. Check back soon!</p>
    </div>
  <?php else: foreach ($products as $p):
    $catId   = htmlspecialchars($p['category_id'] ?? '');
    $pName   = htmlspecialchars($p['name'] ?? '');
    $pDesc   = htmlspecialchars($p['description'] ?? '');
    $pId     = (int)($p['id'] ?? 0);
    $inStock = ($p['stock_status'] ?? 'in_stock') !== 'out_of_stock';
    $eff     = $p['effective'];
  ?>
    <div class="prod-card fade-up" data-cat="<?= $catId ?>" data-name="<?= strtolower($pName) ?>">
      <div class="t8-card-img-wrap">
        <?php if ($p['img']): ?>
          <img src="<?= $p['img'] ?>" alt="<?= $pName ?>" loading="lazy">
        <?php else: ?>
          <div class="t8-card-img-ph">✈️</div>
        <?php endif; ?>
        <?php if ($p['has_discount']): ?>
          <span class="t8-discount-tag"><?= $p['disc_pct'] ?>% OFF</span>
        <?php endif; ?>
        <?php if ($inStock && $p['img']): ?>
          <div class="t8-badge-wrap">
            <span class="t8-badge"><i class="fas fa-star" style="font-size:.5rem"></i> Popular</span>
          </div>
        <?php endif; ?>
      </div>
      <div class="t8-card-body">
        <div class="t8-card-name"><?= $pName ?></div>
        <?php if ($pDesc): ?><div class="t8-card-desc"><?= $pDesc ?></div><?php endif; ?>
        <div class="t8-card-price-row">
          <span class="t8-price"><?= $currency ?><?= number_format($eff, 2) ?></span>
          <?php if ($p['has_discount']): ?>
            <span class="t8-price-old"><?= $currency ?><?= number_format($p['price'], 2) ?></span>
          <?php endif; ?>
        </div>
      </div>
      <div class="t8-card-footer">
        <?php if ($inStock): ?>
          <button class="t8-book-btn" onclick='addToCart(<?= json_encode([
            "id"=>$pId,"name"=>$pName,"price"=>$eff,"img"=>$p['img']
          ]) ?>)'>
            <i class="fas fa-suitcase"></i> Book Now
          </button>
          <button class="t8-wishlist-btn" onclick="showToast('Added to wishlist ❤️')" aria-label="Wishlist">
            <i class="far fa-heart"></i>
          </button>
        <?php else: ?>
          <span class="t8-out-badge"><i class="fas fa-times-circle" style="margin-right:4px"></i>Sold Out</span>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; endif; ?>
</div>

<!-- ══ FOOTER ════════════════════════════════════════════════ -->
<footer class="t8-footer fade-up">
  <div class="t8-footer-logo-row">
    <?php if ($logo): ?>
      <img class="t8-footer-logo" src="<?= $logo ?>" alt="<?= $shopName ?>">
    <?php else: ?>
      <div class="t8-footer-logo-ph"><?= mb_substr($shopName,0,1) ?></div>
    <?php endif; ?>
    <div>
      <div class="t8-footer-bname"><?= $shopName ?></div>
      <?php if ($shopTagline): ?><div class="t8-footer-btag"><?= $shopTagline ?></div><?php endif; ?>
    </div>
  </div>
  <?php if ($shopDesc): ?>
    <p style="font-size:.78rem;color:rgba(255,255,255,.65);line-height:1.6;margin-bottom:10px"><?= htmlspecialchars($shopDesc) ?></p>
  <?php endif; ?>
  <div class="t8-footer-divider"></div>
  <div class="t8-footer-info">
    <?php if ($waPhone): ?>
    <div class="t8-footer-row">
      <i class="fab fa-whatsapp"></i>
      <a href="https://wa.me/<?= $waPhone ?>">+<?= $waPhone ?></a>
    </div>
    <?php endif; ?>
    <?php if ($storePhone && $storePhone !== $waPhone): ?>
    <div class="t8-footer-row">
      <i class="fas fa-phone-alt"></i><span><?= $storePhone ?></span>
    </div>
    <?php endif; ?>
    <?php if ($storeEmail): ?>
    <div class="t8-footer-row">
      <i class="fas fa-envelope"></i>
      <a href="mailto:<?= $storeEmail ?>"><?= $storeEmail ?></a>
    </div>
    <?php endif; ?>
    <?php if ($storeAddress): ?>
    <div class="t8-footer-row">
      <i class="fas fa-map-marker-alt"></i><span><?= $storeAddress ?></span>
    </div>
    <?php endif; ?>
  </div>
  <div class="t8-footer-bottom">
    Powered by <a href="#" target="_blank">Tapify</a> &nbsp;|&nbsp; A unit of <strong>Mr Print World</strong>
  </div>
</footer>

<!-- ══ FLOAT CART BAR ════════════════════════════════════════ -->
<div id="floatCart">
  <div class="t8-float-inner">
    <div class="t8-float-left">
      <div class="t8-float-icon"><i class="fas fa-shopping-bag"></i></div>
      <div id="cartBubble" style="background:#fff;color:var(--p);font-size:.72rem;font-weight:800;
        width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center">0</div>
      <span class="t8-float-label">items in cart</span>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
      <span class="t8-float-price" id="floatTotal"><?= $currency ?>0.00</span>
      <button class="t8-view-cart-btn" onclick="openCart()">View Cart</button>
    </div>
  </div>
</div>

<!-- ══ CART OVERLAY ══════════════════════════════════════════ -->
<div id="cartOverlay" onclick="closeCart()"></div>

<!-- ══ CART DRAWER ═══════════════════════════════════════════ -->
<div id="cartDrawer" role="dialog" aria-modal="true" aria-label="Shopping Cart">
  <div class="t8-drawer-handle"></div>
  <div class="t8-drawer-header">
    <span class="t8-drawer-title"><i class="fas fa-shopping-bag" style="color:var(--p);margin-right:6px"></i>Your Bookings</span>
    <button class="t8-close-btn" onclick="closeCart()" aria-label="Close"><i class="fas fa-times"></i></button>
  </div>
  <div class="t8-drawer-body">
    <div id="cartList"></div>
    <div id="cartSummary"></div>
    <div id="checkoutSection">
      <div class="t8-form-title"><i class="fas fa-user-circle"></i> Booking Details</div>
      <div class="t8-form-grid">
        <input class="t8-input" id="custName" type="text" placeholder="Your Name *" required>
        <input class="t8-input" id="custPhone" type="tel" placeholder="Phone *" required>
        <input class="t8-input full" id="custAddress" type="text" placeholder="Pickup / Travel Address *">
        <textarea class="t8-input full" id="custNote" rows="2" placeholder="Special requests or notes…" style="resize:none"></textarea>
      </div>
      <button class="t8-checkout-btn" id="checkoutBtn" onclick="placeOrder()">
        <i class="fab fa-whatsapp" style="font-size:1.1rem"></i> Confirm Booking via WhatsApp
      </button>
    </div>
  </div>
</div>

<!-- ══ JS ════════════════════════════════════════════════════ -->
<script>
/* ── Store data ────────────────────────────────────────── */
const STORE_DATA={
  id:<?= $storeId ?>,
  whatsapp:'<?= $waPhone ?>',
  currency:'<?= $currency ?>',
  deliveryFee:<?= $deliveryFee ?>,
  minOrder:<?= $minOrder ?>,
  template:<?= json_encode($waTemplate) ?>
};
let cart=[];

/* ── Cart CRUD ─────────────────────────────────────────── */
function addToCart(p){
  const i=cart.findIndex(x=>x.id===p.id);
  if(i>-1) cart[i].qty++;
  else cart.push({...p,qty:1});
  updateCartUI();
  showToast('<i class="fas fa-check-circle"></i> Added to cart!');
}
function removeFromCart(id){
  cart=cart.filter(x=>x.id!==id);
  updateCartUI();renderCartDrawer();
}
function changeQty(id,d){
  const i=cart.findIndex(x=>x.id===id);
  if(i<0)return;
  cart[i].qty+=d;
  if(cart[i].qty<1)cart.splice(i,1);
  updateCartUI();renderCartDrawer();
}
function getSub(){return cart.reduce((s,x)=>s+x.price*x.qty,0)}
function getCount(){return cart.reduce((s,x)=>s+x.qty,0)}

/* ── Cart UI ───────────────────────────────────────────── */
function updateCartUI(){
  const n=getCount(),sub=getSub(),total=sub+STORE_DATA.deliveryFee;
  const fc=document.getElementById('floatCart');
  const bub=document.getElementById('cartBubble');
  const ft=document.getElementById('floatTotal');
  const nb=document.querySelector('.t8-cart-bubble');
  if(n>0){
    fc.style.display='block';
    if(nb){nb.style.display='flex';nb.textContent=n}
    if(bub)bub.textContent=n;
    if(ft)ft.textContent=STORE_DATA.currency+total.toFixed(2);
  }else{
    fc.style.display='none';
    if(nb){nb.style.display='none'}
  }
}

function openCart(){
  document.getElementById('cartDrawer').classList.add('open');
  document.getElementById('cartOverlay').style.display='block';
  document.body.style.overflow='hidden';
  renderCartDrawer();
}
function closeCart(){
  document.getElementById('cartDrawer').classList.remove('open');
  document.getElementById('cartOverlay').style.display='none';
  document.body.style.overflow='';
}

function renderCartDrawer(){
  const cl=document.getElementById('cartList');
  const cs=document.getElementById('cartSummary');
  const ck=document.getElementById('checkoutSection');
  if(!cl)return;
  if(cart.length===0){
    cl.innerHTML=`<div class="t8-empty-cart">
      <i class="fas fa-suitcase-rolling"></i>
      <p>Your cart is empty</p>
      <p style="font-size:.75rem;color:#94a3b8">Browse our packages above</p>
    </div>`;
    cs.innerHTML='';ck.style.display='none';return;
  }
  cl.innerHTML=cart.map(item=>`
    <div class="t8-cart-item">
      ${item.img
        ?`<img class="t8-ci-img" src="${item.img}" alt="${item.name}">`
        :`<div class="t8-ci-img-ph">✈️</div>`}
      <div class="t8-ci-info">
        <div class="t8-ci-name">${item.name}</div>
        <div class="t8-ci-price">${STORE_DATA.currency}${(item.price*item.qty).toFixed(2)}</div>
      </div>
      <div class="t8-qty-ctrl">
        <button class="t8-qty-btn" onclick="changeQty(${item.id},-1)"><i class="fas fa-minus" style="font-size:.6rem"></i></button>
        <span class="t8-qty-val">${item.qty}</span>
        <button class="t8-qty-btn" onclick="changeQty(${item.id},1)"><i class="fas fa-plus" style="font-size:.6rem"></i></button>
      </div>
      <button class="t8-ci-rm" onclick="removeFromCart(${item.id})" aria-label="Remove"><i class="fas fa-trash"></i></button>
    </div>`).join('');
  const sub=getSub(),fee=STORE_DATA.deliveryFee,total=sub+fee;
  const meetsMin=STORE_DATA.minOrder===0||sub>=STORE_DATA.minOrder;
  cs.innerHTML=`<div class="t8-cart-summary">
    <div class="t8-sum-row"><span>Subtotal</span><span>${STORE_DATA.currency}${sub.toFixed(2)}</span></div>
    ${fee>0?`<div class="t8-sum-row"><span>Delivery</span><span>${STORE_DATA.currency}${fee.toFixed(2)}</span></div>`:''}
    <div class="t8-sum-row total"><span>Total</span><span>${STORE_DATA.currency}${total.toFixed(2)}</span></div>
    ${!meetsMin?`<div class="t8-min-warn"><i class="fas fa-info-circle"></i> Min order: ${STORE_DATA.currency}${STORE_DATA.minOrder.toFixed(2)} (add ${STORE_DATA.currency}${(STORE_DATA.minOrder-sub).toFixed(2)} more)</div>`:''}
  </div>`;
  ck.style.display=meetsMin?'block':'none';
}

/* ── Place order ───────────────────────────────────────── */
function placeOrder(){
  const name=(document.getElementById('custName')?.value||'').trim();
  const phone=(document.getElementById('custPhone')?.value||'').trim();
  const addr=(document.getElementById('custAddress')?.value||'').trim();
  const note=(document.getElementById('custNote')?.value||'').trim();
  if(!name){showToast('<i class="fas fa-exclamation-circle"></i> Please enter your name');return}
  if(!phone){showToast('<i class="fas fa-exclamation-circle"></i> Please enter your phone');return}
  const btn=document.getElementById('checkoutBtn');
  if(btn){btn.disabled=true;btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Processing…'}
  let msg=STORE_DATA.template||'New Booking from {store_name}';
  const sub=getSub(),fee=STORE_DATA.deliveryFee,total=sub+fee;
  const itemLines=cart.map(i=>`• ${i.name} x${i.qty} = ${STORE_DATA.currency}${(i.price*i.qty).toFixed(2)}`).join('\n');
  msg=msg.replace('{items}',itemLines)
         .replace('{subtotal}',STORE_DATA.currency+sub.toFixed(2))
         .replace('{delivery_fee}',STORE_DATA.currency+fee.toFixed(2))
         .replace('{total}',STORE_DATA.currency+total.toFixed(2))
         .replace('{customer_name}',name)
         .replace('{customer_phone}',phone)
         .replace('{customer_address}',addr)
         .replace('{notes}',note);
  if(!/\{items\}/.test(STORE_DATA.template||'')){
    msg+='\n\n*Booking Items:*\n'+itemLines+'\n\n*Subtotal:* '+STORE_DATA.currency+sub.toFixed(2);
    if(fee>0) msg+='\n*Delivery:* '+STORE_DATA.currency+fee.toFixed(2);
    msg+='\n*Total:* '+STORE_DATA.currency+total.toFixed(2);
    msg+='\n\n*Name:* '+name+'\n*Phone:* '+phone;
    if(addr) msg+='\n*Address:* '+addr;
    if(note) msg+='\n*Notes:* '+note;
  }
  fetch('/backend/store-order-submit.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({store_id:STORE_DATA.id,customer_name:name,customer_phone:phone,
      customer_address:addr,notes:note,items:cart,subtotal:sub,delivery_fee:fee,total:total})
  }).catch(()=>{}).finally(()=>{
    window.location.href='https://wa.me/'+STORE_DATA.whatsapp+'?text='+encodeURIComponent(msg);
  });
}

/* ── Filters & search ──────────────────────────────────── */
function filterByCategory(cat,btn){
  document.querySelectorAll('.cat-btn').forEach(b=>b.classList.remove('active'));
  if(btn)btn.classList.add('active');
  document.querySelectorAll('.prod-card').forEach(c=>{
    c.style.display=(cat==='all'||c.dataset.cat==cat)?'flex':'none';
  });
}
function searchProducts(q){
  const cl=document.getElementById('searchClear');
  if(cl)cl.style.display=q?'block':'none';
  const s=q.toLowerCase().trim();
  document.querySelectorAll('.prod-card').forEach(c=>{
    const nm=c.dataset.name||'';
    c.style.display=(!s||nm.includes(s))?'flex':'none';
  });
  // Reset category filter
  if(s){
    document.querySelectorAll('.cat-btn').forEach(b=>b.classList.remove('active'));
    document.querySelector('.cat-btn[data-cat="all"]')?.classList.add('active');
  }
}
function clearSearch(){
  const si=document.getElementById('searchInput');
  if(si){si.value='';searchProducts('');}
}

/* ── Toast ─────────────────────────────────────────────── */
function showToast(msg){
  const t=document.createElement('div');
  t.className='tap-toast';t.innerHTML=msg;
  document.body.appendChild(t);
  setTimeout(()=>{t.classList.add('out');setTimeout(()=>t.remove(),350)},2500);
}

/* ── Intersection observer ─────────────────────────────── */
const io=new IntersectionObserver((entries)=>{
  entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('visible');io.unobserve(e.target)}});
},{threshold:.12});
document.querySelectorAll('.fade-up').forEach(el=>io.observe(el));

/* ── Stagger grid cards ────────────────────────────────── */
document.querySelectorAll('.prod-card').forEach((c,i)=>{
  c.style.transitionDelay=(i*55)+'ms';
  io.observe(c);
});

/* ── Swipe to close drawer ─────────────────────────────── */
(function(){
  let sy=0;
  const dr=document.getElementById('cartDrawer');
  dr.addEventListener('touchstart',e=>{sy=e.touches[0].clientY},{passive:true});
  dr.addEventListener('touchend',e=>{
    if(e.changedTouches[0].clientY-sy>60)closeCart();
  },{passive:true});
})();
</script>
</body>
</html>
