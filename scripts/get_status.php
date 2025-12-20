<?php
header('Content-Type: application/json');
// Search for the santa_worker.php process
exec("ps aux | grep santa_worker.php | grep -v grep", $output);

$isRunning = (count($output) > 0);

echo json_encode(['running' => $isRunning]);
?>
