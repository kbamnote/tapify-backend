<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDB();
    
    $stmt = $pdo->query("SELECT * FROM review_funnels");
    $funnels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $testResult = null;
    if (count($funnels) > 0) {
        $slug = $funnels[0]['slug'];
        $stmt2 = $pdo->prepare("
            SELECT f.id, f.google_review_url, v.vcard_name as business_name, v.profile_image 
            FROM review_funnels f
            LEFT JOIN vcards v ON v.user_id = f.user_id AND v.status = 1
            WHERE f.slug = ? 
            LIMIT 1
        ");
        $stmt2->execute([$slug]);
        $testResult = $stmt2->fetch(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'status' => 'success',
        'database_funnel_count' => count($funnels),
        'all_funnels_in_database' => $funnels,
        'simulated_fetch_for_first_funnel' => $testResult ?: 'FETCH_FAILED'
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
