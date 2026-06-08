<?php
require_once 'app/Core/Database.php';

$mysql = 'C:\xampp\mysql\bin\mysql.exe';
$dbName = 'medicine_delivery';
$user = 'root';
$pass = '';

$passArg = $pass ? "--password=\"$pass\"" : "";
// test script is in backend/scratch
$fullPath = dirname(__DIR__) . '\storage\backups\backup_manual_2026-04-26_083313.sql';

$cmd = "\"$mysql\" --user=$user $passArg $dbName < \"$fullPath\"";
echo "Command: $cmd\n";

exec($cmd . " 2>&1", $output, $returnVar);

echo "Return Var: $returnVar\n";
echo "Output: \n" . implode("\n", $output) . "\n";
