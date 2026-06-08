<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=medicine_delivery', 'root', '');
$count = $pdo->query('SELECT COUNT(*) FROM medicines')->fetchColumn();
echo "TOTAL_MEDICINES:" . $count;
