<?php
/**
 * TAPIFY - Storage Stats
 * GET /backend/api/storage/stats.php
 * Returns actual file usage statistics
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Get user's vCard IDs
    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE user_id = ?");
    $stmt->execute([$userId]);
    $vcardIds = array_column($stmt->fetchAll(), 'id');

    // Get user's store IDs
    $storeIds = [];
    try {
        $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE user_id = ?");
        $stmt->execute([$userId]);
        $storeIds = array_column($stmt->fetchAll(), 'id');
    } catch (Exception $e) {}

    // Calculate folder size recursively
    function getDirSize($path) {
        if (!is_dir($path)) return 0;
        $size = 0;
        $count = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
                $count++;
            }
        }
        return ['size' => $size, 'count' => $count];
    }

    $uploadsBase = __DIR__ . '/../../uploads/';

    // Categories
    $categories = [];

    // vCards (cover, profile, favicon, products, blogs, testimonials, gallery)
    $vcardSize = 0;
    $vcardCount = 0;
    foreach ($vcardIds as $vid) {
        $path = $uploadsBase . "vcard_$vid";
        if (is_dir($path)) {
            $r = getDirSize($path);
            $vcardSize += $r['size'];
            $vcardCount += $r['count'];
        }
    }
    $categories[] = [
        'name' => 'vCard Images',
        'icon' => 'fa-id-card',
        'color' => '#8338ec',
        'size' => $vcardSize,
        'count' => $vcardCount,
        'description' => 'Cover, profile, products, blogs, gallery'
    ];

    // Stores (logos, covers, products, categories)
    $storeSize = 0;
    $storeCount = 0;
    foreach ($storeIds as $sid) {
        $path = $uploadsBase . "store_$sid";
        if (is_dir($path)) {
            $r = getDirSize($path);
            $storeSize += $r['size'];
            $storeCount += $r['count'];
        }
    }
    $categories[] = [
        'name' => 'Store Images',
        'icon' => 'fa-store',
        'color' => '#25D366',
        'size' => $storeSize,
        'count' => $storeCount,
        'description' => 'Logos, covers, products'
    ];

    // Avatar
    $avatarSize = 0;
    $avatarCount = 0;
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    if ($row && $row['avatar']) {
        $avatarPath = __DIR__ . '/../../../' . $row['avatar'];
        if (file_exists($avatarPath) && is_file($avatarPath)) {
            $avatarSize = filesize($avatarPath);
            $avatarCount = 1;
        }
    }
    $categories[] = [
        'name' => 'Profile Avatar',
        'icon' => 'fa-user-circle',
        'color' => '#3b82f6',
        'size' => $avatarSize,
        'count' => $avatarCount,
        'description' => 'Your profile picture'
    ];

    // Total
    $totalSize = $vcardSize + $storeSize + $avatarSize;
    $totalCount = $vcardCount + $storeCount + $avatarCount;

    // Quota (assume 500MB free, configurable later)
    $quotaBytes = 500 * 1024 * 1024; // 500MB
    $usagePercent = $quotaBytes > 0 ? round(($totalSize / $quotaBytes) * 100, 2) : 0;

    // Format helper
    function formatBytes($bytes) {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    foreach ($categories as &$cat) {
        $cat['size_formatted'] = formatBytes($cat['size']);
        $cat['percentage'] = $totalSize > 0 ? round(($cat['size'] / $totalSize) * 100, 1) : 0;
    }

    sendSuccess('Storage stats loaded', [
        'total' => [
            'size' => $totalSize,
            'size_formatted' => formatBytes($totalSize),
            'count' => $totalCount,
            'quota' => $quotaBytes,
            'quota_formatted' => formatBytes($quotaBytes),
            'available' => $quotaBytes - $totalSize,
            'available_formatted' => formatBytes(max(0, $quotaBytes - $totalSize)),
            'usage_percent' => $usagePercent
        ],
        'categories' => $categories,
        'counts' => [
            'vcards' => count($vcardIds),
            'stores' => count($storeIds)
        ]
    ]);
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
