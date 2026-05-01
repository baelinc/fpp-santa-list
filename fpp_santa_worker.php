<?php
/**
 * Santa's Naughty & Nice List — FPP Worker Process  (v3)
 * Runs as a background PHP process, started via start_service.php
 *
 * Key fixes vs v2:
 *  - WordPress API now sends Bearer token auth header
 *  - Parses the correct JSON structure returned by the WP plugin
 *    (response is {success, names:[{display_name, list_type, ...}]})
 *  - Title/header panel: 64×32 landscape (not rotated)
 *  - Names panel: 96×128 after FPP rotation
 *  - Scrolling names support when list exceeds name_limit
 *  - Incremental sync using ?since= timestamp to avoid re-fetching all names
 *  - Cleaner rainbow cycling
 *  - Better logging with timestamps
 */

$pluginName  = "fpp-santa-list";
include_once "/opt/fpp/www/common.php";

$settingsFile = "/home/fpp/media/config/plugin." . $pluginName;
$logFile      = "/home/fpp/media/logs/" . $pluginName . ".log";

// ── Logging ───────────────────────────────────────────────────────────────
function log_msg($msg) {
    global $logFile;
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $msg\n", FILE_APPEND);
}

// ── HSL → hex helper ──────────────────────────────────────────────────────
function hslToHex($h, $s, $l) {
    $h /= 360; $s /= 100; $l /= 100;
    $r = $g = $b = $l;
    $v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
    if ($v > 0) {
        $m  = $l + $l - $v;
        $sv = ($v - $m) / $v;
        $h *= 6.0;
        $sextant = floor($h);
        $fract   = $h - $sextant;
        $vsf  = $v * $sv * $fract;
        $mid1 = $m + $vsf;
        $mid2 = $v - $vsf;
        switch ($sextant) {
            case 0: $r = $v;    $g = $mid1; $b = $m;    break;
            case 1: $r = $mid2; $g = $v;    $b = $m;    break;
            case 2: $r = $m;    $g = $v;    $b = $mid1; break;
            case 3: $r = $m;    $g = $mid2; $b = $v;    break;
            case 4: $r = $mid1; $g = $m;    $b = $v;    break;
            case 5: $r = $v;    $g = $m;    $b = $mid2; break;
        }
    }
    return sprintf("#%02x%02x%02x", $r * 255, $g * 255, $b * 255);
}

// ── Fetch names from WordPress ────────────────────────────────────────────
/**
 * Calls the WordPress REST API with Bearer token auth.
 * Returns ['nice' => [...display_names], 'naughty' => [...display_names]]
 * Uses $since to only retrieve new names since the last poll.
 */
function fetchNames($wp_url, $api_token, $since = null) {
    $url = rtrim($wp_url, '/');

    // Append ?since= for incremental updates
    if ($since) {
        $url .= '?since=' . urlencode($since);
    }

    $context = stream_context_create([
        'http' => [
            'method'  => 'GET',
            'header'  => "Authorization: Bearer " . $api_token . "\r\n" .
                         "Accept: application/json\r\n",
            'timeout' => 8,
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false,
        ],
    ]);

    $raw  = @file_get_contents($url, false, $context);
    $data = $raw ? json_decode($raw, true) : null;

    if (!$data || !isset($data['names'])) {
        log_msg("API fetch failed or bad response from: $url");
        log_msg("Raw: " . substr($raw, 0, 200));
        return null;
    }

    $result = ['nice' => [], 'naughty' => []];
    foreach ($data['names'] as $entry) {
        $type = $entry['list_type'] ?? '';
        $name = $entry['display_name'] ?? '';
        if ($name && isset($result[$type])) {
            $result[$type][] = $name;
        }
    }

    // Return server_time so we can do incremental syncs
    $result['_server_time'] = $data['server_time'] ?? null;

    log_msg("Fetched " . count($result['nice']) . " nice, " . count($result['naughty']) . " naughty names.");
    return $result;
}

// ── Draw header panel via fppmm ───────────────────────────────────────────
function drawHeader($model, $text, $color, $fontSize, $position) {
    $cmd = sprintf(
        'fppmm -m %s -o on -c %s -s %d -p %s -t %s',
        escapeshellarg($model),
        escapeshellarg($color),
        (int)$fontSize,
        escapeshellarg($position),
        escapeshellarg($text)
    );
    exec($cmd);
}

