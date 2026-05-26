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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400;1,500&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
:root{
  --p:<?= $primary ?>;
  --s:<?= $secondary ?>;
  --cream:#fdf8f5;
  --blush:#fce8f0;
  --rose-dark:#2d0a18;
  --rose-mid:#7a2248;
  --rose-muted:#c07090;
  --card:#ffffff;
  --border:rgba(214,51,132,0.1);
  --shadow-soft:0 4px 24px rgba(139,0,67,0.08);
  --shadow-card:0 2px 16px rgba(139,0,67,0.06);
  --radius-xl:24px;
  --radius-lg:18px;
  --radius-md:12px;
  --radius-pill:100px;
}
html{scroll-behavior:smooth;}
body{background:var(--cream);font-family:'DM Sans',sans-serif;display:flex;justify-content:center;min-height:100vh;color:var(--rose-dark);}
.wrap{width:100%;max-width:480px;background:var(--cream);min-height:100vh;position:relative;overflow-x:hidden;}

/* ── TOP BAR ── */
.top-bar{
  position:sticky;top:0;z-index:200;
  background:rgba(253,248,245,0.88);
  backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);
  display:flex;align-items:center;justify-content:space-between;
  padding:12px 18px;
  border-bottom:1px solid var(--border);
}
.top-logo-row{display:flex;align-items:center;gap:10px;}
.logo-circle{
  width:44px;height:44px;border-radius:50%;object-fit:cover;
  border:2px solid var(--p);flex-shrink:0;
  box-shadow:0 0 0 4px rgba(214,51,132,0.12);
}
.logo-placeholder{
  width:44px;height:44px;border-radius:50%;
  background:linear-gradient(135deg,var(--p),var(--s));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;
  flex-shrink:0;box-shadow:0 4px 14px rgba(214,51,132,0.35);
}
.top-name{font-family:'Playfair Display',serif;font-size:1.05rem;font-weight:700;color:var(--rose-dark);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;letter-spacing:.2px;}
.top-tagline-small{font-size:.68rem;color:var(--rose-muted);font-weight:400;margin-top:1px;letter-spacing:.3px;}
.top-wa{
  width:42px;height:42px;background:#25D366;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  color:#fff;text-decoration:none;font-size:1.2rem;flex-shrink:0;
  box-shadow:0 4px 16px rgba(37,211,102,0.35);
  transition:transform .22s,box-shadow .22s;
}
.top-wa:hover{transform:scale(1.1) rotate(-5deg);box-shadow:0 6px 20px rgba(37,211,102,0.5);}

/* ── HERO ── */
.hero{position:relative;width:100%;height:260px;overflow:hidden;}
.hero img{width:100%;height:100%;object-fit:cover;transition:transform 8s ease;}
.hero img:hover{transform:scale(1.04);}
.hero-bg{
  width:100%;height:100%;
  background:linear-gradient(135deg,var(--p) 0%,#f082a8 50%,var(--s) 100%);
  display:flex;align-items:center;justify-content:center;
  position:relative;overflow:hidden;
}
.hero-bg::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 30% 50%,rgba(255,255,255,0.18) 0%,transparent 60%),
             radial-gradient(ellipse at 80% 20%,rgba(255,255,255,0.12) 0%,transparent 50%);
}
.hero-bg-circles{position:absolute;inset:0;overflow:hidden;}
.hero-bg-circles span{
  position:absolute;border-radius:50%;border:1px solid rgba(255,255,255,0.2);
  animation:float-circle 6s ease-in-out infinite;
}
.hero-bg-circles span:nth-child(1){width:160px;height:160px;top:-40px;right:-30px;animation-delay:0s;}
.hero-bg-circles span:nth-child(2){width:80px;height:80px;bottom:20px;left:20px;animation-delay:2s;}
.hero-bg-circles span:nth-child(3){width:120px;height:120px;top:30px;left:40%;animation-delay:4s;}
@keyframes float-circle{0%,100%{transform:translateY(0) scale(1);}50%{transform:translateY(-12px) scale(1.05);}}
.hero-bg-icon{font-size:6rem;color:rgba(255,255,255,0.15);position:relative;z-index:1;}
.hero-overlay{
  position:absolute;inset:0;
  background:linear-gradient(to top,var(--cream) 0%,rgba(253,248,245,0.4) 35%,transparent 65%);
}
.hero-badge{
  position:absolute;top:18px;left:18px;z-index:5;
  background:rgba(255,255,255,0.22);backdrop-filter:blur(12px);
  border:1px solid rgba(255,255,255,0.4);
  color:#fff;font-size:.7rem;font-weight:600;
  padding:5px 14px;border-radius:var(--radius-pill);letter-spacing:.8px;
}

