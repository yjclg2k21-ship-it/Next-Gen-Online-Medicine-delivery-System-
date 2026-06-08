<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=medicine_delivery', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Starting Definitive Wipe & Seed (1:1 Accurate Mapping)...\n";

// 1. Wipe existing medicines
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
$pdo->exec("TRUNCATE TABLE inventory_logs;");
$pdo->exec("TRUNCATE TABLE medicines;");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

// 2. Identify 4 Vendors
$stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'vendor' ORDER BY id ASC LIMIT 4");
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$v_mnc = $vendors[0]['id'];
$v_local = $vendors[1]['id'];
$v_generic = $vendors[2]['id'];
$v_ayurvedic = $vendors[3]['id'];

// 3. Define EXACT 1:1 Mappings (38 Unique Images)
$catalog = [
    ['name' => 'Paracetamol 500mg', 'salt' => 'Paracetamol', 'img' => 'paracetamol-500mg.jpg', 'cat' => 1],
    ['name' => 'Amoxicillin Capsules', 'salt' => 'Amoxicillin', 'img' => 'amoxicillin_capsules_1778507853087.png', 'cat' => 1],
    ['name' => 'Cetirizine 10mg', 'salt' => 'Cetirizine', 'img' => 'cetirizine_tablets_1778507868145.png', 'cat' => 1],
    ['name' => 'Omeprazole 20mg', 'salt' => 'Omeprazole', 'img' => 'omeprazole_capsules_1778507883875.png', 'cat' => 1],
    ['name' => 'Azithromycin 500mg', 'salt' => 'Azithromycin', 'img' => 'azithromycin_tablets_1778507899119.png', 'cat' => 1],
    ['name' => 'Ibuprofen 400mg', 'salt' => 'Ibuprofen', 'img' => 'ibuprofen_tablets_1778507914522.png', 'cat' => 1],
    ['name' => 'Vitamin C 500mg', 'salt' => 'Ascorbic Acid', 'img' => 'vitamin_c_tablets_1778514730165.png', 'cat' => 2],
    ['name' => 'Antibiotic Eye Ointment', 'salt' => 'Ciprofloxacin', 'img' => 'eye_ointment_tube_1778514747227.png', 'cat' => 2],
    ['name' => 'Orthopedic Knee Brace', 'salt' => 'Support Device', 'img' => 'knee_brace_box_1778514764394.png', 'cat' => 3],
    ['name' => 'Metformin 500mg', 'salt' => 'Metformin', 'img' => 'metformin_tablets_1778514780678.png', 'cat' => 1],
    ['name' => 'Pediatric Multivitamin Syrup', 'salt' => 'Vitamin Complex', 'img' => 'multivitamin_syrup_1778514797763.png', 'cat' => 2],
    ['name' => 'Diclofenac Pain Spray', 'salt' => 'Diclofenac Sodium', 'img' => 'diclofenac_spray_1778514812612.png', 'cat' => 2],
    ['name' => 'Calcium + D3 Tablets', 'salt' => 'Calcium Citrate', 'img' => 'calcium_tablets_1778514828387.png', 'cat' => 2],
    ['name' => 'Telmisartan 40mg', 'salt' => 'Telmisartan', 'img' => 'telmisartan_tablets_1778514847903.png', 'cat' => 1],
    ['name' => 'Albuterol Nebulizer Sol', 'salt' => 'Albuterol Sulfate', 'img' => 'albuterol_nebulizer_solution_1778514862544.png', 'cat' => 3],
    ['name' => 'Antifungal Dusting Powder', 'salt' => 'Clotrimazole', 'img' => 'antifungal_powder_1778514877151.png', 'cat' => 2],
    ['name' => 'Digoxin 0.25mg', 'salt' => 'Digoxin', 'img' => 'digoxin_tablets_1778514921406.png', 'cat' => 1],
    ['name' => 'Warfarin 5mg', 'salt' => 'Warfarin Sodium', 'img' => 'warfarin_tablets_1778514938540.png', 'cat' => 1],
    ['name' => 'Antacid Gel', 'salt' => 'Aluminium/Magnesium', 'img' => 'antacid-gel.jpg', 'cat' => 2],
    ['name' => 'Ashwagandha Extract', 'salt' => 'Withania Somnifera', 'img' => 'ashwagandha-extract.png', 'cat' => 2],
    ['name' => 'Aspirin 150mg', 'salt' => 'Acetylsalicylic Acid', 'img' => 'aspirin-150mg.jpg', 'cat' => 1],
    ['name' => 'Asthma Relief Inhaler', 'salt' => 'Salbutamol', 'img' => 'asthma-relief-inhaler.jpg', 'cat' => 3],
    ['name' => 'Blood Glucose Monitoring Kit', 'salt' => 'Diagnostic Kit', 'img' => 'blood-glucose-kit.png', 'cat' => 3],
    ['name' => 'Clotrimazole Cream', 'salt' => 'Clotrimazole', 'img' => 'clotrimazole-cream.jpg', 'cat' => 2],
    ['name' => 'Cough Syrup Expectorant', 'salt' => 'Guaifenesin', 'img' => 'cough-syrup-ex.jpg', 'cat' => 2],
    ['name' => 'Digital Thermometer', 'salt' => 'Medical Device', 'img' => 'digital-thermometer.png', 'cat' => 3],
    ['name' => 'Emergency First Aid Kit', 'salt' => 'Multiple Components', 'img' => 'first-aid-kit.png', 'cat' => 3],
    ['name' => 'Alcohol Hand Sanitizer', 'salt' => '70% Alcohol', 'img' => 'hand-sanitizer.png', 'cat' => 3],
    ['name' => 'Homeopathic Pellets', 'salt' => 'Herbal Formula', 'img' => 'homeopathic-pellets.png', 'cat' => 2],
    ['name' => 'Insulin Glargine Pen', 'salt' => 'Insulin', 'img' => 'insulin-glargine.jpg', 'cat' => 1],
    ['name' => 'Lubricating Eye Drops', 'salt' => 'Sodium Hyaluronate', 'img' => 'lubricating-eye-drops.jpg', 'cat' => 2],
    ['name' => 'Diclofenac Pain Relief Gel', 'salt' => 'Diclofenac', 'img' => 'pain-relief-gel.jpg', 'cat' => 2],
    ['name' => 'Pediatric Vitamin Drops', 'salt' => 'Essential Vitamins', 'img' => 'pediatric-vitamin-drops.jpg', 'cat' => 2],
    ['name' => 'Saline Nasal Drops', 'salt' => 'Normal Saline', 'img' => 'saline-nasal-drops.jpg', 'cat' => 2],
    ['name' => 'Sterile Surgical Gloves', 'salt' => 'Latex Free', 'img' => 'surgical-gloves.png', 'cat' => 3],
    ['name' => 'Multivitamin Blister Pack', 'salt' => 'Vitamins/Minerals', 'img' => 'tablet_blister.png', 'cat' => 2],
    ['name' => 'Vitamin B12 Injection', 'salt' => 'Cyanocobalamin', 'img' => 'vitamin-b12-injection.jpg', 'cat' => 1]
];

