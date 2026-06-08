<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();
$q = $db->query("DESCRIBE medicines");
print_r($q->fetchAll(PDO::FETCH_ASSOC));
