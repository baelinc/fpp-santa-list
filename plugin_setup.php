<?php
$pluginName = "fpp-santa-list";
$logFile = "/home/fpp/media/logs/santa_worker.log";

// -------------------------------------------------------------------------
// 1. ACTION HANDLER: Save Settings
// -------------------------------------------------------------------------
if (isset($_POST['saveSettings'])) {
    header('Content-Type: text/plain');
    
    $api = $_POST['api_url'];
    $h = $_POST['model_header'];
    $n = $_POST['model_names'];
    $i = $_POST['interval'];

    // Use FPP's internal config tool to save to the system database
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.api_url " . escapeshellarg($api));
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.model_header " . escapeshellarg($h));
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.model_names " . escapeshellarg($n));
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.interval " . escapeshellarg($i));
    
    echo "SUCCESS";
    exit; 
}

// -------------------------------------------------------------------------
// 2. ACTION HANDLER: Test Connection (Server-Side)
// -------------------------------------------------------------------------
if (isset($_POST['testConnection'])) {
    header('Content-Type: text/plain');
    $url = $_POST['api_url'];
    
    $options = array('http' => array('timeout' => 5, 'user_agent' => 'FPP-Santa-Plugin'));
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        echo "FAIL: Pi could not reach the website. Check internet/DNS settings.";
    } else {
        $json = json_decode($response, true);
        if (isset($json['nice']) || isset($json['naughty'])) {
            $niceCount = count($json['nice'] ?? []);
            $naughtyCount = count($json['naughty'] ?? []);
            echo "SUCCESS: Found $niceCount Nice and $naughtyCount Naughty names!";
        } else {
            echo "FAIL: Connected, but the website didn't return a valid list.";
        }
    }
    exit;
}

// -------------------------------------------------------------------------
// 3. LOAD CURRENT SETTINGS
// -------------------------------------------------------------------------
$api_url = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.api_url");
$model_header = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.model_header");
$model_names = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.model_names");
$interval = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.interval");

// Default values
if (empty($api_url)) $api_url = "https://christmas.onthehill.us/wp-json/santa/v1/list";
if (empty($model_header)) $model_header = "Screen1";
if (empty($model_names)) $model_names = "Screen2";
if (empty($interval)) $interval = "10";
?>

<div id="santa-list-plugin">
    <fieldset>
        <legend>🎅 Santa's List Settings</legend>
        
        <table class="table">
            <tr>
                <td class="settingLabel" style="width:200px;"><b>WordPress API URL:</b></td>
                <td><input type="text" id="api_url" style="width:100%;" value="<?php echo htmlspecialchars($api_url); ?>"></td>
            </tr>
            <tr>
                <td class="settingLabel"><b>Header Model (NICE/NAUGHTY):</b></td>
                <td><input type="text" id="model_header" value="<?php echo htmlspecialchars($model_header); ?>"></td>
            </tr>
            <tr>
                <td class="settingLabel"><b>Name Model (Child's Name):</b></td>
                <td><input type="text" id="model_names" value="<?php echo htmlspecialchars($model_names); ?>"></td>
            </tr>
            <tr>
                <td class="settingLabel"><b>Refresh Interval (Seconds):</b></td>
                <td><input type="number" id="interval" style="width:80px;" value="<?php echo htmlspecialchars($interval); ?>"></td>
            </tr>
        </table>
        
        <div style="margin-top:20px;">
            <button type="button" class="buttons btn-success" onclick="SaveSantaSettings();">Save Settings</button>
            <button type="button" class="buttons" style="background:#165b33; color:white;" onclick="TestConnection();">⚡ Test Connection</button>
        </div>
    </fieldset>

    <div id="test_status" style="margin-top:15px; padding:10px; border-radius:5px; font-weight:bold; font-family:monospace; display:none;"></div>
    
    <?php if (file_exists($logFile)): ?>
    <fieldset style="margin-top:30px; background:#f9f9f9;">
        <legend>📜 Recent Worker Logs</legend>
        <pre style="max-height:200px; overflow-y:scroll; font-size:11px;"><?php echo shell_exec("tail -n 10 $logFile"); ?></pre>
    </fieldset>
    <?php endif; ?>
</div>

<script>
function SaveSantaSettings() {
    var params = {
        saveSettings: 1,
        api_url: $("#api_url").val(),
        model_header: $("#model_header").val(),
        model_names: $("#model_names").val(),
        interval: $("#interval").val()
    };

    $.post(window.location.href, params, function(response) {
        if(response.trim() === "SUCCESS") {
            $.jGrowl("Settings Saved Successfully!", {theme: 'success'});
        } else {
            alert("Save Failed: " + response);
        }
    });
}

function TestConnection() {
    var url = $("#api_url").val();
    $("#test_status").show().text("Connecting...").css({"background":"#eee", "color":"#333"});
    
    // Post back to PHP handler to avoid CORS issues
    $.post(window.location.href, { testConnection: 1, api_url: url }, function(response) {
        if (response.includes("SUCCESS")) {
            $("#test_status").text("✅ " + response).css({"background":"#dff0d8", "color":"#3c763d"});
        } else {
            $("#test_status").text("❌ " + response).css({"background":"#f2dede", "color":"#a94442"});
        }
    });
}
</script>

<style>
.settingLabel { font-weight: bold; vertical-align: middle; }
#santa-list-plugin input[type="text"], #santa-list-plugin input[type="number"] { padding: 5px; border: 1px solid #ccc; border-radius: 4px; }
</style>
