<?php
require_once 'app/Core/Database.php';

$url = "http://localhost/Next%20Gen%20Online%20Medicine%20Delivery%20System/api/admin/backups/8/download";

// Let's get an admin token from the database directly since it might be required
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("SELECT id FROM users WHERE role='admin' LIMIT 1");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Actually, I can just bypass auth for a moment to test, or create a JWT token
// Let's just do a cURL without token first and see the body
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
unset($ch); // curl_close is deprecated, garbage collection handles this in modern PHP

echo "HTTP Code: $httpcode\n";
echo "Response: $response\n";
