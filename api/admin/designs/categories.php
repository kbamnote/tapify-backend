<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/database.php';
ini_set('display_errors', 0);
require_once __DIR__ . '/../../../includes/functions.php';

requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getDB();

    // ── GET: List all categories with design count ──────────────────────────
    if ($method === 'GET') {
        $stmt = $pdo->prepare(
            "SELECT dc.*, COUNT(d.id) AS design_count
             FROM design_categories dc
             LEFT JOIN designs d ON d.category_id = dc.id
             GROUP BY dc.id
             ORDER BY dc.sort_order ASC"
        );
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendSuccess('Categories fetched', ['categories' => $categories]);

    // ── POST: Create or update a category ───────────────────────────────────
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        $id         = isset($input['id'])         ? (int) $input['id']              : null;
        $name       = isset($input['name'])       ? trim($input['name'])             : '';
        $icon       = isset($input['icon'])       ? trim($input['icon'])             : '';
        $bg_color   = isset($input['bg_color'])   ? trim($input['bg_color'])         : '#ffffff';
        $text_color = isset($input['text_color']) ? trim($input['text_color'])       : '#000000';
        $sort_order = isset($input['sort_order']) ? (int) $input['sort_order']       : 0;
        $is_active  = isset($input['is_active'])  ? (int) (bool) $input['is_active'] : 1;

        if (!$name) {
            sendError('Category name is required', 400);
        }

        // Auto-generate slug if empty
        $slug = isset($input['slug']) ? trim($input['slug']) : '';
        if (!$slug) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
            $slug = trim($slug, '-');
        }

        if ($id) {
            // UPDATE existing
            $stmt = $pdo->prepare(
                "UPDATE design_categories
                 SET name = ?, slug = ?, icon = ?, bg_color = ?, text_color = ?,
                     sort_order = ?, is_active = ?
                 WHERE id = ?"
            );
            $stmt->execute([$name, $slug, $icon, $bg_color, $text_color, $sort_order, $is_active, $id]);
            sendSuccess('Category updated', ['id' => $id]);
        } else {
            // INSERT new
            $stmt = $pdo->prepare(
                "INSERT INTO design_categories (name, slug, icon, bg_color, text_color, sort_order, is_active)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$name, $slug, $icon, $bg_color, $text_color, $sort_order, $is_active]);
            $newId = (int) $pdo->lastInsertId();
            sendSuccess('Category created', ['id' => $newId]);
        }

    // ── DELETE: Remove a category ────────────────────────────────────────────
    } elseif ($method === 'DELETE') {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            sendError('Category id is required', 400);
        }

        $stmt = $pdo->prepare("DELETE FROM design_categories WHERE id = ?");
        $stmt->execute([$id]);
        sendSuccess('Category deleted', []);

    } else {
        sendError('Method not allowed', 405);
    }
} catch (Exception $e) {
    sendError('Operation failed: ' . $e->getMessage(), 500);
}