/* ── STORE INFO ── */
.store-info{padding:6px 20px 0;text-align:center;}
.store-info h1{
  font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:700;
  color:var(--rose-dark);line-height:1.1;letter-spacing:-.3px;
}
.store-tagline{
  font-family:'Playfair Display',serif;font-size:1rem;font-style:italic;
  color:var(--rose-muted);margin-top:5px;letter-spacing:.2px;
}

/* Divider */
.divider-ornament{display:flex;align-items:center;gap:10px;margin:14px auto 0;width:fit-content;}
.divider-ornament::before,.divider-ornament::after{content:'';width:40px;height:1px;background:linear-gradient(to right,transparent,var(--p));}
.divider-ornament::after{background:linear-gradient(to left,transparent,var(--p));}
.divider-ornament i{color:var(--p);font-size:.7rem;}

/* Badges */
.badges-row{display:flex;justify-content:center;gap:7px;margin-top:14px;flex-wrap:wrap;}
.badge{
  display:inline-flex;align-items:center;gap:5px;
  background:#fff;border:1px solid rgba(214,51,132,0.15);
  color:var(--rose-mid);font-size:.71rem;font-weight:600;
  padding:5px 13px;border-radius:var(--radius-pill);letter-spacing:.1px;
  box-shadow:var(--shadow-card);
}
.badge-p{background:linear-gradient(135deg,var(--p),var(--s));color:#fff;border:none;box-shadow:0 4px 14px rgba(214,51,132,0.3);}

/* Description */
.store-desc-block{
  margin:14px 18px 0;
  background:#fff;border-radius:var(--radius-lg);
  padding:16px 18px;font-size:.84rem;color:var(--rose-mid);line-height:1.7;
  border:1px solid var(--border);box-shadow:var(--shadow-card);
  position:relative;overflow:hidden;
}
.store-desc-block::before{
  content:'';position:absolute;left:0;top:0;bottom:0;width:3px;
  background:linear-gradient(to bottom,var(--p),var(--s));
}

/* ── STATS STRIP ── */
.stats-strip{
  display:flex;margin:18px 18px 0;
  background:#fff;border-radius:var(--radius-lg);
  border:1px solid var(--border);box-shadow:var(--shadow-card);overflow:hidden;
}
.stat-item{
  flex:1;padding:14px 8px;text-align:center;
  border-right:1px solid var(--border);
  position:relative;
}
.stat-item:last-child{border-right:none;}
.stat-num{font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;color:var(--p);display:block;}
.stat-lbl{font-size:.67rem;font-weight:600;color:var(--rose-muted);letter-spacing:.4px;margin-top:2px;display:block;}

/* ── SEARCH ── */
.search-wrap{padding:18px 18px 0;}
.search-inner{
  display:flex;align-items:center;gap:10px;
  background:#fff;border:1.5px solid var(--border);
  border-radius:var(--radius-pill);padding:12px 20px;
  transition:border-color .25s,box-shadow .25s;
  box-shadow:var(--shadow-card);
}
.search-inner:focus-within{border-color:var(--p);box-shadow:0 0 0 4px rgba(214,51,132,0.08);}
.search-inner i{color:var(--p);font-size:.9rem;}
.search-inner input{border:none;outline:none;background:transparent;flex:1;font-family:'DM Sans',sans-serif;font-size:.88rem;color:var(--rose-dark);}
.search-inner input::placeholder{color:var(--rose-muted);}

/* ── SECTION LABELS ── */
.sec-label{
  padding:22px 20px 4px;
  font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;
  color:var(--rose-dark);display:flex;align-items:center;gap:10px;
}
.sec-label span{font-family:'DM Sans',sans-serif;font-size:.72rem;font-weight:600;color:var(--rose-muted);letter-spacing:.6px;margin-left:2px;}
.sec-label::after{content:'';flex:1;height:1px;background:linear-gradient(to right,rgba(214,51,132,0.2),transparent);}

/* ── CATEGORIES ── */
.cat-scroll{display:flex;gap:14px;overflow-x:auto;padding:10px 18px 16px;scrollbar-width:none;}
.cat-scroll::-webkit-scrollbar{display:none;}
.cat-btn{border:none;background:none;display:flex;flex-direction:column;align-items:center;gap:7px;cursor:pointer;padding:2px;flex-shrink:0;transition:transform .2s;}
.cat-btn:hover{transform:translateY(-2px);}
.cat-img-wrap{
  width:66px;height:66px;border-radius:50%;overflow:hidden;
  background:#fff;border:2px solid rgba(214,51,132,0.15);
  display:flex;align-items:center;justify-content:center;
  transition:all .25s;flex-shrink:0;
  box-shadow:var(--shadow-card);
}
.cat-img-wrap img{width:100%;height:100%;object-fit:cover;}
.cat-icon-inner{font-size:1.5rem;color:var(--p);}
.cat-btn.active .cat-img-wrap{
  border-color:var(--p);background:#fff5f9;
  box-shadow:0 0 0 4px rgba(214,51,132,0.14),0 4px 16px rgba(214,51,132,0.18);
}
.cat-lbl{font-size:.7rem;font-weight:600;color:var(--rose-mid);white-space:nowrap;max-width:72px;overflow:hidden;text-overflow:ellipsis;text-align:center;}
.cat-btn.active .cat-lbl{color:var(--p);}

/* ── PRODUCT GRID ── */
.prod-section{padding:6px 14px 110px;}
.prod-grid{display:grid;grid-template-columns:1fr 1fr;gap:13px;}

.prod-card{
  background:var(--card);border-radius:var(--radius-xl);overflow:hidden;
  box-shadow:var(--shadow-card);
  border:1px solid rgba(255,255,255,0.8);
  transition:transform .3s cubic-bezier(.34,1.56,.64,1),box-shadow .3s;
  position:relative;
}
.prod-card:hover{
  transform:translateY(-6px) scale(1.01);
  box-shadow:0 16px 40px rgba(139,0,67,0.16);
}

.prod-img-wrap{position:relative;aspect-ratio:1;overflow:hidden;background:var(--blush);}
.prod-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .5s cubic-bezier(.25,.46,.45,.94);}
.prod-card:hover .prod-img-wrap img{transform:scale(1.09);}

