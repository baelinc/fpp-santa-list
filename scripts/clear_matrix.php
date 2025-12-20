<?php
// scripts/clear_matrix.php
$pluginName = "fpp-santa-list";
include_once "/opt/fpp/www/common.php";

$settingsFile = "/home/fpp/media/config/plugin." . $pluginName;
$settings = parse_ini_file($settingsFile);

$h_model = $settings['header_model'] ?? 'Matrix_Header';
$n_model = $settings['names_model'] ?? 'Matrix_Names';

// -o off turns the overlay model off and clears the text
exec("fppmm -m $h_model -o off");
exec("fppmm -m $n_model -o off");

echo json_encode(["status" => "cleared"]);
?>
