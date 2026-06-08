<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once __DIR__ . '/../app/bootstrap.php';

$db = \App\Core\Database::getInstance()->getConnection();

// Get user
$stmt = $db->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ?");
$stmt->execute(['user@medimitra.com']);
$user = $stmt->fetch(\PDO::FETCH_ASSOC);

echo "User: " . json_encode(['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role'], 'status' => $user['status']]) . "\n";
echo "Hash starts with: " . substr($user['password'], 0, 7) . "\n";
echo "password123 verify: " . (password_verify('password123', $user['password']) ? "PASS" : "FAIL") . "\n";

// Simulate login API call
$_SERVER['REQUEST_METHOD'] = 'POST';
$loginData = json_encode(['email' => 'user@medimitra.com', 'password' => 'password123']);

// Write to php://input simulation
echo "\nSimulating login API call...\n";
try {
    $userModel = new \App\Models\User();
    $found = $userModel->findByEmail('user@medimitra.com');
    echo "findByEmail result: " . ($found ? "Found (ID: {$found['id']})" : "NOT FOUND") . "\n";
    
    if ($found) {
        echo "password_verify: " . (password_verify('password123', $found['password']) ? "PASS" : "FAIL") . "\n";
        echo "Status: {$found['status']}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
