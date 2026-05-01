<?php
// scripts/get_log.php
// Returns the last 50 lines of the plugin log for display in the UI.

$pluginName = "fpp-santa-list";
$logFile    = "/home/fpp/media/logs/" . $pluginName . ".log";

header('Content-Type: text/plain');

if (!file_exists($logFile)) {
    echo "(Log file not found — service may not have run yet)";
    exit;
}

// Read last 50 lines efficiently
$lines = array_slice(file($logFile), -50);
echo implode('', $lines);
