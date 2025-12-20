<?php
// We load common.php to access $pluginSettings
$pluginName = "fpp-santa-list";
include_once "/opt/fpp/www/common.php";
$settingsFile = "/home/fpp/media/config/plugin." . $pluginName;

while(true) {
    // Reload settings from disk every loop in case user changed them in UI
    if (file_exists($settingsFile)) {
        $pluginSettings = parse_ini_file($settingsFile);
    }

    $wp_url = $pluginSettings['wp_url'];
    $sync_interval = (int)$pluginSettings['sync_interval'];
    $flip_speed = (int)$pluginSettings['flip_speed'];
    $limit = (int)$pluginSettings['name_limit'];
    $h_model = $pluginSettings['header_model'];
    $n_model = $pluginSettings['names_model'];

    // Fetch the data
    $response = file_get_contents($wp_url);
    $data = json_decode($response, true);

    if ($data) {
        foreach (['nice', 'naughty'] as $type) {
            $list = array_slice($data[$type], 0, $limit);
            $h_color = ($type == 'nice') ? "#00FF00" : "#FF0000";
            
            // 1. Update Header
            exec("fppmm -m $h_model -o on -c '$h_color' -s 18 -p Center -t '" . strtoupper($type) . " LIST'");

            // 2. Update Names (Implode with \n for static vertical stack)
            $names_block = implode("\n", $list);
            exec("fppmm -m $n_model -o on -c '#FFFFFF' -s 12 -p TopLeft -t '$names_block'");

            sleep($flip_speed);
        }
    }
    // Wait for the sync interval minus the time spent flipping lists
    sleep(max(2, $sync_interval - ($flip_speed * 2)));
}
