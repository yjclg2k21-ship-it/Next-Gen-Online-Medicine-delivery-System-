<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance()->getConnection();

$categories = [
    ['name' => 'Analgesics', 'description' => 'Pain relief and anti-inflammatory medications.'],
    ['name' => 'Antibiotics', 'description' => 'Medications that slow or stop the growth of bacteria.'],
    ['name' => 'Antivirals', 'description' => 'Medications used specifically for treating viral infections.'],
    ['name' => 'Antifungals', 'description' => 'Used to treat and prevent mycosis such as athlete\'s foot.'],
    ['name' => 'Cardiovascular', 'description' => 'Drugs for heart and blood vessel related conditions.'],
    ['name' => 'Respiratory', 'description' => 'Treatment for asthma, COPD, and other lung conditions.'],
    ['name' => 'Digestive System', 'description' => 'Stomach and intestinal health medications.'],
    ['name' => 'Diabetes Care', 'description' => 'Insulin and oral hypoglycemic agents.'],
    ['name' => 'Dermatology', 'description' => 'Skin, hair, and nail treatments.'],
    ['name' => 'Vitamins', 'description' => 'Nutritional supplements and multivitamins.'],
    ['name' => 'Baby Care', 'description' => 'Baby health and wellness products.'],
    ['name' => 'Personal Care', 'description' => 'Hygiene and daily grooming products.'],
    ['name' => 'Sexual Wellness', 'description' => 'Reproductive health and protection.'],
    ['name' => 'Ayurvedic', 'description' => 'Traditional Indian medicine and herbal remedies.'],
    ['name' => 'Homeopathy', 'description' => 'Alternative medicine system.'],
    ['name' => 'Orthopedics', 'description' => 'Joint, bone, and muscle support.']
];

$stmt = $db->prepare("INSERT IGNORE INTO categories (name, description, parent_id) VALUES (?, ?, NULL)");

foreach ($categories as $cat) {
    $stmt->execute([$cat['name'], $cat['description']]);
}

echo "Seeded " . count($categories) . " categories.\n";
