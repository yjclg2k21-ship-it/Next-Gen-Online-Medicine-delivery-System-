<?php
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
echo "password123: " . (password_verify('password123', $hash) ? 'TRUE' : 'FALSE') . "\n";
echo "password: " . (password_verify('password', $hash) ? 'TRUE' : 'FALSE') . "\n";
