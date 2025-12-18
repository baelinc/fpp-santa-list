<?php
$pluginName = "fpp-santa-list";

// Handle Saving via FPP System Command
if (isset($_POST['saveSettings'])) {
    $api = $_POST['api_url'];
    $h = $_POST['model_header'];
    $n = $_POST['model_names'];
    $i = $_POST['interval'];

    // We use the FPP 'config' utility to save these to the FPP database
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.api_url \"$api\"");
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.model_header \"$h\"");
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.model_names \"$n\"");
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.interval \"$i\"");
    
    echo "SUCCESS";
    exit;
}

// Load settings from FPP Database
$api_url = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.api_url");
$model_header = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.model_header");
$model_names = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.model_names");
$interval = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.interval");

// Set defaults if empty
if (empty($api_url)) $api_url = "https://christmas.onthehill.us/wp-json/santa/v1/list";
if (empty($model_header)) $model_header = "Screen1";
if (empty($model_names)) $model_names = "Screen2";
if (empty($interval)) $interval = "10";
?>

<div id="santa_list_wrapper">
    <fieldset>
        <legend>🎅 Santa's List Settings</legend>
        <table class="table">
            <tr><td>API URL:</td><td><input type="text" id="api_url" size="64" value="<?php echo $api_url; ?>"></td></tr>
            <tr><td>Header Model:</td><td><input type="text" id="model_header" value="<?php echo $model_header; ?>"></td></tr>
            <tr><td>Names Model:</td><td><input type="text" id="model_names" value="<?php echo $model_names; ?>"></td></tr>
            <tr><td>Interval (s):</td><td><input type="number" id="interval" value="<?php echo $interval; ?>"></td></tr>
        </table>
        
        <div style="margin-top:20px;">
            <button type="button" class="buttons btn-success" onclick="SaveSantaSettings();">Save Settings</button>
            <button type="button" class="buttons" style="background:#165b33; color:white;" onclick="TestConnection();">⚡ Test Connection</button>
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

    $.post(window.location.href, params, function(response) {
        if(response.trim() === "SUCCESS") {
            $.jGrowl("Settings Saved to FPP Database!", {theme: 'success'});
        } else {
            alert("Save Error: " + response);
        }
    });
}

function TestConnection() {
    var url = $("#api_url").val();
    $("#test_status").text("Testing...");
    
    $.getJSON(url, function(data) {
        $("#test_status").html("✅ Success! " + data.nice.length + " Nice / " + data.naughty.length + " Naughty");
    }).fail(function() { 
        $("#test_status").html("<span style='color:red;'>❌ API Connection Failed</span>"); 
    });
}
</script>
