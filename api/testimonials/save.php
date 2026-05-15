<?php
/**
 * TAPIFY - Testimonials Save
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$vcardId = (int)($input['vcard_id'] ?? 0);
$testimonialId = (int)($input['id'] ?? 0);
$name = sanitize($input['name'] ?? '');
$company = sanitize($input['company'] ?? '');
$designation = sanitize($input['designation'] ?? '');
$message = $input['message'] ?? '';
$rating = (int)($input['rating'] ?? 5);
if ($rating < 1 || $rating > 5) $rating = 5;

if (!$vcardId) sendError('vCard ID required');
if (empty($name)) sendError('Name is required');
if (empty($message)) sendError('Message is required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    if ($testimonialId > 0) {
        $stmt = $pdo->prepare("UPDATE vcard_testimonials SET name = ?, company = ?, designation = ?, message = ?, rating = ? WHERE id = ? AND vcard_id = ?");
        $stmt->execute([$name, $company, $designation, $message, $rating, $testimonialId, $vcardId]);
        sendSuccess('Testimonial updated', ['id' => $testimonialId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO vcard_testimonials (vcard_id, name, company, designation, message, rating) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$vcardId, $name, $company, $designation, $message, $rating]);
        sendSuccess('Testimonial added', ['id' => $pdo->lastInsertId()]);
    }
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
