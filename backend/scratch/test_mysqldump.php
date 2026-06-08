<?php
$mysqldump = 'C:\xampp\mysql\bin\mysqldump.exe';
$dbName = 'medicine_delivery';
$user = 'root';
$pass = '';
$fullPath = __DIR__ . '/test_backup.sql';

$cmd = "\"$mysqldump\" --user=$user --password=$pass $dbName > \"$fullPath\"";
echo "Running: $cmd\n";
exec($cmd, $output, $returnVar);

echo "Return Var: $returnVar\n";
print_r($output);
if (file_exists($fullPath)) {
    echo "Success! File created: " . filesize($fullPath) . " bytes\n";
    unlink($fullPath);
} else {
    echo "Failure! File not created.\n";
}
