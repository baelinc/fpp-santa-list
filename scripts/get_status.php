<?php
// scripts/get_status.php
// Check if the worker script is currently running
$running = exec("pgrep -f fpp_santa_worker.php");

header('Content-Type: application/json');
echo json_encode(['running' => !empty($running)]);
?>
