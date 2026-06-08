<?php
require __DIR__ . '/../app/bootstrap.php';

use App\Models\User;

$userModel = new User();

// Test 1: Role Helpers
$testUser = ['role' => 'admin'];
echo "Is Admin: " . (User::isAdmin($testUser) ? "Yes" : "No") . "\n";

$testVendor = ['role' => 'vendor'];
echo "Is Vendor: " . (User::isVendor($testVendor) ? "Yes" : "No") . "\n";

// Test 2: Polymorphic Profile
// Assuming ID 8 is a user (as seen in inspect_db)
$userProfile = $userModel->getUserProfile(8);
echo "User Profile (ID 8): " . ($userProfile['profile'] ? "Found" : "Not Found") . "\n";
if ($userProfile['profile']) {
    echo "Gender: " . ($userProfile['profile']['gender'] ?? 'N/A') . "\n";
}

// Assuming ID 9 is a vendor (as seen in inspect_db)
$vendorProfile = $userModel->getUserProfile(9);
echo "Vendor Profile (ID 9): " . ($vendorProfile['profile'] ? "Found" : "Not Found") . "\n";
if ($vendorProfile['profile']) {
    echo "Pharmacy: " . ($vendorProfile['profile']['pharmacy_name'] ?? 'N/A') . "\n";
}

echo "Verification Complete.\n";
