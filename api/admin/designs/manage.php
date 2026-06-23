<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/database.php';
ini_set('display_errors', 0); // Override database.php config for JSON APIs
require_once __DIR__ . '/../../../includes/functions.php';

requireDesignerOrAdmin();

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getDB();

    // ── GET: List designs, optionally filtered by category ──────────────────
    if ($method === 'GET') {
        $category_id = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;

        $sql = "SELECT d.*, dc.name AS category_name,
                       cc.name AS content_category_name
                FROM designs d
                LEFT JOIN design_categories dc ON dc.id = d.category_id
                LEFT JOIN categories cc ON cc.id = d.content_category_id";
        $params = [];

        if ($category_id) {
            $sql .= " WHERE d.category_id = ?";
            $params[] = $category_id;
        }

        $sql .= " ORDER BY d.sort_order ASC, d.id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $designs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendSuccess('Designs fetched', ['designs' => $designs]);

    // ── POST: Create or update a design ─────────────────────────────────────
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        $id                  = isset($input['id'])                  ? (int) $input['id']                         : null;
        $category_id         = isset($input['category_id'])         ? (int) $input['category_id']               : 0;
        $content_category_id = isset($input['content_category_id']) && $input['content_category_id'] !== '' && $input['content_category_id'] !== null
                                   ? (int) $input['content_category_id'] : null;
        $title               = isset($input['title'])               ? trim($input['title'])                      : '';
        $description         = isset($input['description'])         ? trim($input['description'])                : '';
        $image_url           = isset($input['image_url'])           ? trim($input['image_url'])                  : '';
        $tags = '';
        if (isset($input['tags'])) {
            $tags = is_array($input['tags']) ? implode(',', $input['tags']) : trim($input['tags']);
        }
        $is_active   = isset($input['is_active'])   ? (int)(bool)$input['is_active']   : 1;
        $sort_order  = isset($input['sort_order'])  ? (int) $input['sort_order']        : 0;
        $created_by  = getCurrentUserId();

        if (!$category_id) {
            $stmtCat = $pdo->query("SELECT id FROM design_categories ORDER BY sort_order ASC, id ASC LIMIT 1");
            $firstCat = $stmtCat->fetchColumn();
            if ($firstCat) {
                $category_id = (int)$firstCat;
            } else {
                sendError('Please create at least one Category in the Categories tab first!', 400);
            }
        }
        if (!$title) {
            sendError('title is required', 400);
        }

        if ($id) {
            // UPDATE existing
            $stmt = $pdo->prepare(
                "UPDATE designs
                 SET category_id = ?, title = ?, description = ?, image_url = ?,
                     tags = ?, is_active = ?, sort_order = ?, content_category_id = ?
                 WHERE id = ?"
            );
            $stmt->execute([$category_id, $title, $description, $image_url, $tags, $is_active, $sort_order, $content_category_id, $id]);
            sendSuccess('Design updated', ['id' => $id]);
        } else {
            // INSERT new
            $stmt = $pdo->prepare(
                "INSERT INTO designs (category_id, title, description, image_url, tags, is_active, sort_order, content_category_id, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$category_id, $title, $description, $image_url, $tags, $is_active, $sort_order, $content_category_id, $created_by]);
            $newId = (int) $pdo->lastInsertId();
            sendSuccess('Design created', ['id' => $newId]);
        }

    // ── DELETE: Remove a design ──────────────────────────────────────────────
    } elseif ($method === 'DELETE') {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            sendError('Design id is required', 400);
        }

        $stmt = $pdo->prepare("DELETE FROM designs WHERE id = ?");
        $stmt->execute([$id]);
        sendSuccess('Design deleted', []);

    } else {
        sendError('Method not allowed', 405);
    }
} catch (Exception $e) {
    sendError('Operation failed: ' . $e->getMessage(), 500);
}
