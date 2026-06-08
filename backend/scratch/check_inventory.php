<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=medicine_delivery', 'root', '');

$stmt = $pdo->query("SELECT u.name, COUNT(m.id) as med_count FROM medicines m JOIN users u ON m.vendor_id = u.id GROUP BY m.vendor_id");
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "INVENTORY COUNT:\n";
print_r($inventory);
