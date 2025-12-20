<?php
header('Content-Type: application/json');

// pgrep -f looks for the pattern in the full command line
// We use 'pgrep' because 'ps | grep' often catches itself, 
// causing the "Always Running" bug you are seeing.
exec("pgrep -f fpp_santa_worker.php", $pids);

// Filter out any potential empty strings and count
$isRunning = false;
if (!empty($pids)) {
    foreach ($pids as $pid) {
        if (is_numeric(trim($pid))) {
            $isRunning = true;
            break;
        }
    }
}

echo json_encode(['running' => $isRunning]);
?>
