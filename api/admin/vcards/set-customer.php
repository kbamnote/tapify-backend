<?php
/**
 * Admin/Staff: set / reset the customer login (email + password) for an existing vCard.
 *
 * GET  ?vcard_id=ID            -> returns the current owner { email, name, has_login }
 * POST { vcard_id, customer_email, customer_password, customer_name? }
 *      -> creates or links a customer user account and assigns the vCard to it.
 *
 * Used to fix vCards that were created without customer credentials.
 */
header('Content-Type: application/json');
ini_set('display_errors', 0);

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';

if (!isLoggedIn() || !isStaffOrAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Staff or admin access required']);
    exit;
}

try {
    $pdo = getDB();

    // ---- GET: return current owner info for the vCard ----
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $vcardId = isset($_GET['vcard_id']) ? (int)$_GET['vcard_id'] : 0;
        if (!$vcardId) {
            echo json_encode(['success' => false, 'message' => 'Missing vcard_id']);
            exit;
        }
        $stmt = $pdo->prepare("
            SELECT u.id AS user_id, u.email, u.name, u.role
            FROM vcards v
            LEFT JOIN users u ON u.id = v.user_id
            WHERE v.id = ?
            LIMIT 1
        ");
        $stmt->execute([$vcardId]);
        $owner = $stmt->fetch();

        if (!$owner) {
            echo json_encode(['success' => false, 'message' => 'vCard not found']);
            exit;
        }

        // A "real" customer login is a non-admin owner with an email.
        $hasLogin = !empty($owner['email']) && ($owner['role'] ?? '') !== 'admin';
        echo json_encode([
            'success' => true,
            'data' => [
                'email'     => $hasLogin ? $owner['email'] : '',
                'name'      => $hasLogin ? $owner['name'] : '',
                'has_login' => $hasLogin
            ]
        ]);
        exit;
    }

    // ---- POST: set / reset the customer login ----
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $vcardId  = isset($data['vcard_id']) ? (int)$data['vcard_id'] : 0;
    $email    = trim($data['customer_email'] ?? '');
    $password = $data['customer_password'] ?? '';
    $name     = sanitize($data['customer_name'] ?? '');

    if (!$vcardId) {
        echo json_encode(['success' => false, 'message' => 'Missing vcard_id']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'A valid customer email is required']);
        exit;
    }
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }

    // Confirm the vCard exists and fetch its name (for default user name).
    $stmt = $pdo->prepare("SELECT id, vcard_name FROM vcards WHERE id = ? LIMIT 1");
    $stmt->execute([$vcardId]);
    $vcard = $stmt->fetch();
    if (!$vcard) {
        echo json_encode(['success' => false, 'message' => 'vCard not found']);
        exit;
    }

    $pdo->beginTransaction();

    // Find existing user by email, else create one.
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();

    if ($existing) {
        $userId = $existing['id'];
        // Reset their password to the new one and (optionally) update name.
        $hashed = hashPassword($password);
        if ($name) {
            $stmt = $pdo->prepare("UPDATE users SET password = ?, name = ? WHERE id = ?");
            $stmt->execute([$hashed, $name, $userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $userId]);
        }
    } else {
        $hashed = hashPassword($password);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'user', 1)");
        $stmt->execute([$name ?: $vcard['vcard_name'], $email, $hashed]);
        $userId = $pdo->lastInsertId();

        // Give the new user a default subscription.
        // Dates are computed in PHP and bound as params (avoids SQL date
        // functions in a prepared statement, which some MySQL/MariaDB reject).
        $subStart  = date('Y-m-d');
        $subExpiry = date('Y-m-d', strtotime('+1 year'));
        $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, plan_name, vcards_limit, stores_limit, price, subscribed_date, expiry_date, status) VALUES (?, 'Free Plan', 5, 1, 0, ?, ?, 'active')");
        $stmt->execute([$userId, $subStart, $subExpiry]);
    }

    // Assign the vCard to this customer.
    $stmt = $pdo->prepare("UPDATE vcards SET user_id = ? WHERE id = ?");
    $stmt->execute([$userId, $vcardId]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => $existing ? 'Customer login updated and vCard assigned' : 'Customer account created and vCard assigned',
        'data'    => ['email' => $email, 'has_login' => true]
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
