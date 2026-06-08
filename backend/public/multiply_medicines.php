<?php
require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    // 1. Ensure 4 Brand Types exist in the `brands` table
    $brandTypes = [
        'MNC' => 'Global Pharma (MNC)',
        'Local' => 'City Medicals (Local)',
        'Generic' => 'Jan Aushadhi (Generic)',
        'Ayurvedic' => 'Patanjali (Ayurvedic)'
    ];

    $brandIds = [];
    foreach ($brandTypes as $type => $name) {
        $slug = strtolower($type) . '-brand';
        $stmt = $db->prepare("SELECT id FROM brands WHERE type = ? LIMIT 1");
        $stmt->execute([$type]);
        $row = $stmt->fetch();
        
        if ($row) {
            $brandIds[$type] = $row['id'];
        } else {
            $stmt = $db->prepare("INSERT INTO brands (name, type, slug, status) VALUES (?, ?, ?, 1)");
            $stmt->execute([$name, $type, $slug]);
            $brandIds[$type] = $db->lastInsertId();
        }
    }

    echo "Brands ensured.\n";

    // 2. Fetch all medicines that haven't been multiplied yet.
    // We assume if slug contains '-variant-' it is already processed.
    // So we fetch those without '-variant-'
    $stmt = $db->prepare("SELECT * FROM medicines WHERE slug NOT LIKE '%-variant-%'");
    $stmt->execute();
    $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count = 0;
    foreach ($medicines as $med) {
        $baseSlug = $med['slug'];
        $basePrice = (float)$med['price'];
        $baseMrp = (float)$med['mrp'];
        if ($baseMrp <= 0) $baseMrp = $basePrice * 1.2;

        // Make the original medicine the 'MNC' variant
        $stmt = $db->prepare("UPDATE medicines SET brand_id = ?, slug = ?, price = ?, mrp = ? WHERE id = ?");
        $stmt->execute([
            $brandIds['MNC'], 
            $baseSlug . '-variant-mnc', 
            round($basePrice * 1.5, 2), 
            round($baseMrp * 1.5, 2), 
            $med['id']
        ]);

        // Insert Local Variant (1.0x price)
        $stmt = $db->prepare("INSERT INTO medicines (vendor_id, category_id, brand_id, name, slug, salt, price, mrp, stock, expiry_date, image, requires_prescription, approval_status, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $med['vendor_id'], $med['category_id'], $brandIds['Local'], $med['name'], 
            $baseSlug . '-variant-local', $med['salt'], round($basePrice * 1.0, 2), round($baseMrp * 1.0, 2),
            $med['stock'], $med['expiry_date'], $med['image'], $med['requires_prescription'], 
            $med['approval_status'], $med['status']
        ]);

        // Insert Generic Variant (0.6x price)
        $stmt->execute([
            $med['vendor_id'], $med['category_id'], $brandIds['Generic'], $med['name'], 
            $baseSlug . '-variant-generic', $med['salt'], round($basePrice * 0.6, 2), round($baseMrp * 0.6, 2),
            $med['stock'], $med['expiry_date'], $med['image'], $med['requires_prescription'], 
            $med['approval_status'], $med['status']
        ]);

        // Insert Ayurvedic Variant (0.8x price)
        $stmt->execute([
            $med['vendor_id'], $med['category_id'], $brandIds['Ayurvedic'], $med['name'], 
            $baseSlug . '-variant-ayurvedic', $med['salt'], round($basePrice * 0.8, 2), round($baseMrp * 0.8, 2),
            $med['stock'], $med['expiry_date'], $med['image'], $med['requires_prescription'], 
            $med['approval_status'], $med['status']
        ]);

        $count++;
    }

    $db->commit();
    echo "Multiplied $count base medicines into 4 variants each successfully.\n";

} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