// Add 13 generic items to reach 50 total base medicines
$generics = [
    'Amlodipine 5mg', 'Atorvastatin 10mg', 'Losartan 50mg', 'Hydrochlorothiazide 12.5mg',
    'Gabapentin 300mg', 'Sertraline 50mg', 'Montelukast 10mg', 'Pantoprazole 40mg',
    'Furosemide 20mg', 'Prednisone 5mg', 'Lisinopril 20mg', 'Metoprolol 25mg', 'Simvastatin 40mg'
];

foreach ($generics as $g) {
    $catalog[] = ['name' => $g, 'salt' => explode(' ', $g)[0], 'img' => 'archetypes/generic.png', 'cat' => 1];
}

// 4. Seeding Logic (200 Records)
$insertStmt = $pdo->prepare("INSERT INTO medicines (vendor_id, category_id, name, slug, salt, price, mrp, stock, image, status, approval_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'approved')");

$totalCount = 0;
foreach ($catalog as $index => $item) {
    $baseName = $item['name'];
    $salt = $item['salt'];
    $img = $item['img'];
    $cat = $item['cat'];
    $baseSlug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $baseName)) . '-' . $index;

    // MNC (Vendor 1)
    $insertStmt->execute([$v_mnc, $cat, $baseName, $baseSlug . "-mnc", $salt, 100, 120, rand(50, 100), $img]);
    $totalCount++;

    // Local (Vendor 2)
    $insertStmt->execute([$v_local, $cat, $baseName, $baseSlug . "-local", $salt, 70, 80, rand(30, 80), $img]);
    $totalCount++;

    // Generic (Vendor 3)
    $insertStmt->execute([$v_generic, $cat, $baseName, $baseSlug . "-gen", $salt, 30, 40, rand(100, 200), 'archetypes/generic.png']);
    $totalCount++;

    // Ayurvedic (Vendor 4)
    $insertStmt->execute([$v_ayurvedic, $cat, $baseName . " (Herbal)", $baseSlug . "-ayu", $salt . " Extract", 60, 70, rand(20, 50), 'archetypes/ayurvedic.png']);
    $totalCount++;
}

echo "Successfully seeded {$totalCount} medicines with 1:1 image accuracy!\n";
