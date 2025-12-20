<?php
// scripts/test_proxy.php
$url = $_GET['test_url'];

// Fetch the data using PHP (Server-to-Server)
$options = array(
    'http' => array(
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'GET',
        'timeout' => 5
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

// Send the result back to our FPP UI
header('Content-Type: application/json');
echo $result;
?>
