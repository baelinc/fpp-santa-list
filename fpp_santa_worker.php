<?php
$pluginName = "fpp-santa-list";
include_once "/opt/fpp/www/common.php";

$settingsFile = "/home/fpp/media/config/plugin." . $pluginName;
$logFile = "/home/fpp/media/logs/" . $pluginName . ".log";

function log_msg($msg) {
    global $logFile;
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $msg\n", FILE_APPEND);
}

// Helper to convert HSL to Hex for the Rainbow Effect
function hslToHex($h, $s, $l) {
    $h /= 360; $s /= 100; $l /= 100;
    $r = $l; $g = $l; $b = $l;
    $v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
    if ($v > 0) {
        $m = $l + $l - $v;
        $sv = ($v - $m) / $v;
        $h *= 6.0;
        $sextant = floor($h);
        $fract = $h - $sextant;
        $vsf = $v * $sv * $fract;
        $mid1 = $m + $vsf;
        $mid2 = $v - $vsf;
        switch ($sextant) {
            case 0: $r = $v; $g = $mid1; $b = $m; break;
            case 1: $r = $mid2; $g = $v; $b = $m; break;
            case 2: $r = $m; $g = $v; $b = $mid1; break;
            case 3: $r = $m; $g = $mid2; $b = $v; break;
            case 4: $r = $mid1; $g = $m; $b = $v; break;
            case 5: $r = $v; $g = $m; $b = $mid2; break;
        }
    }
    return sprintf("#%02x%02x%02x", $r * 255, $g * 255, $b * 255);
}

log_msg("Service Started with Styling and Rainbow support.");

while(true) {
    if (file_exists($settingsFile)) { $settings = parse_ini_file($settingsFile); } else { sleep(10); continue; }

    $wp_url         = $settings['wp_url'] ?? "";
    $sync_interval  = (int)($settings['sync_interval'] ?? 60);
    $flip_speed     = (int)($settings['flip_speed'] ?? 10);
    $limit          = (int)($settings['name_limit'] ?? 6);
    $h_model        = $settings['header_model'] ?? 'Matrix_Header';
    $n_model        = $settings['names_model'] ?? 'Matrix_Names';
    $h_font_sz      = (int)($settings['header_font'] ?? 18);
    $n_font_sz      = (int)($settings['names_font'] ?? 12);
    $nice_color     = $settings['nice_color'] ?? '#00FF00';
    $naught_color   = $settings['naughty_color'] ?? '#FF0000';
    $text_color     = $settings['text_color'] ?? '#FFFFFF';
    $is_rainbow     = ($settings['rainbow_names'] ?? '0') == '1';
    
    // Style Logic: Try to find a bold/italic version of the font if available
    // Note: This assumes standard FPP font naming or can be adjusted to specific .ttf files
    $style_suffix = "";
    if (($settings['font_bold'] ?? '0') == '1') $style_suffix .= "Bold";
    if (($settings['font_italic'] ?? '0') == '1') $style_suffix .= "Italic";
    
    $nice_label     = $settings['nice_text'] ?? 'NICE LIST';
    $naught_label   = $settings['naughty_text'] ?? 'NAUGHTY LIST';
    
    $alignSetting   = $settings['text_align'] ?? 'Center';
    $h_pos = ($alignSetting == "Center") ? "Center" : $alignSetting;
    $n_pos = ($alignSetting == "Center") ? "Center" : "Top" . $alignSetting;

    if (empty($wp_url)) { sleep(30); continue; }

    $json = @file_get_contents($wp_url, false, stream_context_create(['http' => ['timeout' => 5]]));
    $data = json_decode($json, true);

    if ($data) {
        foreach (['nice', 'naughty'] as $type) {
            $names = array_slice($data[$type] ?? [], 0, $limit);
            $names_block = implode("\n", $names) ?: "No Names Found";
            
            $header_text = ($type == 'nice') ? $nice_label : $naught_label;
            $h_color = ($type == 'nice') ? $nice_color : $naught_color;

            // 1. Draw Static Header
            exec("fppmm -m $h_model -o on -c '$h_color' -s $h_font_sz -p $h_pos -t " . escapeshellarg($header_text));

            // 2. Display Names with Rainbow or Static Color
            $startTime = time();
            $hue = 0;
            
            while ((time() - $startTime) < $flip_speed) {
                if ($is_rainbow) {
                    $current_color = hslToHex($hue, 100, 50);
                    $hue = ($hue + 10) % 360; // Adjust '10' for faster/slower rainbow cycle
                    exec("fppmm -m $n_model -o on -c '$current_color' -s $n_font_sz -p $n_pos -t " . escapeshellarg($names_block));
                    usleep(100000); // 100ms delay for smooth animation
                } else {
                    exec("fppmm -m $n_model -o on -c '$text_color' -s $n_font_sz -p $n_pos -t " . escapeshellarg($names_block));
                    sleep($flip_speed); // Just wait if static
                    break;
                }
            }
        }
    } else {
        sleep(10);
    }

    $napTime = max(5, $sync_interval - ($flip_speed * 2));
    sleep($napTime);
}
