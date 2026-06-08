<?php
require __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    $vendors = [
        [
            'name' => 'Barad Pharmacy',
            'email' => 'barad_vendor@medimitra.com',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'vendor',
            'status' => 'active',
            'profile' => [
                'pharmacy_name' => 'Barad Pharmacy',
                'owner_name' => 'Abhay Nimbalkar',
                'license_number' => 'DL-BARAD-99128',
                'pharmacist_reg_number' => 'PR-901823',
                'pan_card_number' => 'ABCDE1234F',
                'aadhaar_number' => '123456789012',
                'gst_number' => '27ABCDE1234F1Z1',
                'address' => 'Main Road, Barad, Tal. Phaltan, Dist. Satara',
                'bank_name' => 'State Bank of India',
                'account_number' => '10020030040',
                'ifsc_code' => 'SBIN0001234',
                'latitude' => 17.92570000,
                'longitude' => 74.58330000,
                'delivery_radius_km' => 20.0
            ]
        ],
        [
            'name' => 'Taradgaon Medicals',
            'email' => 'taradgaon_vendor@medimitra.com',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'vendor',
            'status' => 'active',
            'profile' => [
                'pharmacy_name' => 'Taradgaon Medicals',
                'owner_name' => 'Sanjay Ranaware',
                'license_number' => 'DL-TRD-44910',
                'pharmacist_reg_number' => 'PR-334190',
                'pan_card_number' => 'FGHIJ5678K',
                'aadhaar_number' => '234567890123',
                'gst_number' => '27FGHIJ5678K2Z2',
                'address' => 'Taradgaon Bazar Peth, Tal. Phaltan, Dist. Satara',
                'bank_name' => 'Bank of Baroda',
                'account_number' => '20030040050',
                'ifsc_code' => 'BARB0TARADG',
                'latitude' => 18.04100000,
                'longitude' => 74.20850000,
                'delivery_radius_km' => 20.0
            ]
        ],
        [
            'name' => 'Lonand Medical & General',
            'email' => 'lonand_vendor@medimitra.com',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'vendor',
            'status' => 'active',
            'profile' => [
                'pharmacy_name' => 'Lonand Medical & General',
                'owner_name' => 'Vijay Kadam',
                'license_number' => 'DL-LND-33829',
                'pharmacist_reg_number' => 'PR-229108',
                'pan_card_number' => 'KLMNO9012P',
                'aadhaar_number' => '345678901234',
                'gst_number' => '27KLMNO9012P3Z3',
                'address' => 'Station Road, Lonand, Tal. Phaltan, Dist. Satara',
                'bank_name' => 'HDFC Bank',
                'account_number' => '30040050060',
                'ifsc_code' => 'HDFC0002291',
                'latitude' => 18.04600000,
                'longitude' => 74.19180000,
                'delivery_radius_km' => 20.0
            ]
        ],
        [
            'name' => 'Adarki Pharmacy',
            'email' => 'adarki_vendor@medimitra.com',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'vendor',
            'status' => 'active',
            'profile' => [
                'pharmacy_name' => 'Adarki Pharmacy',
                'owner_name' => 'Ramesh Bhoite',
                'license_number' => 'DL-ADAR-77189',
                'pharmacist_reg_number' => 'PR-881290',
                'pan_card_number' => 'PQRST3456U',
                'aadhaar_number' => '456789012345',
                'gst_number' => '27PQRST3456U4Z4',
                'address' => 'Adarki Khurd, Near Railway Station, Tal. Phaltan, Dist. Satara',
                'bank_name' => 'ICICI Bank',
                'account_number' => '40050060070',
                'ifsc_code' => 'ICIC0003456',
                'latitude' => 17.90560000,
                'longitude' => 74.19720000,
                'delivery_radius_km' => 20.0
            ]
        ]
    ];

    foreach ($vendors as $v) {
        // Check if user already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$v['email']]);
        $existing = $stmt->fetch();

        if ($existing) {
            echo "Vendor with email {$v['email']} already exists. Skipping.\n";
            continue;
        }

        // Insert user
        $userStmt = $db->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
        $userStmt->execute([$v['name'], $v['email'], $v['password'], $v['role'], $v['status']]);
        $userId = $db->lastInsertId();

        // Insert profile
        $p = $v['profile'];
        $profileStmt = $db->prepare("INSERT INTO vendor_profiles 
            (user_id, pharmacy_name, owner_name, license_number, pharmacist_reg_number, pan_card_number, aadhaar_number, gst_number, address, bank_name, account_number, ifsc_code, latitude, longitude, delivery_radius_km) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $profileStmt->execute([
            $userId,
            $p['pharmacy_name'],
            $p['owner_name'],
            $p['license_number'],
            $p['pharmacist_reg_number'],
            $p['pan_card_number'],
            $p['aadhaar_number'],
            $p['gst_number'],
            $p['address'],
            $p['bank_name'],
            $p['account_number'],
            $p['ifsc_code'],
            $p['latitude'],
            $p['longitude'],
            $p['delivery_radius_km']
        ]);

        echo "Created vendor '{$v['name']}' with User ID {$userId}.\n";
    }

    $db->commit();
    echo "All 4 demo vendors seeded successfully!\n";
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo "Seeding failed: " . $e->getMessage() . "\n";
}
