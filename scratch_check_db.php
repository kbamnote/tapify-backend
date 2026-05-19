<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDB();
    echo "DB Connected successfully\n";

    echo "--- USERS ---\n";
    $stmt = $pdo->query("SELECT id, email, name FROM users");
    $users = $stmt->fetchAll();
    print_r($users);

    echo "\n--- WHATSAPP STORES ---\n";
    $stmt = $pdo->query("SELECT id, user_id, url_alias, store_name, status FROM whatsapp_stores");
    $stores = $stmt->fetchAll();
    print_r($stores);

    echo "\n--- APPOINTMENTS ---\n";
    $stmt = $pdo->query("SELECT a.id, a.vcard_id, v.user_id, a.customer_name, a.appointment_date, a.status FROM vcard_appointments a JOIN vcards v ON v.id = a.vcard_id");
    $appts = $stmt->fetchAll();
    print_r($appts);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
