<?php
require 'C:/xampp/htdocs/Next Gen Online Medicine Delivery System/backend/app/bootstrap.php';
$db = \App\Core\Database::getInstance()->getConnection();

$vendorId = 2; // Assuming vendor ID is 2 based on previous fixes

$medicines = $db->query("SELECT COUNT(*) FROM medicines WHERE vendor_id = $vendorId")->fetchColumn();
$orders = $db->query("SELECT COUNT(*) FROM orders WHERE vendor_id = $vendorId")->fetchColumn();
$payouts = $db->query("SELECT COUNT(*) FROM vendor_payouts WHERE vendor_id = $vendorId")->fetchColumn();
// $prescriptions = $db->query("SELECT COUNT(*) FROM prescriptions WHERE vendor_id = $vendorId")->fetchColumn();

echo "Vendor Data Summary (Vendor ID $vendorId):\n";
echo "Medicines: $medicines\n";
echo "Orders: $orders\n";
echo "Payouts: $payouts\n";
// echo "Prescriptions: $prescriptions\n";


// Get total count for all vendors just in case
$all_medicines = $db->query("SELECT COUNT(*) FROM medicines")->fetchColumn();
$all_orders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();

echo "\nTotal DB Data:\n";
echo "Total Medicines: $all_medicines\n";
echo "Total Orders: $all_orders\n";
