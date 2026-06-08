<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=medicine_delivery', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Starting Database Wipe & Seed...\n";

// 1. Wipe existing medicines
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
$pdo->exec("TRUNCATE TABLE inventory_logs;");
$pdo->exec("TRUNCATE TABLE medicines;");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
echo "Catalog wiped.\n";

// 2. Identify 4 Vendors
$stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'vendor' ORDER BY id ASC LIMIT 4");
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($vendors) < 4) {
    die("Need at least 4 vendors in the database. Found " . count($vendors));
}

$v_mnc = $vendors[0]['id'];
$v_local = $vendors[1]['id'];
$v_generic = $vendors[2]['id'];
$v_ayurvedic = $vendors[3]['id'];

echo "Mapped Vendors:\n";
echo "MNC: Vendor {$v_mnc}\n";
echo "Local: Vendor {$v_local}\n";
echo "Generic: Vendor {$v_generic}\n";
echo "Ayurvedic: Vendor {$v_ayurvedic}\n";

// 3. Define Base Concepts (50 Medicines)
$base_medicines = [
    ['name' => 'Paracetamol 500mg', 'salt' => 'Paracetamol', 'base_price' => 20, 'image' => 'paracetamol-500mg.jpg', 'cat' => 1],
    ['name' => 'Azithromycin 500mg', 'salt' => 'Azithromycin', 'base_price' => 120, 'image' => 'azithromycin_tablets_1778507899119.png', 'cat' => 1],
    ['name' => 'Amoxicillin 500mg', 'salt' => 'Amoxicillin Trihydrate', 'base_price' => 80, 'image' => 'amoxicillin_capsules_1778507853087.png', 'cat' => 1],
    ['name' => 'Cetirizine 10mg', 'salt' => 'Cetirizine Hydrochloride', 'base_price' => 30, 'image' => 'cetirizine_tablets_1778507868145.png', 'cat' => 1],
    ['name' => 'Omeprazole 20mg', 'salt' => 'Omeprazole', 'base_price' => 45, 'image' => 'omeprazole_capsules_1778507883875.png', 'cat' => 1],
    ['name' => 'Ibuprofen 400mg', 'salt' => 'Ibuprofen', 'base_price' => 35, 'image' => 'ibuprofen_tablets_1778507914522.png', 'cat' => 1],
    ['name' => 'Antacid Gel', 'salt' => 'Aluminium Hydroxide, Magnesium', 'base_price' => 90, 'image' => 'antacid-gel.jpg', 'cat' => 2],
    ['name' => 'Ashwagandha Extract', 'salt' => 'Withania Somnifera', 'base_price' => 150, 'image' => 'ashwagandha-extract.png', 'cat' => 2],
    ['name' => 'Aspirin 150mg', 'salt' => 'Acetylsalicylic Acid', 'base_price' => 15, 'image' => 'aspirin-150mg.jpg', 'cat' => 1],
    ['name' => 'Asthma Relief Inhaler', 'salt' => 'Salbutamol', 'base_price' => 250, 'image' => 'asthma-relief-inhaler.jpg', 'cat' => 3],
    ['name' => 'Blood Glucose Kit', 'salt' => 'Diagnostic', 'base_price' => 800, 'image' => 'blood-glucose-kit.png', 'cat' => 3],
    ['name' => 'Clotrimazole Cream', 'salt' => 'Clotrimazole', 'base_price' => 70, 'image' => 'clotrimazole-cream.jpg', 'cat' => 2],
    ['name' => 'Cough Syrup Expectorant', 'salt' => 'Guaifenesin, Bromhexine', 'base_price' => 110, 'image' => 'cough-syrup-ex.jpg', 'cat' => 2],
    ['name' => 'Digital Thermometer', 'salt' => 'Device', 'base_price' => 300, 'image' => 'digital-thermometer.png', 'cat' => 3],
    ['name' => 'First Aid Kit', 'salt' => 'Bandages, Antiseptic', 'base_price' => 500, 'image' => 'first-aid-kit.png', 'cat' => 3],
    ['name' => 'Hand Sanitizer', 'salt' => 'Ethyl Alcohol', 'base_price' => 100, 'image' => 'hand-sanitizer.png', 'cat' => 3],
    ['name' => 'Homeopathic Pellets', 'salt' => 'Arnica Montana', 'base_price' => 80, 'image' => 'homeopathic-pellets.png', 'cat' => 2],
    ['name' => 'Insulin Glargine', 'salt' => 'Insulin', 'base_price' => 450, 'image' => 'insulin-glargine.jpg', 'cat' => 1],
    ['name' => 'Lubricating Eye Drops', 'salt' => 'Sodium Hyaluronate', 'base_price' => 180, 'image' => 'lubricating-eye-drops.jpg', 'cat' => 2],
    ['name' => 'Pain Relief Gel', 'salt' => 'Diclofenac', 'base_price' => 120, 'image' => 'pain-relief-gel.jpg', 'cat' => 2],
    ['name' => 'Pediatric Vitamin Drops', 'salt' => 'Multivitamin', 'base_price' => 140, 'image' => 'pediatric-vitamin-drops.jpg', 'cat' => 2],
    ['name' => 'Saline Nasal Drops', 'salt' => 'Sodium Chloride', 'base_price' => 50, 'image' => 'saline-nasal-drops.jpg', 'cat' => 2],
    ['name' => 'Surgical Gloves', 'salt' => 'Latex', 'base_price' => 40, 'image' => 'surgical-gloves.png', 'cat' => 3],
    ['name' => 'Tablet Blister Pack', 'salt' => 'Generic Supplement', 'base_price' => 60, 'image' => 'tablet_blister.png', 'cat' => 2],
    ['name' => 'Vitamin B12 Injection', 'salt' => 'Cyanocobalamin', 'base_price' => 55, 'image' => 'vitamin-b12-injection.jpg', 'cat' => 1]
];