// ── Draw names panel via fppmm ────────────────────────────────────────────
function drawNames($model, $names, $color, $fontSize, $position) {
    $text = implode("\n", $names) ?: "No Names Found";
    $cmd  = sprintf(
        'fppmm -m %s -o on -c %s -s %d -p %s -t %s',
        escapeshellarg($model),
        escapeshellarg($color),
        (int)$fontSize,
        escapeshellarg($position),
        escapeshellarg($text)
    );
    exec($cmd);
}

// ── Turn off a model ──────────────────────────────────────────────────────
function clearModel($model) {
    exec("fppmm -m " . escapeshellarg($model) . " -o off");
}

// ── Main loop ─────────────────────────────────────────────────────────────
log_msg("Santa Worker v3 started.");

$allNames   = ['nice' => [], 'naughty' => []];
$lastSync   = 0;
$lastSince  = null;
$hue        = 0;

while (true) {

    // Load settings fresh each loop (hot-reload support)
    if (!file_exists($settingsFile)) {
        log_msg("Settings file missing: $settingsFile — waiting...");
        sleep(10);
        continue;
    }
    $settings = parse_ini_file($settingsFile);

    $wp_url        = $settings['wp_url']        ?? '';
    $api_token     = $settings['api_token']     ?? '';
    $sync_interval = (int)($settings['sync_interval'] ?? 60);
    $flip_speed    = (int)($settings['flip_speed']    ?? 10);
    $limit         = (int)($settings['name_limit']    ?? 6);
    $h_model       = $settings['header_model']  ?? 'Title';
    $n_model       = $settings['names_model']   ?? 'Names';
    $h_font_sz     = (int)($settings['header_font']   ?? 18);
    $n_font_sz     = (int)($settings['names_font']    ?? 12);
    $nice_color    = $settings['nice_color']    ?? '#00FF00';
    $naught_color  = $settings['naughty_color'] ?? '#FF0000';
    $text_color    = $settings['text_color']    ?? '#FFFFFF';
    $is_rainbow    = ($settings['rainbow_names'] ?? '0') === '1';
    $nice_label    = $settings['nice_text']     ?? 'NICE LIST';
    $naught_label  = $settings['naughty_text']  ?? 'NAUGHTY LIST';
    $alignSetting  = $settings['text_align']    ?? 'Center';

    // fppmm position strings
    $h_pos = $alignSetting;
    $n_pos = ($alignSetting === 'Center') ? 'Center' : 'Top' . $alignSetting;

    if (empty($wp_url) || empty($api_token)) {
        log_msg("WordPress URL or API token not set — check plugin settings.");
        sleep(15);
        continue;
    }

    // ── Sync names from WordPress ──────────────────────────────────────
    $now = time();
    if ($now - $lastSync >= $sync_interval) {
        $fetched = fetchNames($wp_url, $api_token, $lastSince);
        if ($fetched) {
            if ($lastSince === null) {
                // First load — replace entire list
                $allNames['nice']    = $fetched['nice'];
                $allNames['naughty'] = $fetched['naughty'];
            } else {
                // Incremental — append new names only
                $allNames['nice']    = array_merge($allNames['nice'],    $fetched['nice']);
                $allNames['naughty'] = array_merge($allNames['naughty'], $fetched['naughty']);
            }
            $lastSince = $fetched['_server_time'] ?? null;
        }
        $lastSync = $now;
    }

    // ── Display both lists alternately ───────────────────────────────
    foreach (['nice', 'naughty'] as $type) {

        $names       = array_slice($allNames[$type], 0, $limit);
        $headerText  = ($type === 'nice') ? $nice_label   : $naught_label;
        $headerColor = ($type === 'nice') ? $nice_color   : $naught_color;

        // Draw the static header panel
        drawHeader($h_model, $headerText, $headerColor, $h_font_sz, $h_pos);

        // Display names for flip_speed seconds
        $startTime = time();
        while ((time() - $startTime) < $flip_speed) {

            if ($is_rainbow) {
                $rainbowColor = hslToHex($hue, 100, 50);
                $hue = ($hue + 10) % 360;
                drawNames($n_model, $names, $rainbowColor, $n_font_sz, $n_pos);
                usleep(120000); // 120ms
            } else {
                drawNames($n_model, $names, $text_color, $n_font_sz, $n_pos);
                sleep(max(1, $flip_speed));
                break;
            }
        }
    }

    // Brief pause before next cycle to avoid hammering fppmm
    sleep(1);
}
