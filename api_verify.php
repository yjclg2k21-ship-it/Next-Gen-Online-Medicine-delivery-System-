<?php
$ch = curl_init('http://localhost/Next%20Gen%20Online%20Medicine%20Delivery%20System/api/admin/dashboard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$resp = curl_exec($ch);
echo "API Response: " . $resp . "\n";
