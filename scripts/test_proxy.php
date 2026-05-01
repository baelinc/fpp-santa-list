<?php
// scripts/test_proxy.php
// Proxies the WordPress API test from FPP so we avoid CORS issues.
// Now correctly sends the Bearer token in the Authorization header.

$url   = $_GET['test_url']   ?? '';
$token = $_GET['test_token'] ?? '';

if (empty($url)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No URL provided']);
    exit;
}

$context = stream_context_create([
    'http' => [
        'method'  => 'GET',
        'header'  => "Authorization: Bearer " . $token . "\r\n" .
                     "Accept: application/json\r\n",
        'timeout' => 8,
        'ignore_errors' => true,
    ],
    'ssl' => [
        'verify_peer'      => false,
        'verify_peer_name' => false,
    ],
]);

$result = @file_get_contents($url, false, $context);

header('Content-Type: application/json');
if ($result === false) {
    echo json_encode(['error' => 'Could not reach ' . $url]);
} else {
    echo $result;
}
