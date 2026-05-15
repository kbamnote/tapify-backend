<?php
/**
 * TAPIFY - Services Save API
 * POST /backend/api/services/save.php
 * Body: { id (optional), vcard_id, name, service_url, icon, display_order }
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

$input = getInput();
$vcardId = (int)($input['vcard_id'] ?? 0);
$serviceId = (int)($input['id'] ?? 0);
$name = sanitize($input['name'] ?? '');
$serviceUrl = trim($input['service_url'] ?? '');
$icon = sanitize($input['icon'] ?? '');
$displayOrder = (int)($input['display_order'] ?? 0);

if (!$vcardId) sendError('vCard ID required');
if (empty($name)) sendError('Service name is required');
if (strlen($name) > 200) sendError('Service name too long (max 200)');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify vCard ownership
    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    if ($serviceId > 0) {
        // Update existing - verify ownership through vcard
        $stmt = $pdo->prepare("UPDATE vcard_services SET name = ?, service_url = ?, icon = ?, display_order = ? WHERE id = ? AND vcard_id = ?");
        $stmt->execute([$name, $serviceUrl, $icon, $displayOrder, $serviceId, $vcardId]);

        if ($stmt->rowCount() === 0) {
            sendError('Service not found or no changes', 404);
        }

        sendSuccess('Service updated', ['id' => $serviceId]);
    } else {
        // Create new
        $stmt = $pdo->prepare("INSERT INTO vcard_services (vcard_id, name, service_url, icon, display_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$vcardId, $name, $serviceUrl, $icon, $displayOrder]);
        $newId = $pdo->lastInsertId();

        sendSuccess('Service added', ['id' => $newId]);
    }

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