// Duplicate base medicines with slight variations to reach 50
$more_meds = [];
foreach ($base_medicines as $med) {
    $new_med = $med;
    $new_med['name'] = $med['name'] . ' Plus';
    $new_med['base_price'] = $med['base_price'] * 1.2;
    $more_meds[] = $new_med;
}
$base_medicines = array_merge($base_medicines, $more_meds); // Now 50 base medicines

// 4. Seeding
$insertStmt = $pdo->prepare("INSERT INTO medicines (vendor_id, category_id, name, slug, salt, price, mrp, stock, image, status, approval_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'approved')");

$totalCount = 0;
foreach ($base_medicines as $index => $med) {
    $baseName = $med['name'];
    $salt = $med['salt'];
    $basePrice = $med['base_price'];
    $baseImg = $med['image'];
    $cat = $med['cat'];
    $baseSlug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $baseName));

    // Variant 1: MNC
    $insertStmt->execute([
        $v_mnc, $cat, $baseName . " (MNC Brand)", $baseSlug . "-mnc-" . $index, $salt,
        $basePrice * 1.5, $basePrice * 1.6, rand(50, 200), $baseImg
    ]);
    $totalCount++;

    // Variant 2: Local
    $insertStmt->execute([
        $v_local, $cat, $baseName . " (Local Brand)", $baseSlug . "-local-" . $index, $salt,
        $basePrice * 1.0, $basePrice * 1.1, rand(20, 100), $baseImg
    ]);
    $totalCount++;

    // Variant 3: Generic
    $insertStmt->execute([
        $v_generic, $cat, $baseName . " (Generic)", $baseSlug . "-generic-" . $index, $salt,
        $basePrice * 0.4, $basePrice * 0.5, rand(100, 500), 'archetypes/generic.png'
    ]);
    $totalCount++;

    // Variant 4: Ayurvedic
    $insertStmt->execute([
        $v_ayurvedic, $cat, $baseName . " (Herbal Extract)", $baseSlug . "-ayurvedic-" . $index, $salt . " (Herbal Equivalent)",
        $basePrice * 0.8, $basePrice * 0.9, rand(30, 150), 'archetypes/ayurvedic.png'
    ]);
    $totalCount++;
}

echo "Seeding completed! Inserted {$totalCount} medicines.\n";
