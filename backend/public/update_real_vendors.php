<?php
require __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    $updates = [
        5 => [
            'name' => 'Vighnaharta Medical & General Store',
            'profile' => [
                'pharmacy_name' => 'Vighnaharta Medical & General Store',
                'owner_name' => 'Vikram R. Nimbalkar',
                'license_number' => '20B-MH-SAT-189204, 21B-MH-SAT-189205',
                'pharmacist_reg_number' => 'PH-142831',
                'pan_card_number' => 'ANJPN1289K',
                'aadhaar_number' => '543210987654',
                'gst_number' => '27ANJPN1289K1Z4',
                'address' => 'Near Grampanchayat Office, Barad, Tal. Phaltan, Dist. Satara - 415523',
                'bank_name' => 'State Bank of India',
                'account_number' => '39485720194',
                'ifsc_code' => 'SBIN0001354',
                'latitude' => 17.92570000,
                'longitude' => 74.58330000,
                'delivery_radius_km' => 20.0
            ]
        ],
        6 => [
            'name' => 'Shree Samarth Medical & General Store',
            'profile' => [
                'pharmacy_name' => 'Shree Samarth Medical & General Store',
                'owner_name' => 'Dnyaneshwar S. Ranaware',
                'license_number' => '20B-MH-SAT-143920, 21B-MH-SAT-143921',
                'pharmacist_reg_number' => 'PH-158294',
                'pan_card_number' => 'BRPPR8829C',
                'aadhaar_number' => '892047291849',
                'gst_number' => '27BRPPR8829C2Z3',
                'address' => 'Taradgaon Bazar Peth, Main Chowk, Tal. Phaltan, Dist. Satara - 415528',
                'bank_name' => 'Bank of Maharashtra',
                'account_number' => '60281940293',
                'ifsc_code' => 'MAHB0000412',
                'latitude' => 18.04100000,
                'longitude' => 74.20850000,
                'delivery_radius_km' => 20.0
            ]
        ],
        7 => [
            'name' => 'Siddhanath Medical Store',
            'profile' => [
                'pharmacy_name' => 'Siddhanath Medical Store',
                'owner_name' => 'Prakash V. Kadam',
                'license_number' => '20B-MH-SAT-99281, 21B-MH-SAT-99282',
                'pharmacist_reg_number' => 'PH-99201',
                'pan_card_number' => 'CLKPD4439A',
                'aadhaar_number' => '729104829301',
                'gst_number' => '27CLKPD4439A1Z9',
                'address' => 'Station Road, Near ST Stand, Lonand, Tal. Phaltan, Dist. Satara - 415521',
                'bank_name' => 'HDFC Bank',
                'account_number' => '501004392019',
                'ifsc_code' => 'HDFC0002711',
                'latitude' => 18.04600000,
                'longitude' => 74.19180000,
                'delivery_radius_km' => 20.0
            ]
        ],
        8 => [
            'name' => 'Bhairavnath Medical Store',
            'profile' => [
                'pharmacy_name' => 'Bhairavnath Medical Store',
                'owner_name' => 'Dattatray B. Bhoite',
                'license_number' => '20B-MH-SAT-204910, 21B-MH-SAT-204911',
                'pharmacist_reg_number' => 'PH-201823',
                'pan_card_number' => 'DMKPB9921D',
                'aadhaar_number' => '301948291048',
                'gst_number' => '27DMKPB9921D1Z2',
                'address' => 'Adarki Khurd, Opp. Railway Station, Tal. Phaltan, Dist. Satara - 415522',
                'bank_name' => 'Bank of Baroda',
                'account_number' => '33410293049',
                'ifsc_code' => 'BARB0ADARKI',
                'latitude' => 17.90560000,
                'longitude' => 74.19720000,
                'delivery_radius_km' => 20.0
            ]
        ]
    ];

    foreach ($updates as $userId => $v) {
        // Update user display name
        $userStmt = $db->prepare("UPDATE users SET name = ? WHERE id = ?");
        $userStmt->execute([$v['name'], $userId]);

        // Update vendor profile details
        $p = $v['profile'];
        $profileStmt = $db->prepare("UPDATE vendor_profiles SET 
            pharmacy_name = ?, owner_name = ?, license_number = ?, pharmacist_reg_number = ?, 
            pan_card_number = ?, aadhaar_number = ?, gst_number = ?, address = ?, 
            bank_name = ?, account_number = ?, ifsc_code = ?, latitude = ?, longitude = ?, delivery_radius_km = ?
            WHERE user_id = ?");
        
        $profileStmt->execute([
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
            $p['delivery_radius_km'],
            $userId
        ]);

        echo "Updated database details for Vendor ID {$userId} -> '{$v['name']}'\n";
    }

    $db->commit();
    echo "All 4 vendors successfully populated with realistic operational data!\n";
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo "Update failed: " . $e->getMessage() . "\n";
}
