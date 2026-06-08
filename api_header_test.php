<?php
$ch = curl_init('http://localhost/Next%20Gen%20Online%20Medicine%20Delivery%20System/api/header_test.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer test_token']);
$resp = curl_exec($ch);
echo "Response: " . $resp . "\n";
