<?php
header('Content-Type: application/json');

// Use 'pgrep' which is much more reliable than 'ps | grep' 
// because it won't return the grep process itself.
exec("pgrep -f fpp_santa_worker.php", $output);

// If the array output has any PIDs, it is running
$isRunning = (count($output) > 0);

echo json_encode(['running' => $isRunning]);
?>
