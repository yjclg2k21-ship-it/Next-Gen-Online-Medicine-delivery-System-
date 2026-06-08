<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=medicine_delivery', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Adding aadhaar_number to vendor_profiles...\n";
    $db->exec("ALTER TABLE vendor_profiles ADD COLUMN IF NOT EXISTS aadhaar_number VARCHAR(20) AFTER pan_card_number");

    echo "Success!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
