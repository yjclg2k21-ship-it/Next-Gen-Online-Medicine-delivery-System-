<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();

try { $db->exec("ALTER TABLE categories ADD COLUMN parent_id INT NULL DEFAULT NULL AFTER id"); } catch(Exception $e) {}
try { $db->exec("ALTER TABLE categories ADD COLUMN description TEXT NULL AFTER name"); } catch(Exception $e) {}

echo "Category table updated.\n";
