<?php
return [
    'app_name' => getenv('APP_NAME') ?: 'Mediflow',
    'app_env' => getenv('APP_ENV') ?: 'development',
    'app_debug' => getenv('APP_DEBUG') === 'true',
    'app_url' => getenv('APP_URL') ?: '*',
    'timezone' => 'Asia/Kolkata',
    
    // Security
    'jwt_secret' => $_ENV['JWT_SECRET'] ?? (getenv('JWT_SECRET') ?: 'mediflow_placeholder_secret'),
    'jwt_expiry' => (int)($_ENV['JWT_EXPIRY'] ?? (getenv('JWT_EXPIRY') ?: 86400)),
];
