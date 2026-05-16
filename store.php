<?php
/**
 * TAPIFY - Public WhatsApp Store Display Page
 * Loads store + categories + products and shows beautiful e-commerce page
 */

require_once __DIR__ . '/config/database.php';

$alias = trim($_GET['alias'] ?? '');
if (empty($alias)) {
    header('Location: /');
    exit;
}

try {
    $pdo = getDB();

    $stmt = $pdo->prepare("SELECT * FROM whatsapp_stores WHERE url_alias = ? AND status = 1 LIMIT 1");
    $stmt->execute([$alias]);
    $store = $stmt->fetch();

    if (!$store) {
        http_response_code(404);
        die('<!DOCTYPE html><html><head><title>Store Not Found</title></head><body style="font-family:sans-serif;text-align:center;padding:50px"><h1>Store Not Found</h1></body></html>');
    }

    $storeId = $store['id'];

    // Increment view count
    $pdo->prepare("UPDATE whatsapp_stores SET view_count = view_count + 1 WHERE id = ?")->execute([$storeId]);

    // Load categories
    $categories = [];
    try {
        $stmt = $pdo->prepare("SELECT * FROM whatsapp_store_categories WHERE store_id = ? AND status = 1 ORDER BY display_order, id");
        $stmt->execute([$storeId]);
        $categories = $stmt->fetchAll();
    } catch (Exception $e) {}

    // Load products
    $products = [];
    try {
        $stmt = $pdo->prepare("SELECT * FROM whatsapp_store_products WHERE store_id = ? AND status = 1 ORDER BY is_featured DESC, display_order, id");
        $stmt->execute([$storeId]);
        $products = $stmt->fetchAll();
    } catch (Exception $e) {}

} catch (Exception $e) {
    die('Error loading store: ' . htmlspecialchars($e->getMessage()));
}

function imgUrl($path) {
    if (empty($path)) return '';
    if (strpos($path, 'http') === 0) return $path;
    return '/' . ltrim($path, '/');
}

