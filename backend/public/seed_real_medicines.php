<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

$vendorId = 2; // City Medicals

$realMedicines = [
    [
        'name' => 'Dolo 650 Tablet',
        'salt' => 'Paracetamol (650mg)',
        'price' => 30.00,
        'mrp' => 33.00,
        'stock' => 150,
        'requires_prescription' => 0
    ],
    [
        'name' => 'Augmentin 625 Duo Tablet',
        'salt' => 'Amoxycillin (500mg) + Clavulanic Acid (125mg)',
        'price' => 201.00,
        'mrp' => 223.00,
        'stock' => 50,
        'requires_prescription' => 1
    ],
    [
        'name' => 'Pan 40 Tablet',
        'salt' => 'Pantoprazole (40mg)',
        'price' => 155.00,
        'mrp' => 165.00,
        'stock' => 200,
        'requires_prescription' => 1
    ],
    [
        'name' => 'Shelcal 500 Tablet',
        'salt' => 'Calcium (500mg) + Vitamin D3 (250 IU)',
        'price' => 119.00,
        'mrp' => 131.00,
        'stock' => 120,
        'requires_prescription' => 0
    ],
    [
        'name' => 'Telma 40 Tablet',
        'salt' => 'Telmisartan (40mg)',
        'price' => 212.00,
        'mrp' => 235.00,
        'stock' => 80,
        'requires_prescription' => 1
    ],
    [
        'name' => 'Glycomet-GP 2 Tablet',
        'salt' => 'Glimepiride (2mg) + Metformin (500mg)',
        'price' => 180.00,
        'mrp' => 195.00,
        'stock' => 90,
        'requires_prescription' => 1
    ],
    [
        'name' => 'Allegra 120mg Tablet',
        'salt' => 'Fexofenadine (120mg)',
        'price' => 218.00,
        'mrp' => 240.00,
        'stock' => 60,
        'requires_prescription' => 0
    ],
    [
        'name' => 'Thyronorm 50mcg Tablet',
        'salt' => 'Thyroxine (50mcg)',
        'price' => 120.00,
        'mrp' => 135.00,
        'stock' => 110,
        'requires_prescription' => 1
    ],
    [
        'name' => 'Lisinopril 20mg Tablet',
        'salt' => 'Lisinopril (20mg)',
        'price' => 140.00,
        'mrp' => 160.00,
        'stock' => 70,
        'requires_prescription' => 1
    ],
    [
        'name' => 'Azee 500 Tablet',
        'salt' => 'Azithromycin (500mg)',
        'price' => 119.00,
        'mrp' => 132.00,
        'stock' => 85,
        'requires_prescription' => 1
    ],
    [
        'name' => 'Vicks Action 500 Extra Tablet',
        'salt' => 'Paracetamol (500mg) + Diphenhydramine (25mg) + Caffeine (30mg)',
        'price' => 45.00,
        'mrp' => 50.00,
        'stock' => 300,
        'requires_prescription' => 0
    ],
    [
        'name' => 'Crocin Advance Tablet',
        'salt' => 'Paracetamol (500mg)',
        'price' => 20.00,
        'mrp' => 25.00,
        'stock' => 500,
        'requires_prescription' => 0
    ]
];

// First delete all existing medicines for a fresh start
$db->exec("DELETE FROM medicines");

$stmt = $db->prepare("INSERT INTO medicines (vendor_id, name, slug, salt, price, mrp, stock, expiry_date, requires_prescription, approval_status, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved', 1)");

$count = 0;
foreach ($realMedicines as $med) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $med['name']))) . '-' . bin2hex(random_bytes(2));
    
    // Random expiry date between 1 and 3 years from now
    $days = rand(365, 1095);
    $expiryDate = date('Y-m-d', strtotime("+$days days"));
    
    $stmt->execute([
        $vendorId,
        $med['name'],
        $slug,
        $med['salt'],
        $med['price'],
        $med['mrp'],
        $med['stock'],
        $expiryDate,
        $med['requires_prescription']
    ]);
    $count++;
}

echo "$count REAL Indian medicines seeded successfully!\n";
