<?php
/**
 * TAPIFY - WhatsApp Store Template Registry
 * ------------------------------------------------------------------
 * Single source of truth for store templates. store.php maps a
 * store's `template_id` to a template file + presentation config here.
 *
 * Business DATA never lives here — only PRESENTATION config
 * (labels, layout, default colors/fonts, which sections a template
 * renders). Switching templates only changes which entry is used;
 * the same store rows are rendered by whichever template is selected.
 *
 * ONLY 8 templates exist for the web store (store_template_9..16,
 * converted from webStoreTemps/Theme 1..8). The older v1 templates have
 * been DELETED. Any store still carrying a legacy template_id
 * (store_template_1..8, whatsapp_store_default, or empty) is transparently
 * mapped to its v2 equivalent by tapify_resolve_store_template_id() below,
 * so existing stores keep working and no store ever falls back to a
 * missing/old design.
 *
 * To add a future template: drop a new file in this folder and add
 * one entry below. No backend/schema change required.
 */

function tapify_store_templates(): array {
    return [
        // ── v2 full-width listing templates (webStoreTemps) — the only 8 offered ──
        'store_template_9' => [
            'file' => 'store-template-9-beauty.php', 'label' => 'Ethereal Beauty', 'vertical' => 'beauty', 'generation' => 'v2',
            'default_colors' => ['primary' => '#c29c77', 'secondary' => '#3d1f2b', 'accent' => '#e9c2cf', 'text' => '#2d0a18'],
            'fonts' => ['heading' => "'Playfair Display', serif", 'body' => "'DM Sans', sans-serif"],
            'layout' => 'sidebar-filter', 'product_cta' => 'Add to Cart', 'product_grid' => 3,
            'features' => ['price_range' => true, 'date_filter' => true, 'category_carousel' => false, 'pwa' => false],
        ],
        'store_template_10' => [
            'file' => 'store-template-10-ecommerce.php', 'label' => 'Prime Store', 'vertical' => 'ecommerce', 'generation' => 'v2',
            'default_colors' => ['primary' => '#2650d7', 'secondary' => '#0a2540', 'accent' => '#f6a609', 'text' => '#111827'],
            'fonts' => ['heading' => "'Poppins', sans-serif", 'body' => "'Poppins', sans-serif"],
            'layout' => 'sidebar-filter', 'product_cta' => 'Add to Cart', 'product_grid' => 4,
            'features' => ['price_range' => true, 'date_filter' => true, 'category_carousel' => false, 'pwa' => false],
        ],
        'store_template_11' => [
            'file' => 'store-template-11-restaurant.php', 'label' => 'Mahejbani', 'vertical' => 'restaurant', 'generation' => 'v2',
            'default_colors' => ['primary' => '#bf9157', 'secondary' => '#1d1d1d', 'accent' => '#f4a340', 'text' => '#1d1d1d'],
            'fonts' => ['heading' => "'Poppins', sans-serif", 'body' => "'Poppins', sans-serif"],
            'layout' => 'sidebar-filter', 'product_cta' => 'Add to Cart', 'product_grid' => 3,
            'features' => ['price_range' => true, 'date_filter' => true, 'category_carousel' => false, 'pwa' => false, 'no_discount' => true],
        ],
        'store_template_12' => [
            'file' => 'store-template-12-grocery.php', 'label' => 'Grocery Store', 'vertical' => 'grocery', 'generation' => 'v2',
            'default_colors' => ['primary' => '#72bf78', 'secondary' => '#141414', 'accent' => '#ffb300', 'text' => '#141414'],
            'fonts' => ['heading' => "'Poppins', sans-serif", 'body' => "'Poppins', sans-serif"],
            'layout' => 'sidebar-filter', 'product_cta' => 'Add to Cart', 'product_grid' => 4,
            'features' => ['price_range' => true, 'date_filter' => true, 'category_carousel' => false, 'pwa' => false],
        ],
        'store_template_13' => [
            'file' => 'store-template-13-cloth.php', 'label' => 'Cloth Store', 'vertical' => 'fashion', 'generation' => 'v2',
            'default_colors' => ['primary' => '#27262e', 'secondary' => '#27262e', 'accent' => '#c9a24a', 'text' => '#27262e'],
            'fonts' => ['heading' => "'Poppins', sans-serif", 'body' => "'Poppins', sans-serif"],
            'layout' => 'sidebar-filter', 'product_cta' => 'Add to Cart', 'product_grid' => 3,
            'features' => ['price_range' => true, 'date_filter' => true, 'category_carousel' => false, 'pwa' => false],
        ],
        'store_template_14' => [
            'file' => 'store-template-14-home-decor.php', 'label' => 'Home Decor', 'vertical' => 'decor', 'generation' => 'v2',
            'default_colors' => ['primary' => '#19496a', 'secondary' => '#19496a', 'accent' => '#d0a98f', 'text' => '#19496a'],
            'fonts' => ['heading' => "'Poppins', sans-serif", 'body' => "'Poppins', sans-serif"],
            'layout' => 'sidebar-filter', 'product_cta' => 'Add to Cart', 'product_grid' => 4,
            'features' => ['price_range' => true, 'date_filter' => true, 'category_carousel' => false, 'pwa' => false],
        ],
        'store_template_15' => [
            'file' => 'store-template-15-jewellery.php', 'label' => 'The Royal Jewellers', 'vertical' => 'jewellery', 'generation' => 'v2',
            'default_colors' => ['primary' => '#b8860b', 'secondary' => '#1a1408', 'accent' => '#e6c463', 'text' => '#1a1408'],
            'fonts' => ['heading' => "'Playfair Display', serif", 'body' => "'Poppins', sans-serif"],
            'layout' => 'carousel-cats', 'product_cta' => 'View More', 'product_grid' => 4,
            'features' => ['price_range' => false, 'date_filter' => false, 'category_carousel' => true, 'pwa' => true],
        ],
        'store_template_16' => [
            'file' => 'store-template-16-travel.php', 'label' => 'Desi Miles Travel', 'vertical' => 'travel', 'generation' => 'v2',
            'default_colors' => ['primary' => '#1e88a8', 'secondary' => '#0d3b4a', 'accent' => '#f4a340', 'text' => '#0d3b4a'],
            'fonts' => ['heading' => "'Poppins', sans-serif", 'body' => "'Poppins', sans-serif"],
            'layout' => 'sidebar-filter', 'product_cta' => 'Explore', 'product_grid' => 3,
            'features' => ['price_range' => true, 'date_filter' => true, 'category_carousel' => false, 'pwa' => false, 'paginate' => true],
        ],
    ];
}

