<?php
$data = json_encode(['email' => 'user@medimitra.com', 'password' => 'password']);
$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\nOrigin: http://localhost\r\n",
        'method'  => 'POST',
        'content' => $data,
        'ignore_errors' => true
    ]
];
$context  = stream_context_create($options);
$result = file_get_contents('http://localhost/Next%20Gen%20Online%20Medicine%20Delivery%20System/api/auth/login', false, $context);
$loginResp = json_decode($result, true);
$token = $loginResp['token'];

echo "Login Response: " . $result . "\n\n";

if ($token) {
    $options2 = [
        'http' => [
            'header'  => "Content-type: application/json\r\nOrigin: http://localhost\r\nAuthorization: Bearer $token\r\n",
            'method'  => 'GET',
            'ignore_errors' => true
        ]
    ];
    $context2 = stream_context_create($options2);
    $dashboard = file_get_contents('http://localhost/Next%20Gen%20Online%20Medicine%20Delivery%20System/api/medicines/search', false, $context2);
    file_put_contents('output.json', $dashboard);
    print_r($http_response_header);
}
