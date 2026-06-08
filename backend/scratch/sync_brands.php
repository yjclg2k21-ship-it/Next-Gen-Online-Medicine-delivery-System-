<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();

try {
    // Add columns if they don't exist
    $db->exec("ALTER TABLE brands ADD COLUMN hq VARCHAR(255) DEFAULT 'Phaltan, India'");
    $db->exec("ALTER TABLE brands ADD COLUMN type VARCHAR(100) DEFAULT 'MNC'");
} catch (Exception $e) {
    // Columns might already exist
}

$brands = [
    ['name' => 'Cipla', 'hq' => 'Phaltan, India', 'type' => 'MNC'],
    ['name' => 'Sun Pharma', 'hq' => 'Phaltan, India', 'type' => 'MNC'],
    ['name' => 'Pfizer', 'hq' => 'New York, USA', 'type' => 'MNC'],
    ['name' => 'Dr. Reddys', 'hq' => 'Hyderabad, India', 'type' => 'MNC'],
    ['name' => 'Abbott', 'hq' => 'Chicago, USA', 'type' => 'MNC'],
    ['name' => 'Glenmark', 'hq' => 'Phaltan, India', 'type' => 'Generic'],
    ['name' => 'Himalaya', 'hq' => 'Bengaluru, India', 'type' => 'Ayurvedic'],
    ['name' => 'Zydus Cadila', 'hq' => 'Ahmedabad, India', 'type' => 'MNC'],
    ['name' => 'Mankind', 'hq' => 'New Delhi, India', 'type' => 'Domestic']
];

foreach ($brands as $b) {
    $stmt = $db->prepare("SELECT id FROM brands WHERE name = ?");
    $stmt->execute([$b['name']]);
    if ($stmt->fetch()) {
        // Update
        $stmt = $db->prepare("UPDATE brands SET hq = ?, type = ? WHERE name = ?");
        $stmt->execute([$b['hq'], $b['type'], $b['name']]);
    } else {
        // Insert
        $slug = strtolower(str_replace(' ', '-', $b['name']));
        $stmt = $db->prepare("INSERT INTO brands (name, slug, hq, type, status, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$b['name'], $slug, $b['hq'], $b['type']]);
    }
}

echo "Brands synchronized successfully.\n";
