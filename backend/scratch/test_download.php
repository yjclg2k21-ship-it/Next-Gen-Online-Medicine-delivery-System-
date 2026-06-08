<?php
require_once 'app/Core/Database.php';

// Simulate frontend call to download
$url = "http://localhost/Next%20Gen%20Online%20Medicine%20Delivery%20System/api/admin/backups/8/download";
// Get token for auth
$db = \App\Core\Database::getInstance()->getConnection();
$stmt = $db->query("SELECT token FROM users WHERE role='admin' LIMIT 1");
// Actually, tokens are usually JWT, let's just make a curl call and see the HTTP response code.
// Wait, without token it should return 401.

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpcode\n";
echo "Response: $response\n";
