<?php
// Load FPP helper functions
include_once 'common.php';

// 1. Get User Settings from FPP
$wp_url = "https://yourdomain.com/wp-json/santa/v1/list"; 
$limit = 6; // How many names to show
$header_model = "Matrix_Header";
$names_model = "Matrix_Names";

// 2. Fetch WordPress Data
$response = file_get_contents($wp_url);
$data = json_decode($response, true);

if (!$data) exit;

/**
 * Display Logic
 * We iterate through categories, showing the newest names first
 */
foreach (['nice', 'naughty'] as $type) {
    // Get newest names by taking the END of the array (since WP returns DESC)
    $list = array_slice($data[$type], 0, $limit); 
    $header_text = ucfirst($type) . " List";
    $header_color = ($type == 'nice') ? "#00FF00" : "#FF0000";
    
    // Clear Models first
    exec("/usr/bin/fppmm -m $header_model -o off");
    exec("/usr/bin/fppmm -m $names_model -o off");

    // Push Header (Top Screen)
    // fppmm parameters: model, on/off, color, font size, position, scroll, text
    $cmd_header = "fppmm -m $header_model -o on -c '$header_color' -n 'Arial' -s 18 -p 'Center' -t '$header_text'";
    exec($cmd_header);

    // Push Names (Bottom Screen)
    $names_string = implode("\n", $list);
    $cmd_names = "fppmm -m $names_model -o on -c '#FFFFFF' -n 'Arial' -s 12 -p 'TopLeft' -t '$names_string'";
    exec($cmd_names);

    sleep(10); // Show each list for 10 seconds
}