/* placeholder */
.prod-img-ph{
  width:100%;aspect-ratio:1;
  background:linear-gradient(135deg,#fce8f0 0%,#f9d0e2 100%);
  display:flex;align-items:center;justify-content:center;font-size:3rem;color:#e8a0bc;
}

/* badges on product */
.bdg-disc{
  position:absolute;top:10px;right:10px;
  background:var(--p);color:#fff;
  font-size:.64rem;font-weight:700;padding:4px 10px;border-radius:var(--radius-pill);
  z-index:2;letter-spacing:.4px;box-shadow:0 2px 8px rgba(214,51,132,0.4);
}
.bdg-feat{
  position:absolute;top:10px;left:10px;
  background:linear-gradient(135deg,#ffd700,#ffb300);color:#2a1800;
  font-size:.61rem;font-weight:800;padding:4px 10px;border-radius:var(--radius-pill);
  z-index:2;letter-spacing:.2px;box-shadow:0 2px 8px rgba(255,180,0,0.35);
}

/* sold out */
.oos-layer{
  position:absolute;inset:0;
  background:rgba(253,248,245,0.65);backdrop-filter:blur(3px);
  display:flex;align-items:center;justify-content:center;z-index:3;
}
.oos-badge{
  background:rgba(45,10,24,0.85);color:#fff;
  font-size:.68rem;font-weight:800;padding:7px 16px;
  border-radius:var(--radius-pill);letter-spacing:1px;
}

/* wishlist quick-action */
.prod-wishlist{
  position:absolute;top:9px;right:10px;z-index:4;
  width:30px;height:30px;border-radius:50%;
  background:rgba(255,255,255,0.9);backdrop-filter:blur(6px);
  border:none;cursor:pointer;display:none;align-items:center;justify-content:center;
  font-size:.78rem;color:var(--p);transition:background .2s,transform .2s;
}
.prod-img-wrap:hover .prod-wishlist{display:flex;}

/* card body */
.prod-body{padding:12px 13px 14px;}
.prod-cat-tag{font-size:.62rem;font-weight:700;color:var(--p);letter-spacing:.5px;margin-bottom:4px;opacity:.8;}
.prod-name{font-size:.85rem;font-weight:600;color:var(--rose-dark);margin-bottom:7px;line-height:1.38;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;}
.prod-prices{display:flex;align-items:baseline;gap:6px;margin-bottom:11px;}
.prod-price{font-family:'Playfair Display',serif;font-size:1.05rem;font-weight:700;color:var(--p);}
.prod-orig{font-size:.75rem;color:#c0a0b0;text-decoration:line-through;}

/* Rating stars */
.prod-stars{display:flex;align-items:center;gap:3px;margin-bottom:9px;}
.prod-stars i{font-size:.6rem;color:#ffc107;}
.prod-stars span{font-size:.65rem;color:var(--rose-muted);margin-left:2px;}

/* Add button */
.add-btn{
  width:100%;padding:10px;
  background:linear-gradient(135deg,var(--p) 0%,var(--s) 100%);
  color:#fff;border:none;border-radius:var(--radius-md);
  font-family:'DM Sans',sans-serif;font-weight:600;font-size:.8rem;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:7px;
  transition:opacity .2s,transform .15s,box-shadow .2s;
  box-shadow:0 4px 14px rgba(214,51,132,0.28);
  letter-spacing:.2px;
}
.add-btn:hover{opacity:.9;box-shadow:0 6px 20px rgba(214,51,132,0.4);}
.add-btn:active{transform:scale(.96);}
.add-btn:disabled{background:linear-gradient(135deg,#e8d8e0,#ddd0d8);color:#c0a0b0;cursor:not-allowed;box-shadow:none;}

/* ── EMPTY STATE ── */
.empty-state{text-align:center;padding:60px 20px;color:var(--rose-muted);}
.empty-state i{font-size:3.5rem;margin-bottom:16px;display:block;color:#e8a0bc;}
.empty-state p{font-family:'Playfair Display',serif;font-size:1.2rem;color:var(--rose-mid);}

/* ── FOOTER ── */
.footer{
  background:var(--rose-dark);color:#fff;
  padding:36px 24px 30px;text-align:center;
  position:relative;overflow:hidden;
}
.footer::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 20% 0%,rgba(214,51,132,0.25) 0%,transparent 60%),
             radial-gradient(ellipse at 80% 100%,rgba(139,0,67,0.3) 0%,transparent 60%);
}
.footer > *{position:relative;z-index:1;}
.footer-name{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:700;letter-spacing:.3px;margin-bottom:6px;}
.footer-tagline{font-family:'Playfair Display',serif;font-style:italic;font-size:.9rem;opacity:.6;margin-bottom:20px;}
.footer-divider{width:40px;height:1px;background:rgba(214,51,132,0.5);margin:0 auto 18px;}
.footer-line{display:flex;align-items:center;justify-content:center;gap:9px;font-size:.82rem;margin-bottom:9px;opacity:.85;}
.footer-line i{font-size:.85rem;color:var(--p);}
.footer-line a{color:#fff;text-decoration:none;}
.footer-cod-tag{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(214,51,132,0.2);border:1px solid rgba(214,51,132,0.35);
  color:#fff;font-size:.72rem;font-weight:700;
  padding:6px 16px;border-radius:var(--radius-pill);
  margin:12px 0 20px;letter-spacing:.4px;
}
.footer-powered{font-size:.7rem;opacity:.45;line-height:2;}
.footer-powered a{color:#fff;text-decoration:none;}

/* ── ANIMATIONS ── */
.fade-up{opacity:0;transform:translateY(28px);transition:opacity .6s ease,transform .6s ease;}
.fade-up.visible{opacity:1;transform:none;}

@keyframes shimmer{0%{background-position:200% 0;}100%{background-position:-200% 0;}}

/* ── CART OVERLAY & DRAWER ── */
#cartOverlay{display:none;position:fixed;inset:0;background:rgba(20,5,12,0.55);z-index:900;backdrop-filter:blur(2px);}

#cartDrawer{
  position:fixed;bottom:0;left:50%;
  transform:translateX(-50%) translateY(105%);
  width:100%;max-width:480px;
  background:#fff;border-radius:28px 28px 0 0;
  z-index:901;transition:transform .38s cubic-bezier(.32,0,.67,0);
  max-height:88vh;overflow-y:auto;
  box-shadow:0 -20px 60px rgba(0,0,0,0.22);
}
#cartDrawer.open{transform:translateX(-50%) translateY(0);}

/* Drawer inner styles */
#cartDrawer .drawer-handle{width:44px;height:4px;background:#e5e7eb;border-radius:2px;margin:14px auto 18px;}
#cartDrawer .drawer-header{
  display:flex;justify-content:space-between;align-items:center;
  padding:0 22px 16px;border-bottom:1px solid #f5f5f5;
}
#cartDrawer .drawer-title{font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:700;color:var(--rose-dark);}
#cartDrawer .drawer-close{
  background:#f5f5f5;border:none;width:32px;height:32px;border-radius:50%;
  font-size:1.1rem;cursor:pointer;color:#888;display:flex;align-items:center;justify-content:center;
  transition:background .2s;
}
#cartDrawer .drawer-close:hover{background:#ffe4ef;color:var(--p);}

/* Cart items */
.c-item{display:flex;gap:13px;padding:14px 22px;border-bottom:1px solid #fafafa;}
.c-img{
  width:58px;height:58px;border-radius:var(--radius-md);overflow:hidden;
  background:#fce8f0;flex-shrink:0;display:flex;align-items:center;justify-content:center;color:#e8a0bc;
  font-size:1.3rem;
}
.c-img img{width:100%;height:100%;object-fit:cover;}
.c-info{flex:1;}
.c-name{font-size:.87rem;font-weight:600;color:var(--rose-dark);margin-bottom:2px;line-height:1.3;}
.c-price{font-family:'Playfair Display',serif;font-weight:700;font-size:.98rem;color:var(--p);margin-bottom:7px;}
.c-qty-row{display:flex;align-items:center;gap:8px;}
.c-qty-btn{
  width:28px;height:28px;border-radius:50%;
  border:1.5px solid #ececec;background:#fff;cursor:pointer;
  font-size:.9rem;display:flex;align-items:center;justify-content:center;
  transition:border-color .2s,background .2s;
}
.c-qty-btn:hover{border-color:var(--p);background:#fff5f9;}
.c-qty-val{font-weight:700;min-width:22px;text-align:center;font-size:.9rem;}
.c-del{border-color:#fee2e2;color:#ef4444;}
.c-del:hover{border-color:#ef4444;background:#fff5f5;}

/* Cart summary */
#cartSummary{padding:14px 22px;}
.c-sum-row{display:flex;justify-content:space-between;padding:5px 0;font-size:.88rem;color:var(--rose-mid);}
.c-total{font-weight:700;font-size:1rem;color:var(--rose-dark);padding-top:12px;border-top:1.5px dashed #f0d0df;margin-top:6px;}
.c-min-warn{color:#ef4444;font-size:.76rem;text-align:center;margin-top:8px;font-weight:600;}

/* Checkout form */
#checkoutSection{padding:18px 22px 28px;}
#checkoutSection h4{font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;margin-bottom:16px;color:var(--rose-dark);}
.ck-field{margin-bottom:12px;}
.ck-field label{display:block;font-size:.74rem;font-weight:700;margin-bottom:5px;color:var(--rose-muted);letter-spacing:.4px;}
.ck-field input,.ck-field textarea{
  width:100%;padding:11px 15px;
  border:1.5px solid #ece8f0;border-radius:var(--radius-md);
  font-family:'DM Sans',sans-serif;font-size:.88rem;color:var(--rose-dark);
  transition:border-color .2s,box-shadow .2s;background:#fdf8f5;
}
.ck-field input:focus,.ck-field textarea:focus{outline:none;border-color:var(--p);box-shadow:0 0 0 3px rgba(214,51,132,0.08);background:#fff;}
.ck-btn{
  width:100%;padding:15px;border:none;border-radius:var(--radius-lg);
  background:linear-gradient(135deg,#25D366 0%,#128C7E 100%);
  color:#fff;font-weight:700;font-size:1rem;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:9px;
  box-shadow:0 6px 20px rgba(37,211,102,0.35);
  transition:opacity .2s,transform .15s;letter-spacing:.2px;
}
.ck-btn:hover{opacity:.92;}
.ck-btn:active{transform:scale(.98);}

/* ── TOAST ── */
.tap-toast{
  position:fixed;top:22px;left:50%;
  transform:translateX(-50%) translateY(-90px);
  background:var(--rose-dark);color:#fff;
  padding:11px 24px;border-radius:var(--radius-pill);
  font-weight:600;font-size:.85rem;z-index:9999;
  transition:transform .38s cubic-bezier(.34,1.56,.64,1);white-space:nowrap;
  box-shadow:0 8px 24px rgba(0,0,0,0.2);
  display:flex;align-items:center;gap:8px;
}
.tap-toast.show{transform:translateX(-50%) translateY(0);}
.tap-toast.error{background:#c0392b;}
.tap-toast.success{background:linear-gradient(135deg,var(--p),var(--s));}

/* ── FLOAT CART ── */
#floatCart{
  display:none;position:fixed;bottom:22px;left:50%;
  transform:translateX(-50%);
  background:linear-gradient(135deg,var(--p),var(--s));
  color:#fff;padding:14px 26px;border-radius:var(--radius-pill);
  cursor:pointer;z-index:850;align-items:center;gap:12px;
  box-shadow:0 10px 36px rgba(214,51,132,0.45);
  max-width:92vw;font-weight:700;white-space:nowrap;
  animation:cart-pop .35s cubic-bezier(.34,1.56,.64,1);
  font-family:'DM Sans',sans-serif;
}
@keyframes cart-pop{from{transform:translateX(-50%) scale(.8);opacity:0;}to{transform:translateX(-50%) scale(1);opacity:1;}}
#cartBubble{
  background:rgba(255,255,255,0.25);border:1.5px solid rgba(255,255,255,0.5);
  width:26px;height:26px;border-radius:50%;
  display:inline-flex;align-items:center;justify-content:center;
  font-size:.82rem;font-weight:800;
}
</style>
</head>
<body>
<div class="wrap">

  <!-- ─── TOP BAR ─── -->
  <div class="top-bar">
    <div class="top-logo-row">
      <?php if($logo): ?>
        <img src="<?= $logo ?>" alt="logo" class="logo-circle">
      <?php else: ?>
        <div class="logo-placeholder"><?= mb_substr($shopName,0,1) ?></div>
      <?php endif; ?>
      <div>
        <div class="top-name"><?= $shopName ?></div>
        <?php if($shopTagline): ?><div class="top-tagline-small"><?= $shopTagline ?></div><?php endif; ?>
      </div>
    </div>
    <?php if($waPhone): ?>
      <a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="top-wa" title="Chat on WhatsApp"><i class="fab fa-whatsapp"></i></a>
    <?php endif; ?>
  </div>

  <!-- ─── HERO BANNER ─── -->
  <div class="hero">
    <?php if($bannerImg): ?>
      <img src="<?= $bannerImg ?>" alt="<?= $shopName ?>">
    <?php else: ?>
      <div class="hero-bg">
        <div class="hero-bg-circles"><span></span><span></span><span></span></div>
        <i class="fas fa-spa hero-bg-icon"></i>
      </div>
    <?php endif; ?>
    <div class="hero-overlay"></div>
    <div class="hero-badge"><i class="fas fa-star" style="font-size:.6rem;"></i> &nbsp;PREMIUM BEAUTY</div>
  </div>

  <!-- ─── STORE INFO ─── -->
  <div class="store-info fade-up">
    <h1><?= $shopName ?></h1>
    <?php if($shopTagline): ?><div class="store-tagline"><?= $shopTagline ?></div><?php endif; ?>
    <div class="divider-ornament"><i class="fas fa-heart"></i></div>
    <div class="badges-row">
      <?php if($codAvail): ?><span class="badge badge-p"><i class="fas fa-money-bill-wave"></i> COD Available</span><?php endif; ?>
      <?php if($deliveryFee > 0): ?><span class="badge"><i class="fas fa-truck"></i> Delivery <?= $currency.number_format($deliveryFee,2) ?></span><?php endif; ?>
      <?php if($deliveryFee == 0): ?><span class="badge badge-p"><i class="fas fa-truck"></i> Free Delivery</span><?php endif; ?>
    </div>
  </div>

  <!-- ─── STATS STRIP ─── -->
  <div class="stats-strip fade-up">
    <div class="stat-item">
      <span class="stat-num"><?= count($products) ?>+</span>
      <span class="stat-lbl">PRODUCTS</span>
    </div>
    <div class="stat-item">
      <span class="stat-num"><?= count($categories) ?>+</span>
      <span class="stat-lbl">CATEGORIES</span>
    </div>
    <div class="stat-item">
      <span class="stat-num">100%</span>
      <span class="stat-lbl">AUTHENTIC</span>
    </div>
  </div>

  <?php if($shopDesc): ?>
  <div class="store-desc-block fade-up">
    <?= htmlspecialchars($shopDesc) ?>
  </div>
  <?php endif; ?>

  <!-- ─── SEARCH ─── -->
  <?php if($showSearch): ?>
  <div class="search-wrap fade-up">
    <div class="search-inner">
      <i class="fas fa-search"></i>
      <input type="text" id="searchInput" placeholder="Search products…" oninput="searchProducts()">
    </div>
  </div>
  <?php endif; ?>

  <!-- ─── CATEGORIES ─── -->
  <?php if($showCats): ?>
  <div class="sec-label fade-up">Categories <span>BROWSE ALL</span></div>
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

  <!-- ─── PRODUCTS ─── -->
  <div class="sec-label fade-up">Our Products <span>NEW ARRIVALS</span></div>
  <div class="prod-section">
    <?php if(!empty($products)): ?>
    <div class="prod-grid">
      <?php foreach($products as $i => $p): ?>
      <div class="prod-card fade-up"
           data-cat="<?= $p['category_id'] ?>"
           data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
           style="transition-delay:<?= ($i % 8) * 0.065 ?>s">

        <div class="prod-img-wrap">
          <?php if($p['img']): ?>
            <img src="<?= $p['img'] ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
          <?php else: ?>
            <div class="prod-img-ph"><i class="fas fa-spa"></i></div>
          <?php endif; ?>

          <?php if($p['has_discount'] && $p['in_stock']): ?>
            <span class="bdg-disc">−<?= $p['disc_pct'] ?>%</span>
          <?php endif; ?>
          <?php if(!empty($p['is_featured'])): ?>
            <span class="bdg-feat">★ Featured</span>
          <?php endif; ?>
          <?php if(!$p['in_stock']): ?>
            <div class="oos-layer"><span class="oos-badge">SOLD OUT</span></div>
          <?php endif; ?>
        </div>

        <div class="prod-body">
          <div class="prod-name"><?= htmlspecialchars($p['name']) ?></div>
          <div class="prod-prices">
            <span class="prod-price"><?= $currency . number_format($p['effective'],2) ?></span>
            <?php if($p['has_discount']): ?><span class="prod-orig"><?= $currency . number_format($p['price'],2) ?></span><?php endif; ?>
          </div>
          <?php if($p['in_stock']): ?>
          <button class="add-btn" onclick="addToCart(<?= $p['id'] ?>,'<?= htmlspecialchars(addslashes($p['name'])) ?>',<?= $p['effective'] ?>,'<?= htmlspecialchars($p['img'] ?? '') ?>')">
            <i class="fas fa-shopping-bag"></i> Add to Bag
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

  <!-- ─── FOOTER ─── -->
  <div class="footer">
    <div class="footer-name"><?= $shopName ?></div>
    <?php if($shopTagline): ?><div class="footer-tagline"><?= $shopTagline ?></div><?php endif; ?>
    <div class="footer-divider"></div>
    <?php if($address): ?><div class="footer-line"><i class="fas fa-map-marker-alt"></i><span><?= $address ?></span></div><?php endif; ?>
    <?php if($phone): ?><div class="footer-line"><i class="fas fa-phone"></i><a href="tel:<?= $phone ?>"><?= $phone ?></a></div><?php endif; ?>
    <?php if($email): ?><div class="footer-line"><i class="fas fa-envelope"></i><a href="mailto:<?= $email ?>"><?= $email ?></a></div><?php endif; ?>
    <?php if($waPhone): ?><div class="footer-line"><i class="fab fa-whatsapp"></i><a href="https://wa.me/<?= $waPhone ?>" target="_blank">Chat on WhatsApp</a></div><?php endif; ?>
    <?php if($codAvail): ?><div><span class="footer-cod-tag"><i class="fas fa-money-bill-wave"></i> Cash on Delivery Available</span></div><?php endif; ?>
    <div class="footer-powered">
      <p>Powered by <a href="/">Tapify</a> &nbsp;·&nbsp; A unit of <strong>Mr Print World</strong></p>
    </div>
  </div>

</div><!-- /.wrap -->

<!-- ─── CART OVERLAY ─── -->
<div id="cartOverlay" onclick="closeCart()"></div>

<!-- ─── CART DRAWER ─── -->
<div id="cartDrawer">
  <div class="drawer-handle"></div>
  <div class="drawer-header">
    <span class="drawer-title">🛍️ Your Bag</span>
    <button class="drawer-close" onclick="closeCart()">×</button>
  </div>
  <div id="cartList"></div>
  <div id="cartSummary"></div>
  <form id="checkoutSection" onsubmit="placeOrder(event)" style="display:none;">
    <h4>Delivery Details</h4>
    <input type="hidden" name="store_id" value="<?= $storeId ?>">
    <div class="ck-field"><label>YOUR NAME *</label><input name="name" required placeholder="Full name"></div>
    <div class="ck-field"><label>PHONE / WHATSAPP *</label><input name="phone" type="tel" required placeholder="WhatsApp number"></div>
    <div class="ck-field"><label>DELIVERY ADDRESS</label><textarea name="address" rows="2" placeholder="Street, city, pincode…"></textarea></div>
    <div class="ck-field"><label>SPECIAL INSTRUCTIONS</label><textarea name="notes" rows="2" placeholder="Any notes for the store…"></textarea></div>
    <button type="submit" class="ck-btn"><i class="fab fa-whatsapp" style="font-size:1.1rem;"></i> Place Order on WhatsApp</button>
  </form>
</div>

<!-- ─── FLOATING CART ─── -->
<div id="floatCart" onclick="openCart()">
  <span id="cartBubble">0</span>
  <span><i class="fas fa-shopping-bag" style="margin-right:4px;font-size:.9rem;"></i>View Bag</span>
  <span id="floatTotal" style="font-size:.88rem;opacity:.85;"><?= $currency ?>0</span>
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
    showToast('✓ Added to bag!');
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
function getCount() { return cart.reduce((s,i) => s+i.qty, 0); }
function getSub()   { return cart.reduce((s,i) => s+i.qty*i.price, 0); }
function updateCartUI() {
    const count = getCount(), sub = getSub(), total = sub + STORE_DATA.deliveryFee;
    document.getElementById('cartBubble').textContent = count;
    document.getElementById('floatTotal').textContent = STORE_DATA.currency + total.toFixed(2);
    document.getElementById('floatCart').style.display = count > 0 ? 'flex' : 'none';
}
function openCart() {
    if (!cart.length) { showToast('Your bag is empty','error'); return; }
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
            <div class="c-img">${item.img ? `<img src="${item.img}" alt="">` : '<i class="fas fa-spa"></i>'}</div>
            <div class="c-info">
                <div class="c-name">${item.name}</div>
                <div class="c-price">${STORE_DATA.currency}${item.price.toFixed(2)}</div>
                <div class="c-qty-row">
                    <button class="c-qty-btn" onclick="changeQty(${item.id},-1)">−</button>
                    <span class="c-qty-val">${item.qty}</span>
                    <button class="c-qty-btn" onclick="changeQty(${item.id},1)">+</button>
                    <button class="c-qty-btn c-del" onclick="removeFromCart(${item.id})"><i class="fas fa-trash-alt" style="font-size:.7rem;"></i></button>
                </div>
            </div>
        </div>`).join('');
    document.getElementById('cartSummary').innerHTML = `
        <div class="c-sum-row"><span>Subtotal</span><span>${STORE_DATA.currency}${sub.toFixed(2)}</span></div>
        ${STORE_DATA.deliveryFee>0?`<div class="c-sum-row"><span>Delivery</span><span>${STORE_DATA.currency}${STORE_DATA.deliveryFee.toFixed(2)}</span></div>`:'<div class="c-sum-row" style="color:#10b981;"><span>Delivery</span><span>FREE</span></div>'}
        <div class="c-sum-row c-total"><span>Total</span><span>${STORE_DATA.currency}${total.toFixed(2)}</span></div>
        ${sub<STORE_DATA.minOrder&&STORE_DATA.minOrder>0?`<p class="c-min-warn"><i class="fas fa-exclamation-circle"></i> Min order: ${STORE_DATA.currency}${STORE_DATA.minOrder.toFixed(2)}</p>`:''}`;
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
    setTimeout(()=>{t.classList.remove('show');setTimeout(()=>t.remove(),400);},2600);
}
const obs=new IntersectionObserver(entries=>entries.forEach(e=>{
    if(e.isIntersecting){e.target.classList.add('visible');obs.unobserve(e.target);}
}),{threshold:0.08});
document.querySelectorAll('.fade-up').forEach(el=>obs.observe(el));
</script>
</body>
</html>