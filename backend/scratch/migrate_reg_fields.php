<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=medicine_delivery', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Altering vendor_profiles...\n";
    $db->exec("ALTER TABLE vendor_profiles 
        ADD COLUMN IF NOT EXISTS dob DATE AFTER user_id, 
        ADD COLUMN IF NOT EXISTS pharmacist_reg_number VARCHAR(100) AFTER license_number, 
        ADD COLUMN IF NOT EXISTS pan_card_number VARCHAR(20) AFTER pharmacist_reg_number");

    echo "Altering delivery_profiles...\n";
    $db->exec("ALTER TABLE delivery_profiles 
        ADD COLUMN IF NOT EXISTS dob DATE AFTER user_id, 
        ADD COLUMN IF NOT EXISTS selfie_image VARCHAR(255) AFTER license_number, 
        ADD COLUMN IF NOT EXISTS aadhaar_number VARCHAR(20) AFTER selfie_image, 
        ADD COLUMN IF NOT EXISTS pan_number VARCHAR(20) AFTER aadhaar_number");

    echo "Migration successful!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