/**
 * Legacy id (or any unrecognised value) => canonical v2 template id.
 * Vertical-matched 1:1 with the old templates: 1=beauty, 2=ecommerce,
 * 3=restaurant, 4=grocery, 5=cloth, 6=home-decor, 7=jewellery, 8=travel.
 * whatsapp_store_default / 'default' / empty => the generic e-commerce theme.
 */
function tapify_store_template_legacy_aliases(): array {
    return [
        'store_template_1' => 'store_template_9',
        'store_template_2' => 'store_template_10',
        'store_template_3' => 'store_template_11',
        'store_template_4' => 'store_template_12',
        'store_template_5' => 'store_template_13',
        'store_template_6' => 'store_template_14',
        'store_template_7' => 'store_template_15',
        'store_template_8' => 'store_template_16',
        'whatsapp_store_default' => 'store_template_10',
        'default' => 'store_template_10',
    ];
}

/** Default template for new/unset stores. */
function tapify_store_template_default(): string {
    return 'store_template_10';
}

/**
 * Resolve ANY template_id value (canonical, legacy, or unknown/empty) to a
 * canonical id that is guaranteed to exist in tapify_store_templates().
 * This is the single choke point that keeps only the 8 v2 themes reachable.
 */
function tapify_resolve_store_template_id(?string $templateId): string {
    $templateId = trim((string)$templateId);
    if ($templateId === '') return tapify_store_template_default();

    $canonical = tapify_store_templates();
    if (isset($canonical[$templateId])) return $templateId;

    $aliases = tapify_store_template_legacy_aliases();
    if (isset($aliases[$templateId])) return $aliases[$templateId];

    return tapify_store_template_default();
}

/** Resolve a template_id to its config. Always returns a valid config (never null). */
function tapify_store_template_config(?string $templateId): array {
    $all = tapify_store_templates();
    return $all[tapify_resolve_store_template_id($templateId)];
}

/** id => filename map for the 8 canonical templates. */
function tapify_store_template_map(): array {
    $map = [];
    foreach (tapify_store_templates() as $id => $cfg) {
        $map[$id] = $cfg['file'];
    }
    return $map;
}
