<?php
$modelsDir = __DIR__ . '/backend/app/Models/';
$files = glob($modelsDir . '*.php');

$softDeleteTables = [
    'users', 'addresses', 'medicines', 'prescriptions', 'return_requests', 'orders'
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'extends Model') !== false || strpos($content, 'extends \App\Core\Model') !== false) {
        
        // Find the table name
        preg_match("/protected \\\$table = '([^']+)';/", $content, $matches);
        $table = $matches[1] ?? '';
        
        if ($table && !in_array($table, $softDeleteTables)) {
            // It's not a soft delete table. Check if it already has useSoftDelete
            if (strpos($content, '$useSoftDelete') === false) {
                // Insert it right after the $table declaration
                $content = preg_replace(
                    "/(protected \\\$table = '[^']+';)/",
                    "$1\n    protected \$useSoftDelete = false;",
                    $content
                );
                file_put_contents($file, $content);
                echo "Fixed " . basename($file) . "\n";
            }
        }
    }
}
echo "Done.\n";
