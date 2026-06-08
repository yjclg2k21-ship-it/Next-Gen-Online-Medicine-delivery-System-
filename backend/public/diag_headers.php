<?php
header("Content-Type: application/json");
$headers = getallheaders();
echo json_encode([
    'headers' => $headers,
    'server' => [
        'HTTP_AUTHORIZATION' => $_SERVER['HTTP_AUTHORIZATION'] ?? 'MISSING',
        'REDIRECT_HTTP_AUTHORIZATION' => $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? 'MISSING',
        'HTTP_X_AUTHORIZATION' => $_SERVER['HTTP_X_AUTHORIZATION'] ?? 'MISSING'
    ]
]);

function getallheaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
}
