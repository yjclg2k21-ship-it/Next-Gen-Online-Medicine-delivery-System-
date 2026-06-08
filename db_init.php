<?php
/**
 * MediMitra Database Initializer
 * Automatically creates the database and imports the final SQL schema/seeds.
 */

// 1. Load Environment Variables (Minimal Loader)
$envPath = __DIR__ . '/backend/.env';
if (!file_exists($envPath)) {
    die("Error: .env file not found in backend directory.\n");
}

$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$config = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    list($name, $value) = explode('=', $line, 2);
    $config[trim($name)] = trim($value);
}

$host = $config['DB_HOST'] ?? '127.0.0.1';
$dbName = $config['DB_DATABASE'] ?? 'medicine_delivery';
$user = $config['DB_USERNAME'] ?? 'root';
$pass = $config['DB_PASSWORD'] ?? '';

echo "--------------------------------------------------\n";
echo "MediMitra Database Initialization\n";
echo "--------------------------------------------------\n";

try {
    // 2. Connect to MySQL (without DB selection)
    $pdo = new PDO("mysql:host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 3. Create Database
    echo "STEP 1: Creating database '$dbName'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName` text;");
    echo "SUCCESS: Database created/verified.\n\n";

    // 4. Import SQL
    $sqlPath = __DIR__ . '/database/mediflow_final.sql';
    if (!file_exists($sqlPath)) {
        die("ERROR: database/mediflow_final.sql not found! Please check file path.\n");
    }

    echo "STEP 2: Importing schema and seeds (This may take a moment)...\n";
    $sql = file_get_contents($sqlPath);
    
    // Execute the massive SQL dump
    $pdo->exec($sql);
    echo "SUCCESS: Tables and samples imported successfully.\n\n";

    echo "--------------------------------------------------\n";
    echo "INITIALIZATION COMPLETE!\n";
    echo "You can now run 'run_mediflow.bat' to start the app.\n";
    echo "--------------------------------------------------\n";

} catch (PDOException $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Please ensure XAMPP MySQL is running.\n";
    exit(1);
}
