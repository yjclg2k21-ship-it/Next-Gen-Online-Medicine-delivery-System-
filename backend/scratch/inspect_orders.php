<?php
require 'app/Core/Database.php';
try {
    $db = \App\Core\Database::getInstance()->getConnection();
    $db->exec("UPDATE users SET wallet_balance = 0.00 WHERE wallet_balance IS NULL");
    echo "WALLET_INITIALIZED";
} catch (Exception $e) {
    echo $e->getMessage();
}