$primaryColor = $store['primary_color'] ?? '#25D366';
$secondaryColor = $store['secondary_color'] ?? '#128C7E';
$currency = $store['currency_symbol'] ?? '₹';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($store['store_name']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($store['tagline'] ?? '') ?>">
    <link rel="icon" type="image/png" href="<?= $store['favicon_image'] ? imgUrl($store['favicon_image']) : '/images/tapify-logo-gold.png' ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary: <?= $primaryColor ?>; --secondary: <?= $secondaryColor ?>; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f9fafb; color: #1a2035; }
        .store-container { max-width: 600px; margin: 0 auto; background: white; min-height: 100vh; box-shadow: 0 0 40px rgba(0,0,0,0.08); padding-bottom: 80px; }

        /* Cover */
        .store-cover { height: 200px; background: linear-gradient(135deg, var(--primary), var(--secondary)); position: relative; overflow: hidden; }
        .store-cover img { width: 100%; height: 100%; object-fit: cover; }

        /* Header */
        .store-header { padding: 0 20px; margin-top: -50px; position: relative; z-index: 5; text-align: center; }
        .store-logo { width: 100px; height: 100px; border-radius: 50%; border: 5px solid white; box-shadow: 0 10px 30px rgba(0,0,0,0.15); background: white; margin: 0 auto 12px; overflow: hidden; }
        .store-logo img { width: 100%; height: 100%; object-fit: cover; }
        .store-logo-placeholder { width: 100%; height: 100%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; }
        .store-name { font-size: 1.5rem; font-weight: 700; color: #1a2035; margin-bottom: 4px; }
        .store-tagline { color: #6b7280; font-size: 0.9rem; margin-bottom: 8px; }
        .store-stats { display: flex; justify-content: center; gap: 16px; font-size: 0.78rem; color: #6b7280; margin-bottom: 16px; }
        .stat-item { display: flex; align-items: center; gap: 4px; }
        .stat-item i { color: var(--primary); }
        .store-desc { color: #4b5563; font-size: 0.88rem; line-height: 1.6; padding: 0 10px; margin-bottom: 16px; }

        /* Quick Actions */
        .quick-actions { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; padding: 0 20px 20px; }
        .quick-action { background: #f9fafb; border-radius: 12px; padding: 12px; text-align: center; text-decoration: none; color: inherit; transition: all 0.3s; }
        .quick-action:hover { background: var(--primary); color: white; transform: translateY(-2px); }
        .quick-action:hover i { color: white; }
        .quick-action i { font-size: 1.3rem; color: var(--primary); margin-bottom: 4px; }
        .quick-action span { display: block; font-size: 0.75rem; font-weight: 600; }

        /* Search */
        .search-bar { padding: 0 20px 16px; }
        .search-input { width: 100%; padding: 12px 16px 12px 44px; border: 1.5px solid #e5e7eb; border-radius: 50px; background: #f9fafb url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="%236b7280"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>') no-repeat 16px center; font-family: inherit; font-size: 0.92rem; }
        .search-input:focus { outline: none; border-color: var(--primary); }

        /* Categories */
        .categories-section { padding: 0 0 20px; }
        .section-title { padding: 0 20px; font-size: 1.05rem; font-weight: 700; margin-bottom: 12px; }
        .categories-scroll { display: flex; gap: 10px; padding: 0 20px; overflow-x: auto; scrollbar-width: none; }
        .categories-scroll::-webkit-scrollbar { display: none; }
        .category-pill { background: #f3f4f6; border: 2px solid transparent; border-radius: 50px; padding: 8px 18px; font-size: 0.85rem; font-weight: 600; white-space: nowrap; cursor: pointer; transition: all 0.3s; }
        .category-pill.active, .category-pill:hover { background: var(--primary); color: white; border-color: var(--primary); }

        /* Products */
        .products-section { padding: 20px; }
        .products-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .product-card { background: white; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; transition: all 0.3s; cursor: pointer; }
        .product-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); border-color: var(--primary); }
        .product-image { width: 100%; aspect-ratio: 1; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #9ca3af; overflow: hidden; position: relative; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-image i { font-size: 2.5rem; }
        .featured-badge { position: absolute; top: 8px; left: 8px; background: #f59e0b; color: white; padding: 3px 8px; border-radius: 50px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; }
        .out-of-stock-badge { position: absolute; top: 8px; right: 8px; background: #ef4444; color: white; padding: 3px 8px; border-radius: 50px; font-size: 0.65rem; font-weight: 700; }
        .product-info { padding: 10px 12px; }
        .product-name { font-weight: 600; font-size: 0.85rem; line-height: 1.3; margin-bottom: 6px; height: 2.6em; overflow: hidden; }
        .product-price-row { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .product-price { color: var(--primary); font-weight: 700; font-size: 1rem; }
        .product-original-price { color: #9ca3af; text-decoration: line-through; font-size: 0.78rem; }
        .add-to-cart-btn { background: var(--primary); color: white; border: none; padding: 8px; border-radius: 8px; cursor: pointer; font-family: inherit; width: 100%; margin-top: 8px; font-size: 0.78rem; font-weight: 600; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 4px; }
        .add-to-cart-btn:hover { background: var(--secondary); }
        .add-to-cart-btn:disabled { background: #cbd5e1; cursor: not-allowed; }

        /* Empty state */
        .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .empty-state i { font-size: 4rem; margin-bottom: 12px; opacity: 0.4; }

        /* Floating Cart */
        .floating-cart { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: var(--primary); color: white; padding: 14px 24px; border-radius: 50px; box-shadow: 0 15px 30px rgba(37,211,102,0.4); display: none; align-items: center; gap: 12px; cursor: pointer; z-index: 100; max-width: 92vw; }
        .floating-cart.show { display: flex; }
        .floating-cart-count { background: white; color: var(--primary); width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; }
        .floating-cart-text { font-weight: 600; }
        .floating-cart-total { margin-left: auto; font-weight: 700; }

        /* Cart Modal */
        .cart-modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 1000; display: none; align-items: flex-end; justify-content: center; }
        .cart-modal-overlay.show { display: flex; }
        .cart-modal { background: white; width: 100%; max-width: 600px; max-height: 85vh; border-radius: 20px 20px 0 0; padding: 24px; overflow-y: auto; }
        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb; }
        .cart-header h3 { font-size: 1.2rem; }
        .cart-close { background: transparent; border: none; font-size: 1.8rem; cursor: pointer; color: #6b7280; }
        .cart-item { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
        .cart-item-image { width: 60px; height: 60px; border-radius: 10px; background: #f3f4f6; flex-shrink: 0; overflow: hidden; }
        .cart-item-image img { width: 100%; height: 100%; object-fit: cover; }
        .cart-item-info { flex: 1; }
        .cart-item-name { font-weight: 600; font-size: 0.9rem; margin-bottom: 4px; }
        .cart-item-price { color: var(--primary); font-weight: 700; }
        .qty-controls { display: flex; align-items: center; gap: 10px; margin-top: 6px; }
        .qty-btn { width: 28px; height: 28px; border-radius: 50%; border: 1px solid #e5e7eb; background: white; cursor: pointer; font-weight: 700; }
        .qty-btn:hover { background: var(--primary); color: white; border-color: var(--primary); }
        .cart-summary { margin-top: 16px; padding-top: 16px; border-top: 2px solid #e5e7eb; }
        .summary-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.92rem; }
        .summary-row.total { font-size: 1.1rem; font-weight: 700; color: var(--primary); border-top: 1px dashed #e5e7eb; padding-top: 12px; margin-top: 8px; }
        .checkout-form { margin-top: 16px; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 5px; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px 14px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-family: inherit; font-size: 0.9rem; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--primary); }
        .checkout-btn { background: var(--primary); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 700; cursor: pointer; width: 100%; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .checkout-btn:hover { background: var(--secondary); }

        .footer { text-align: center; padding: 20px; color: #9ca3af; font-size: 0.8rem; }
        .footer a { color: var(--primary); text-decoration: none; font-weight: 600; }

        .toast { position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-100px); background: #1a2035; color: white; padding: 12px 22px; border-radius: 50px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); transition: transform 0.4s; z-index: 9999; font-weight: 600; max-width: 92%; }
        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast.success { background: #10b981; }
        .toast.error { background: #ef4444; }
    </style>
</head>
<body>

<div class="store-container">

    <div class="store-cover">
        <?php if ($store['cover_image']): ?>
            <img src="<?= imgUrl($store['cover_image']) ?>" alt="Cover">
        <?php endif; ?>
    </div>

    <div class="store-header">
        <div class="store-logo">
            <?php if ($store['logo_image']): ?>
                <img src="<?= imgUrl($store['logo_image']) ?>" alt="Logo">
            <?php else: ?>
                <div class="store-logo-placeholder"><?= strtoupper(substr($store['store_name'], 0, 2)) ?></div>
            <?php endif; ?>
        </div>

        <h1 class="store-name"><?= htmlspecialchars($store['store_name']) ?></h1>
        <?php if (!empty($store['tagline'])): ?>
            <p class="store-tagline"><?= htmlspecialchars($store['tagline']) ?></p>
        <?php endif; ?>

        <div class="store-stats">
            <div class="stat-item"><i class="fas fa-eye"></i> <?= number_format((int)$store['view_count']) ?> views</div>
            <div class="stat-item"><i class="fas fa-shopping-bag"></i> <?= count($products) ?> products</div>
            <?php if ($store['cod_available']): ?>
                <div class="stat-item"><i class="fas fa-money-bill-wave"></i> COD Available</div>
            <?php endif; ?>
        </div>

        <?php if (!empty($store['description'])): ?>
            <p class="store-desc"><?= htmlspecialchars($store['description']) ?></p>
        <?php endif; ?>
    </div>

    <div class="quick-actions">
        <a href="https://wa.me/<?= $store['whatsapp_number'] ?>" target="_blank" class="quick-action">
            <i class="fab fa-whatsapp"></i><span>WhatsApp</span>
        </a>
        <?php if (!empty($store['phone'])): ?>
            <a href="tel:<?= htmlspecialchars($store['phone']) ?>" class="quick-action">
                <i class="fas fa-phone"></i><span>Call</span>
            </a>
        <?php endif; ?>
        <?php if (!empty($store['location_url']) || !empty($store['location'])): ?>
            <a href="<?= !empty($store['location_url']) ? htmlspecialchars($store['location_url']) : 'https://maps.google.com/?q=' . urlencode($store['location']) ?>" target="_blank" class="quick-action">
                <i class="fas fa-map-marker-alt"></i><span>Location</span>
            </a>
        <?php endif; ?>
    </div>

    <?php if ($store['show_search']): ?>
    <div class="search-bar">
        <input type="text" class="search-input" id="searchInput" placeholder="Search products..." oninput="filterProducts()">
    </div>
    <?php endif; ?>

    <?php if ($store['show_categories'] && count($categories) > 0): ?>
    <div class="categories-section">
        <h2 class="section-title">Categories</h2>
        <div class="categories-scroll">
            <button class="category-pill active" data-cat="all" onclick="filterByCategory('all')">All</button>
            <?php foreach ($categories as $cat): ?>
                <button class="category-pill" data-cat="<?= $cat['id'] ?>" onclick="filterByCategory('<?= $cat['id'] ?>')">
                    <?= htmlspecialchars($cat['name']) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="products-section">
        <h2 class="section-title">Products</h2>
        <?php if (count($products) === 0): ?>
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <p>No products yet. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="products-grid" id="productsGrid">
                <?php foreach ($products as $p):
                    $effectivePrice = $p['discount_price'] !== null && $p['discount_price'] > 0 ? (float)$p['discount_price'] : (float)$p['price'];
                ?>
                    <div class="product-card" data-cat="<?= $p['category_id'] ?? 'none' ?>" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>">
                        <div class="product-image">
                            <?php if (!empty($p['image'])): ?>
                                <img src="<?= imgUrl($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                            <?php else: ?>
                                <i class="fas fa-image"></i>
                            <?php endif; ?>
                            <?php if ($p['is_featured']): ?><span class="featured-badge">Featured</span><?php endif; ?>
                            <?php if (!$p['in_stock']): ?><span class="out-of-stock-badge">Out of Stock</span><?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                            <div class="product-price-row">
                                <span class="product-price"><?= $currency ?><?= number_format($effectivePrice, 2) ?></span>
                                <?php if ($p['discount_price'] !== null && $p['discount_price'] > 0): ?>
                                    <span class="product-original-price"><?= $currency ?><?= number_format((float)$p['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="add-to-cart-btn" <?= !$p['in_stock'] ? 'disabled' : '' ?> onclick="addToCart(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>', <?= $effectivePrice ?>, '<?= htmlspecialchars(imgUrl($p['image'] ?? '')) ?>')">
                                <i class="fas fa-cart-plus"></i> <?= $p['in_stock'] ? 'Add to Cart' : 'Sold Out' ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>Powered by <a href="/">Tapify</a></p>
        <p style="margin-top:5px;font-size:0.7rem;">© <?= date('Y') ?></p>
    </div>

</div>

<!-- Floating Cart -->
<div class="floating-cart" id="floatingCart" onclick="openCart()">
    <div class="floating-cart-count" id="cartCount">0</div>
    <div class="floating-cart-text">View Cart</div>
    <div class="floating-cart-total" id="cartTotal"><?= $currency ?>0</div>
</div>

<!-- Cart Modal -->
<div class="cart-modal-overlay" id="cartModal" onclick="if(event.target===this)closeCart()">
    <div class="cart-modal">
        <div class="cart-header">
            <h3>🛒 Your Cart</h3>
            <button class="cart-close" onclick="closeCart()">×</button>
        </div>
        <div id="cartItems"></div>
        <div class="cart-summary" id="cartSummary"></div>
        <form class="checkout-form" id="checkoutForm" onsubmit="placeOrder(event)" style="display:none;">
            <h3 style="margin-bottom:12px;font-size:1rem;">Your Details</h3>
            <input type="hidden" name="store_id" value="<?= $storeId ?>">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="name" required placeholder="Your full name">
            </div>
            <div class="form-group">
                <label>Phone *</label>
                <input type="tel" name="phone" required placeholder="WhatsApp number">
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="2" placeholder="Delivery address"></textarea>
            </div>
            <div class="form-group">
                <label>Notes (optional)</label>
                <textarea name="notes" rows="2" placeholder="Any special instructions"></textarea>
            </div>
            <button type="submit" class="checkout-btn">
                <i class="fab fa-whatsapp"></i> Order on WhatsApp
            </button>
        </form>
    </div>
</div>

<script>
const STORE_DATA = {
    id: <?= $storeId ?>,
    name: <?= json_encode($store['store_name']) ?>,
    whatsapp: <?= json_encode($store['whatsapp_number']) ?>,
    currency: <?= json_encode($currency) ?>,
    deliveryCharge: <?= (float)$store['delivery_charge'] ?>,
    minOrder: <?= (float)$store['min_order_amount'] ?>,
    template: <?= json_encode($store['order_message_template']) ?>
};

let cart = [];

function addToCart(id, name, price, image) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ id, name, price, image, qty: 1 });
    }
    updateCartUI();
    showToast('✓ Added to cart!', 'success');
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    updateCartUI();
    if (cart.length === 0) {
        document.getElementById('cartItems').innerHTML = '<p style="text-align:center;padding:30px;color:#9ca3af">Cart is empty</p>';
        document.getElementById('cartSummary').innerHTML = '';
        document.getElementById('checkoutForm').style.display = 'none';
    }
}

function updateQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (item) {
        item.qty = Math.max(1, item.qty + delta);
        updateCartUI();
    }
}

function updateCartUI() {
    const count = cart.reduce((sum, i) => sum + i.qty, 0);
    const total = cart.reduce((sum, i) => sum + i.qty * i.price, 0);

    document.getElementById('cartCount').textContent = count;
    document.getElementById('cartTotal').textContent = STORE_DATA.currency + total.toFixed(2);

    const floatCart = document.getElementById('floatingCart');
    floatCart.classList.toggle('show', count > 0);

    // Update modal if open
    if (document.getElementById('cartModal').classList.contains('show')) {
        renderCartModal();
    }
}

function openCart() {
    if (cart.length === 0) {
        showToast('Cart is empty', 'error');
        return;
    }
    renderCartModal();
    document.getElementById('cartModal').classList.add('show');
}

function closeCart() {
    document.getElementById('cartModal').classList.remove('show');
}

function renderCartModal() {
    const itemsDiv = document.getElementById('cartItems');
    const summaryDiv = document.getElementById('cartSummary');
    const form = document.getElementById('checkoutForm');

    itemsDiv.innerHTML = cart.map(item => `
        <div class="cart-item">
            <div class="cart-item-image">${item.image ? `<img src="${item.image}">` : '<i class="fas fa-image" style="display:flex;align-items:center;justify-content:center;height:100%;color:#9ca3af"></i>'}</div>
            <div class="cart-item-info">
                <div class="cart-item-name">${item.name}</div>
                <div class="cart-item-price">${STORE_DATA.currency}${item.price.toFixed(2)}</div>
                <div class="qty-controls">
                    <button class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                    <strong>${item.qty}</strong>
                    <button class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                    <button class="qty-btn" onclick="removeFromCart(${item.id})" style="margin-left:auto;color:#ef4444;border-color:#ef4444"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        </div>
    `).join('');

    const subtotal = cart.reduce((sum, i) => sum + i.qty * i.price, 0);
    const total = subtotal + STORE_DATA.deliveryCharge;

    summaryDiv.innerHTML = `
        <div class="summary-row"><span>Subtotal</span><span>${STORE_DATA.currency}${subtotal.toFixed(2)}</span></div>
        ${STORE_DATA.deliveryCharge > 0 ? `<div class="summary-row"><span>Delivery</span><span>${STORE_DATA.currency}${STORE_DATA.deliveryCharge.toFixed(2)}</span></div>` : ''}
        <div class="summary-row total"><span>Total</span><span>${STORE_DATA.currency}${total.toFixed(2)}</span></div>
        ${subtotal < STORE_DATA.minOrder ? `<p style="color:#ef4444;font-size:0.78rem;text-align:center;margin-top:8px;">Min order: ${STORE_DATA.currency}${STORE_DATA.minOrder.toFixed(2)}</p>` : ''}
    `;

    form.style.display = subtotal >= STORE_DATA.minOrder ? 'block' : 'none';
}

function placeOrder(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    if (cart.length === 0) {
        showToast('Cart is empty', 'error');
        return;
    }

    const subtotal = cart.reduce((sum, i) => sum + i.qty * i.price, 0);
    const total = subtotal + STORE_DATA.deliveryCharge;

    // Build WhatsApp message
    let itemsList = cart.map(i => `• ${i.name} x${i.qty} - ${STORE_DATA.currency}${(i.qty * i.price).toFixed(2)}`).join('\n');

    let template = STORE_DATA.template || `🛍️ NEW ORDER\n\nName: {customer_name}\nPhone: {customer_phone}\n\n{items}\n\nTotal: {total}`;
    let message = template
        .replace('{customer_name}', data.name)
        .replace('{customer_phone}', data.phone)
        .replace('{items}', itemsList)
        .replace('{total}', STORE_DATA.currency + total.toFixed(2));

    if (data.address) message += '\n\nAddress: ' + data.address;
    if (data.notes) message += '\nNotes: ' + data.notes;

    // Save order to backend (async, don't wait)
    fetch('/backend/store-order-submit.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            store_id: STORE_DATA.id,
            customer_name: data.name,
            customer_phone: data.phone,
            customer_address: data.address,
            items: cart,
            subtotal: subtotal,
            delivery_charge: STORE_DATA.deliveryCharge,
            total_amount: total,
            notes: data.notes
        })
    }).catch(err => console.error('Order save failed:', err));

    // Open WhatsApp
    const whatsappUrl = `https://wa.me/${STORE_DATA.whatsapp}?text=${encodeURIComponent(message)}`;
    window.location.href = whatsappUrl;
}

function filterProducts() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const matches = card.dataset.name.includes(query);
        card.style.display = matches ? '' : 'none';
    });
}

function filterByCategory(catId) {
    document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
    document.querySelector(`.category-pill[data-cat="${catId}"]`).classList.add('active');

    document.querySelectorAll('.product-card').forEach(card => {
        const matches = catId === 'all' || card.dataset.cat === catId;
        card.style.display = matches ? '' : 'none';
    });
}

function showToast(msg, type = 'success') {
    const old = document.querySelector('.toast');
    if (old) old.remove();
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 400); }, 2500);
}
</script>

</body>
</html>
