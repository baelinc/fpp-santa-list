<?php
// scripts/test_proxy.php
// IONOS strips ALL Authorization headers before they reach PHP.
// Token is sent as ?token= query parameter instead.

$url   = $_GET['test_url']   ?? '';
$token = $_GET['test_token'] ?? '';

header('Content-Type: application/json');

if (empty($url)) {
    echo json_encode(['error' => 'No URL provided']);
    exit;
}

// Append token as query parameter — works on all hosts including IONOS
$sep = (strpos($url, '?') !== false) ? '&' : '?';
$url_with_token = $url . $sep . 'token=' . urlencode($token);

$ch = curl_init($url_with_token);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 3,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
]);

$result = curl_exec($ch);
$code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err    = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode(['error' => 'curl error: ' . $err]);
} elseif ($code !== 200) {
    echo json_encode(['error' => 'HTTP ' . $code, 'raw' => substr($result, 0, 500)]);
} else {
    echo $result;
}
