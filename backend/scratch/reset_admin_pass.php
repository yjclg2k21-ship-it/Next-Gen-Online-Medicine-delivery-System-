<?php
require_once __DIR__ . '/../app/Core/Database.php';

$email = 'admin@medimitra.com';
$password = 'admin123';
$hashed = password_hash($password, PASSWORD_BCRYPT);

try {
    $db = App\Core\Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
    $result = $stmt->execute([$hashed, $email]);
    if ($result) {
        echo "Password for $email has been reset to: $password\n";
    } else {
        echo "Failed to reset password.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
