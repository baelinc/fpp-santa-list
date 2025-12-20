<?php
/**
 * SANTA LIST ROBUST WORKER
 * Handles: Alignment, Dynamic Scaling, Colors, and Error Recovery
 */

$pluginName = "fpp-santa-list";
include_once "/opt/fpp/www/common.php";

$settingsFile = "/home/fpp/media/config/plugin." . $pluginName;
$logFile = "/home/fpp/media/logs/" . $pluginName . ".log";

function log_msg($msg) {
    global $logFile;
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $msg\n", FILE_APPEND);
}

log_msg("Service Started: Monitoring WordPress API.");

while(true) {
    // 1. REFRESH SETTINGS
    if (file_exists($settingsFile)) {
        $settings = parse_ini_file($settingsFile);
    } else {
        sleep(10); continue;
    }

    // Extract variables with robust defaults
    $wp_url        = $settings['wp_url'] ?? "";
    $sync_interval = (int)($settings['sync_interval'] ?? 60);
    $flip_speed    = (int)($settings['flip_speed'] ?? 10);
    $limit         = (int)($settings['name_limit'] ?? 6);
    $h_model       = $settings['header_model'] ?? 'Matrix_Header';
    $n_model       = $settings['names_model'] ?? 'Matrix_Names';
    $h_font        = (int)($settings['header_font'] ?? 18);
    $n_font        = (int)($settings['names_font'] ?? 12);
    $nice_color    = $settings['nice_color'] ?? '#00FF00';
    $naught_color  = $settings['naughty_color'] ?? '#FF0000';
    $text_color    = $settings['text_color'] ?? '#FFFFFF';
    
    // Alignment logic: fppmm uses Center, TopLeft, TopRight, etc.
    $alignSetting  = $settings['text_align'] ?? 'Center';
    $h_pos = ($alignSetting == "Center") ? "Center" : $alignSetting;
    $n_pos = ($alignSetting == "Center") ? "Center" : "Top" . $alignSetting;

    if (empty($wp_url)) { sleep(30); continue; }

    // 2. FETCH DATA WITH TIMEOUT PROTECTION
    $ctx = stream_context_create(['http' => ['timeout' => 5]]);
    $json = @file_get_contents($wp_url, false, $ctx);
    $data = json_decode($json, true);

    if ($data && (isset($data['nice']) || isset($data['naughty']))) {
        
        foreach (['nice', 'naughty'] as $type) {
            $names = array_slice($data[$type] ?? [], 0, $limit);
            $current_h_color = ($type == 'nice') ? $nice_color : $naught_color;
            
            // Update Header (Top Screen)
            $header_text = strtoupper($type) . " LIST";
            exec("fppmm -m $h_model -o on -c '$current_h_color' -s $h_font -p $h_pos -t '$header_text'");

            // Update Names (Bottom Screen)
            // Use double quotes for the shell command to handle names with spaces/apostrophes
            $names_block = implode("\n", $names);
            if(empty($names_block)) $names_block = "No Names Found";
            
            exec("fppmm -m $n_model -o on -c '$text_color' -s $n_font -p $n_pos -t \"$names_block\"");

            log_msg("Displayed $type list. Next flip in $flip_speed sec.");
            sleep($flip_speed);
        }
    } else {
        log_msg("Sync Failed: Could not parse API data. Retrying in 10s...");
        sleep(10);
        continue;
    }

    // 3. CALCULATE SMART SLEEP
    $napTime = max(5, $sync_interval - ($flip_speed * 2));
    sleep($napTime);
}
