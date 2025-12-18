<?php
$pluginName = "fpp-santa-list";

// 1. Handle Saving (Must be at the very top)
if (isset($_POST['saveSettings'])) {
    $api = $_POST['api_url'];
    $h = $_POST['model_header'];
    $n = $_POST['model_names'];
    $i = $_POST['interval'];

    // Save to FPP Database
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.api_url " . escapeshellarg($api));
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.model_header " . escapeshellarg($h));
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.model_names " . escapeshellarg($n));
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.interval " . escapeshellarg($i));
    
    // Send a clean response for the AJAX call
    header('Content-Type: text/plain');
    echo "SUCCESS";
    exit; 
}

// 2. Load Settings from FPP Database
$api_url = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.api_url");
$model_header = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.model_header");
$model_names = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.model_names");
$interval = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.interval");

// Set defaults if the database is empty
if (empty($api_url)) $api_url = "https://christmas.onthehill.us/wp-json/santa/v1/list";
if (empty($model_header)) $model_header = "Screen1";
if (empty($model_names)) $model_names = "Screen2";
if (empty($interval)) $interval = "10";
?>

<div id="santa_list_wrapper">
    <fieldset>
        <legend>🎅 Santa's List Settings</legend>
        <table class="table">
            <tr>
                <td class="settingLabel">API URL:</td>
                <td><input type="text" id="api_url" style="width:100%;" value="<?php echo htmlspecialchars($api_url); ?>"></td>
            </tr>
            <tr>
                <td class="settingLabel">Header Model:</td>
                <td><input type="text" id="model_header" value="<?php echo htmlspecialchars($model_header); ?>"></td>
            </tr>
            <tr>
                <td class="settingLabel">Names Model:</td>
                <td><input type="text" id="model_names" value="<?php echo htmlspecialchars($model_names); ?>"></td>
            </tr>
            <tr>
                <td class="settingLabel">Interval (s):</td>
                <td><input type="number" id="interval" value="<?php echo htmlspecialchars($interval); ?>"></td>
            </tr>
        </table>
        
        <div style="margin-top:20px;">
            <button type="button" class="buttons btn-success" onclick="SaveSantaSettings();">Save Settings</button>
            <button type="button" class="buttons" onclick="TestConnection();">Test Connection</button>
        </div>
    </fieldset>
    
    <div id="test_status" style="margin-top:15px; font-weight:bold; font-family:monospace;"></div>
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

    // Note: window.location.href works best across different FPP versions for AJAX
    $.post(window.location.href, params, function(response) {
        if(response.trim().includes("SUCCESS")) {
            $.jGrowl("Settings Saved!", {theme: 'success'});
        } else {
            alert("Save failed. FPP might have returned an error.");
        }
    });
}

function TestConnection() {
    var url = $("#api_url").val();
    $("#test_status").text("Contacting API...");
    $.getJSON(url, function(data) {
        $("#test_status").html("✅ Connection Success! Nice: " + data.nice.length);
    }).fail(function() { 
        $("#test_status").html("<span style='color:red;'>❌ Connection Failed</span>"); 
    });
}
</script>
