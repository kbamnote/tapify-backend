<?php
/**
 * TAPIFY - Create new vCard API
 * POST /backend/api/vcards/create.php
 * Body: { vcard_name, url_alias (optional), occupation, etc. }
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

$input = getInput();
$userId = getCurrentUserId();

// Required field
$vcardName = sanitize($input['vcard_name'] ?? '');
if (empty($vcardName)) {
    sendError('vCard name is required');
}

if (strlen($vcardName) < 2) {
    sendError('vCard name must be at least 2 characters');
}

try {
    $pdo = getDB();

    // Check user's vCard limit
    $stmt = $pdo->prepare("
        SELECT s.vcards_limit, COUNT(v.id) AS current_count
        FROM subscriptions s
        LEFT JOIN vcards v ON v.user_id = s.user_id
        WHERE s.user_id = ? AND s.status = 'active'
        GROUP BY s.id
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $sub = $stmt->fetch();

    if ($sub && $sub['current_count'] >= $sub['vcards_limit']) {
        sendError('You have reached your vCards limit (' . $sub['vcards_limit'] . '). Please upgrade your plan.', 403);
    }

    // Generate URL alias if not provided
    $urlAlias = trim($input['url_alias'] ?? '');
    if (empty($urlAlias)) {
        $urlAlias = generateUniqueAlias($vcardName);
    } else {
        $urlAlias = generateSlug($urlAlias);
        if (!isUrlAliasUnique($urlAlias)) {
            sendError('This URL is already taken. Please choose another.', 409);
        }
    }

    // Get other fields
    $occupation = sanitize($input['occupation'] ?? '');
    $firstName = sanitize($input['first_name'] ?? '');
    $lastName = sanitize($input['last_name'] ?? '');
    $email = trim($input['email'] ?? '');
    $phone = sanitize($input['phone'] ?? '');
    $company = sanitize($input['company'] ?? '');
    $description = $input['description'] ?? '';
    $templateId = sanitize($input['template_id'] ?? 'vcard1');

    // Insert vCard
    $sql = "INSERT INTO vcards (
                user_id, url_alias, vcard_name, occupation, description,
                first_name, last_name, email, phone, company,
                template_id, status
            ) VALUES (
                :user_id, :url_alias, :vcard_name, :occupation, :description,
                :first_name, :last_name, :email, :phone, :company,
                :template_id, 1
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'user_id' => $userId,
        'url_alias' => $urlAlias,
        'vcard_name' => $vcardName,
        'occupation' => $occupation,
        'description' => $description,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone,
        'company' => $company,
        'template_id' => $templateId
    ]);

    $vcardId = $pdo->lastInsertId();

    // Create default business hours
    $days = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];
    $bhStmt = $pdo->prepare("INSERT INTO vcard_business_hours (vcard_id, day_name, is_open, open_time, close_time) VALUES (?, ?, ?, ?, ?)");

    foreach ($days as $day) {
        $isOpen = $day !== 'SUNDAY' ? 1 : 0;
        $bhStmt->execute([$vcardId, $day, $isOpen, '10:00 AM', '06:00 PM']);
    }

    sendSuccess('vCard created successfully!', [
        'vcard_id' => $vcardId,
        'url_alias' => $urlAlias,
        'preview_url' => SITE_URL . '/' . $urlAlias
    ]);

} catch (Exception $e) {
    sendError('Failed to create vCard: ' . $e->getMessage(), 500);
}
