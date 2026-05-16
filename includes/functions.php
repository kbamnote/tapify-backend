<?php
/**
 * TAPIFY - Helper Functions
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Send JSON response
 */
function sendJson($success, $message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

/**
 * Send error response
 */
function sendError($message, $statusCode = 400) {
    sendJson(false, $message, null, $statusCode);
}

/**
 * Send success response
 */
function sendSuccess($message, $data = null) {
    sendJson(true, $message, $data, 200);
}

/**
 * Get JSON input from request
 */
function getInput() {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    return $input ?: [];
}

/**
 * Sanitize string input
 */
function sanitize($str) {
    return htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get logged in user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Require authentication (call this at top of protected APIs)
 */
function requireAuth() {
    if (!isLoggedIn()) {
        sendError('Authentication required. Please login.', 401);
    }
}

/**
 * Check if current user is an admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Require admin role
 */
function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        sendError('Access denied. Admin privileges required.', 403);
    }
}

/**
 * Get current logged in user details
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, name, email, phone, avatar, role, status FROM users WHERE id = ?");
    $stmt->execute([getCurrentUserId()]);
    return $stmt->fetch();
}

/**
 * Generate URL-safe slug
 */
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'card-' . time();
}

/**
 * Check if URL alias is unique
 */
function isUrlAliasUnique($alias, $excludeId = null) {
    $pdo = getDB();
    $sql = "SELECT id FROM vcards WHERE url_alias = ?";
    $params = [$alias];
    if ($excludeId) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch() === false;
}

/**
 * Generate unique URL alias
 */
function generateUniqueAlias($base) {
    $alias = generateSlug($base);
    $counter = 1;
    while (!isUrlAliasUnique($alias)) {
        $alias = generateSlug($base) . '-' . $counter;
        $counter++;
    }
    return $alias;
}

/**
 * Log activity (for debugging)
 */
function logActivity($action, $details = '') {
    $logFile = __DIR__ . '/../logs/activity.log';
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    $timestamp = date('Y-m-d H:i:s');
    $userId = getCurrentUserId() ?? 'guest';
    $line = "[$timestamp] [User: $userId] $action - $details" . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND);
}

/**
 * Upload file securely
 */
function uploadFile($file, $allowedTypes = ['jpg','jpeg','png','gif','webp']) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(',', $allowedTypes)];
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
        return ['success' => false, 'message' => 'File too large. Max 5MB'];
    }

    $uploadDir = UPLOAD_PATH . date('Y/m/');
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $newName = uniqid() . '_' . time() . '.' . $ext;
    $newPath = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $newPath)) {
        $relativePath = 'uploads/' . date('Y/m/') . $newName;
        return [
            'success' => true,
            'path' => $relativePath,
            'url' => SITE_URL . '/backend/' . $relativePath
        ];
    }

    return ['success' => false, 'message' => 'Upload failed'];
}
 
/**
 * Upload image to Cloudinary
 */
function uploadToCloudinary($file) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }
 
    $cloudName = CLOUDINARY_CLOUD_NAME;
    $apiKey = CLOUDINARY_API_KEY;
    $apiSecret = CLOUDINARY_API_SECRET;
    $timestamp = time();
 
    // Create signature
    // For unsigned upload, we don't need signature, but for signed upload (more secure) we do.
    // Let's use signed upload.
    $params = [
        'timestamp' => $timestamp
    ];
    ksort($params);
    $signStr = "";
    foreach($params as $k => $v) {
        $signStr .= "$k=$v&";
    }
    $signStr = rtrim($signStr, '&') . $apiSecret;
    $signature = sha1($signStr);
 
    $url = "https://api.cloudinary.com/v1_1/$cloudName/image/upload";
 
    $postData = [
        'file' => new CURLFile($file['tmp_name'], $file['type'], $file['name']),
        'api_key' => $apiKey,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];
 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
 
    if ($err) {
        return ['success' => false, 'message' => 'Cloudinary Error: ' . $err];
    }
 
    $result = json_decode($response, true);
    if (isset($result['secure_url'])) {
        return [
            'success' => true,
            'url' => $result['secure_url'],
            'public_id' => $result['public_id']
        ];
    } else {
        return ['success' => false, 'message' => $result['error']['message'] ?? 'Cloudinary upload failed'];
    }
}

/**
 * Resolve an image path to a full public URL.
 * - If $path is already an absolute URL (Cloudinary / http), return as-is.
 * - Otherwise prepend SITE_URL to treat as a legacy local path.
 * Returns null if $path is empty.
 */
function imgUrl($path) {
    if (!$path) return null;
    if (strpos($path, 'http') === 0) return $path; // already absolute (Cloudinary)
    return SITE_URL . '/' . ltrim($path, '/');
}
