<?php
$ch = curl_init('http://localhost/Next%20Gen%20Online%20Medicine%20Delivery%20System/api/admin/roles/assign');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['matrix'=>[['module'=>'Orders']]]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$res = curl_exec($ch);
print_r($res);
