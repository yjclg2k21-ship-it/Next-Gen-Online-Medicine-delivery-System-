<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

$vendorId = 2; // City Medicals

$imageDir = 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/frontend/assets/images/medicines/';
$files = scandir($imageDir);

// First delete all existing medicines for a fresh start
$db->exec("DELETE FROM medicines");

$stmt = $db->prepare("INSERT INTO medicines (vendor_id, name, slug, salt, price, mrp, stock, expiry_date, image, requires_prescription, approval_status, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved', 1)");

$count = 0;
foreach ($files as $file) {
    if ($file === '.' || $file === '..' || is_dir($imageDir . $file) || pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
        continue;
    }

    // Generate name from filename
    $base = pathinfo($file, PATHINFO_FILENAME);
    // Remove trailing timestamp like _1778507853087
    $base = preg_replace('/_\d+$/', '', $base);
    // Replace underscores and hyphens with spaces
    $name = ucwords(str_replace(['_', '-'], ' ', $base));
    
    // Add "Tablet" or "Syrup" if it doesn't have it and it's a typical medicine
    if (!preg_match('/(Tablet|Capsule|Syrup|Cream|Gel|Drops|Inhaler|Powder|Kit|Solution|Injection|Ointment)/i', $name)) {
        // Just leave the name as is if we can't guess, or add something generic
        // Actually the names from image files look pretty descriptive like "paracetamol-500mg" -> "Paracetamol 500mg"
    }

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . bin2hex(random_bytes(2));
    
    $salt = $name . ' (Generic)';
    $price = rand(20, 300);
    $mrp = $price + rand(5, 50);
    $stock = rand(10, 500);
    
    // Random expiry date between 1 and 3 years from now
    $days = rand(365, 1095);
    $expiryDate = date('Y-m-d', strtotime("+$days days"));
    
    // Random prescription requirement
    $reqPresc = (rand(1, 100) <= 40) ? 1 : 0; // 40% chance

    $stmt->execute([
        $vendorId,
        $name,
        $slug,
        $salt,
        $price,
        $mrp,
        $stock,
        $expiryDate,
        $file,
        $reqPresc
    ]);
    
    $count++;
}

echo "$count medicines seeded successfully from images!\n";
