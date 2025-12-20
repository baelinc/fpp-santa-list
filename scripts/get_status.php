<?php
header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate"); // Extra layer to kill caching

// This command looks for the process but excludes the 'grep' command itself
// The [f] is a regex trick: it matches the letter 'f' but the process list 
// will show 'grep [f]pp_santa_worker', which doesn't match the pattern '[f]pp_santa_worker'
exec("ps aux | grep '[f]pp_santa_worker.php'", $output);

$isRunning = false;
if (count($output) > 0) {
    $isRunning = true;
}

echo json_encode(['running' => $isRunning]);
?>
