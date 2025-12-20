<?php
// This script should run in a loop or via Cron
include_once 'common.php';

$wp_url = "YOUR_API_URL_HERE"; 
$limit = 6; 

while(true) {
    $response = file_get_contents($wp_url);
    $data = json_decode($response, true);

    if ($data) {
        foreach (['nice', 'naughty'] as $type) {
            // Get the newest names based on your limit
            $list = array_slice($data[$type], 0, $limit); 
            $header_text = strtoupper($type) . " LIST";
            $h_color = ($type == 'nice') ? "#00FF00" : "#FF0000";

            // 1. Update Top Screen (Header)
            // -s: font size, -p: position (Center), -t: text
            exec("fppmm -m Matrix_Header -o on -c '$h_color' -s 18 -p Center -t '$header_text'");

            // 2. Update Bottom Screen (Static List)
            // implode with \n creates the vertical stack from your image
            $names_block = implode("\n", $list);
            exec("fppmm -m Matrix_Names -o on -c '#FFFFFF' -s 12 -p TopLeft -t '$names_block'");

            sleep(10); // Display this list for 10 seconds before switching
        }
    }
    sleep(2); // Short pause before next API check
}
