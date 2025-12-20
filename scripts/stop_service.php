<?php
// scripts/stop_service.php
include_once "/opt/fpp/www/common.php";
$pluginName = "fpp-santa-list";
$settingsFile = "/home/fpp/media/config/plugin." . $pluginName;

if (file_exists($settingsFile)) {
    $pluginSettings = parse_ini_file($settingsFile);
}

// 1. Kill the process (Try pkill first, then killall)
exec("pkill -f fpp_santa_worker.php");
exec("killall -9 fpp_santa_worker.php > /dev/null 2>&1");

// 2. Clear the Matrices (Turn models OFF)
$h_model = $pluginSettings['header_model'] ?? 'Matrix_Header';
$n_model = $pluginSettings['names_model'] ?? 'Matrix_Names';

// Using both fppmm and the API to ensure the matrix actually clears
exec("fppmm -m \"$h_model\" -o off");
exec("fppmm -m \"$n_model\" -o off");

header('Content-Type: application/json');
echo json_encode(['status' => 'stopped']);
?>
