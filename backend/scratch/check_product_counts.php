<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();
$q = $db->query("SELECT brand_id, COUNT(*) as count FROM medicines GROUP BY brand_id");
print_r($q->fetchAll(PDO::FETCH_ASSOC));
