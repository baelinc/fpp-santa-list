<?php
/**
 * Santa's Naughty & Nice List — FPP Worker Process  (v4)
 *
 * Key fix: switched from file_get_contents to curl for all HTTP requests.
 * file_get_contents drops the Authorization header when following HTTP->HTTPS
 * redirects (301), which is exactly what IONOS hosting does.
 * curl handles redirects correctly and keeps the auth header.
 */

$pluginName   = "fpp-santa-list";
include_once "/opt/fpp/www/common.php";

$settingsFile = "/home/fpp/media/config/plugin." . $pluginName;
$logFile      = "/home/fpp/media/logs/" . $pluginName . ".log";

function log_msg($msg) {
    global $logFile;
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $msg\n", FILE_APPEND);
}

function hslToHex($h, $s, $l) {
    $h /= 360; $s /= 100; $l /= 100;
    $r = $g = $b = $l;
    $v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
    if ($v > 0) {
        $m  = $l + $l - $v;
        $sv = ($v - $m) / $v;
        $h  *= 6.0;
        $sx = floor($h);
        $f  = $h - $sx;
        $vsf  = $v * $sv * $f;
        $mid1 = $m + $vsf;
        $mid2 = $v - $vsf;
        switch ($sx) {
            case 0: $r=$v;    $g=$mid1; $b=$m;    break;
            case 1: $r=$mid2; $g=$v;    $b=$m;    break;
            case 2: $r=$m;    $g=$v;    $b=$mid1; break;
            case 3: $r=$m;    $g=$mid2; $b=$v;    break;
            case 4: $r=$mid1; $g=$m;    $b=$v;    break;
            case 5: $r=$v;    $g=$m;    $b=$mid2; break;
        }
    }
    return sprintf("#%02x%02x%02x", $r*255, $g*255, $b*255);
}

/**
 * Fetch names from WordPress using curl.
 * curl correctly keeps the Authorization header when following redirects,
 * unlike file_get_contents which drops it on HTTP->HTTPS 301 redirects.
 */
function fetchNames($wp_url, $api_token, $since = null) {
    $url = rtrim($wp_url, '/');
    if ($since) {
        $url .= (strpos($url, '?') !== false ? '&' : '?') . 'since=' . urlencode($since);
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $api_token,
            'Accept: application/json',
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);

    $raw  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) {
        log_msg("curl error: $err");
        return null;
    }
    if ($code !== 200) {
        log_msg("API returned HTTP $code");
        log_msg("Response: " . substr($raw, 0, 300));
        return null;
    }

    $data = json_decode($raw, true);
    if (!$data || !isset($data['names'])) {
        log_msg("Bad JSON or missing names key. Raw: " . substr($raw, 0, 300));
        return null;
    }

    $result = ['nice' => [], 'naughty' => []];
    foreach ($data['names'] as $entry) {
        $type = $entry['list_type']    ?? '';
        $name = $entry['display_name'] ?? '';
        if ($name && isset($result[$type])) {
            $result[$type][] = $name;
        }
    }
    $result['_server_time'] = $data['server_time'] ?? null;

    log_msg("Fetched " . count($result['nice']) . " nice, " . count($result['naughty']) . " naughty.");
    return $result;
}

function drawHeader($model, $text, $color, $fontSize, $position) {
    exec(sprintf('fppmm -m %s -o on -c %s -s %d -p %s -t %s',
        escapeshellarg($model), escapeshellarg($color),
        (int)$fontSize, escapeshellarg($position), escapeshellarg($text)
    ));
}

function drawNames($model, $names, $color, $fontSize, $position) {
    $text = implode("\n", $names) ?: "No Names Found";
    exec(sprintf('fppmm -m %s -o on -c %s -s %d -p %s -t %s',
        escapeshellarg($model), escapeshellarg($color),
        (int)$fontSize, escapeshellarg($position), escapeshellarg($text)
    ));
}

// ── Main loop ─────────────────────────────────────────────────────────────
log_msg("Santa Worker v4 started (curl edition).");

$allNames  = ['nice' => [], 'naughty' => []];
$lastSync  = 0;
$lastSince = null;
$hue       = 0;

while (true) {

    if (!file_exists($settingsFile)) {
        log_msg("Settings file missing — waiting...");
        sleep(10);
        continue;
    }
    $settings = parse_ini_file($settingsFile, false, INI_SCANNER_RAW);

    $wp_url        = $settings['wp_url']        ?? '';
    $api_token     = $settings['api_token']      ?? '';
    $sync_interval = (int)($settings['sync_interval'] ?? 60);
    $flip_speed    = (int)($settings['flip_speed']    ?? 10);
    $limit         = (int)($settings['name_limit']    ?? 6);
    $h_model       = $settings['header_model']   ?? 'Title';
    $n_model       = $settings['names_model']    ?? 'Names';
    $h_font_sz     = (int)($settings['header_font']   ?? 18);
    $n_font_sz     = (int)($settings['names_font']    ?? 12);
    $nice_color    = $settings['nice_color']     ?? '#00FF00';
    $naught_color  = $settings['naughty_color']  ?? '#FF0000';
    $text_color    = $settings['text_color']     ?? '#FFFFFF';
    $is_rainbow    = ($settings['rainbow_names'] ?? '0') === '1';
    $nice_label    = $settings['nice_text']      ?? 'NICE LIST';
    $naught_label  = $settings['naughty_text']   ?? 'NAUGHTY LIST';
    $alignSetting  = $settings['text_align']     ?? 'Center';
    $h_pos         = $alignSetting;
    $n_pos         = ($alignSetting === 'Center') ? 'Center' : 'Top' . $alignSetting;

    if (empty($wp_url) || empty($api_token)) {
        log_msg("WordPress URL or API token not configured.");
        sleep(15);
        continue;
    }

    // Sync names on interval
    $now = time();
    if ($now - $lastSync >= $sync_interval) {
        $fetched = fetchNames($wp_url, $api_token, $lastSince);
        if ($fetched) {
            if ($lastSince === null) {
                $allNames = ['nice' => $fetched['nice'], 'naughty' => $fetched['naughty']];
            } else {
                $allNames['nice']    = array_merge($allNames['nice'],    $fetched['nice']);
                $allNames['naughty'] = array_merge($allNames['naughty'], $fetched['naughty']);
            }
            $lastSince = $fetched['_server_time'] ?? null;
        }
        $lastSync = $now;
    }

    // Display both lists alternately
    foreach (['nice', 'naughty'] as $type) {
        $names       = array_slice($allNames[$type], 0, $limit);
        $headerText  = ($type === 'nice') ? $nice_label  : $naught_label;
        $headerColor = ($type === 'nice') ? $nice_color  : $naught_color;

        drawHeader($h_model, $headerText, $headerColor, $h_font_sz, $h_pos);

        $startTime = time();
        while ((time() - $startTime) < $flip_speed) {
            if ($is_rainbow) {
                drawNames($n_model, $names, hslToHex($hue, 100, 50), $n_font_sz, $n_pos);
                $hue = ($hue + 10) % 360;
                usleep(120000);
            } else {
                drawNames($n_model, $names, $text_color, $n_font_sz, $n_pos);
                sleep(max(1, $flip_speed));
                break;
            }
        }
    }

    sleep(1);
}
