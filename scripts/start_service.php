<?php
// scripts/start_service.php
// Kill any existing instances of the worker to prevent double-processing
exec("pkill -f fpp_santa_worker.php");

// Start the worker in the background and redirect output to a log file
// The '&' at the end is crucial—it lets the script run forever in the background
exec("php /home/fpp/media/plugins/fpp-santa-list/fpp_santa_worker.php > /home/fpp/media/logs/fpp-santa-list.log 2>&1 &");

// Return a success message to the UI
header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
?>
