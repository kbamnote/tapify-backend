<?php
/**
 * Quick preview for Grocery Store template (store-template-12)
 * Run: php -S localhost:8888  then open http://localhost:8888/preview-grocery.php
 */

// Mock data
$store = [
    'store_name' => 'Fresh Mart Grocery',
    'logo_image' => '',
    'cover_image' => '',
    'whatsapp_number' => '919876543210',
    'address' => '123 Main Street, Mumbai',
    'delivery_charge' => 30,
    'min_order_amount' => 200,
    'order_message_template' => '🛍️ NEW ORDER\n\nName: {customer_name}\nPhone: {customer_phone}\n\n{items}\n\nTotal: {total}',
    'primary_color' => '#72bf78',
    'secondary_color' => '#141414',
    'accent_color' => '#ffb300',
    'seo_title' => '',
    'seo_description' => '',
    'favicon_image' => '',
    'enable_translate' => 0,
];

$categories = [
    ['id' => 1, 'name' => 'Bakery & Breads', 'image' => ''],
    ['id' => 2, 'name' => 'Snacks & Beverages', 'image' => ''],
    ['id' => 3, 'name' => 'Grains, Pulses & Flours', 'image' => ''],
    ['id' => 4, 'name' => 'Dairy', 'image' => ''],
    ['id' => 5, 'name' => 'Vegetables', 'image' => ''],
    ['id' => 6, 'name' => 'Fruits', 'image' => ''],
];

$products = [
    ['id'=>100,'name'=>'Brown Bread','category_id'=>1,'category_name'=>'Bakery & Breads','price'=>40,'discount_price'=>0,'image'=>'','in_stock'=>1,'is_featured'=>0,'created_at'=>date('Y-m-d H:i:s'),'img'=>'','effective_price'=>40,'has_discount'=>false,'disc_pct'=>0],
    ['id'=>101,'name'=>'Croissant','category_id'=>1,'category_name'=>'Bakery & Breads','price'=>45,'discount_price'=>0,'image'=>'','in_stock'=>1,'is_featured'=>0,'created_at'=>date('Y-m-d H:i:s'),'img'=>'','effective_price'=>45,'has_discount'=>false,'disc_pct'=>0],
    ['id'=>102,'name'=>'Garlic Bread','category_id'=>1,'category_name'=>'Bakery & Breads','price'=>40,'discount_price'=>0,'image'=>'','in_stock'=>1,'is_featured'=>0,'created_at'=>date('Y-m-d H:i:s'),'img'=>'','effective_price'=>40,'has_discount'=>false,'disc_pct'=>0],
    ['id'=>103,'name'=>'Multigrain Bread','category_id'=>1,'category_name'=>'Bakery & Breads','price'=>45,'discount_price'=>0,'image'=>'','in_stock'=>1,'is_featured'=>0,'created_at'=>date('Y-m-d H:i:s'),'img'=>'','effective_price'=>45,'has_discount'=>false,'disc_pct'=>0],
    ['id'=>104,'name'=>'White Bread','category_id'=>1,'category_name'=>'Bakery & Breads','price'=>35,'discount_price'=>0,'image'=>'','in_stock'=>1,'is_featured'=>0,'created_at'=>date('Y-m-d H:i:s'),'img'=>'','effective_price'=>35,'has_discount'=>false,'disc_pct'=>0],
    ['id'=>105,'name'=>'Potato Chips','category_id'=>2,'category_name'=>'Snacks & Beverages','price'=>20,'discount_price'=>0,'image'=>'','in_stock'=>1,'is_featured'=>0,'created_at'=>date('Y-m-d H:i:s'),'img'=>'','effective_price'=>20,'has_discount'=>false,'disc_pct'=>0],
    ['id'=>106,'name'=>'Chochalate Bar','category_id'=>2,'category_name'=>'Snacks & Beverages','price'=>40,'discount_price'=>0,'image'=>'','in_stock'=>1,'is_featured'=>0,'created_at'=>date('Y-m-d H:i:s'),'img'=>'','effective_price'=>40,'has_discount'=>false,'disc_pct'=>0],
    ['id'=>107,'name'=>'Instabt Noodles','category_id'=>2,'category_name'=>'Snacks & Beverages','price'=>99,'discount_price'=>0,'image'=>'','in_stock'=>1,'is_featured'=>0,'created_at'=>date('Y-m-d H:i:s'),'img'=>'','effective_price'=>99,'has_discount'=>false,'disc_pct'=>0],
];

$currency = '₹';
$storeId = 1;
$priceMin = 20;
$priceMax = 99;

$templateConfig = [
    'default_colors' => ['primary' => '#72bf78', 'secondary' => '#141414', 'accent' => '#ffb300'],
    'product_cta' => 'Add to Cart',
];

function imgUrl($path) {
    if (empty($path)) return '';
    if (strpos($path, 'http') === 0) return $path;
    return '/' . ltrim($path, '/');
}

// Include the template
require __DIR__ . '/webStore_templates/store-template-12-grocery.php';
