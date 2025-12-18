<?php
$pluginName = "fpp-santa-list";
$settingsFile = "/home/fpp/media/config/plugin.fpp-santa-list.json";

// Handle Saving
if (isset($_POST['saveSettings'])) {
    $data = array(
        "api_url" => $_POST['api_url'],
        "model_header" => $_POST['model_header'],
        "model_names" => $_POST['model_names'],
        "interval" => $_POST['interval']
    );
    
    // Attempt to write the file
    if (file_put_contents($settingsFile, json_encode($data, JSON_PRETTY_PRINT)) === false) {
        $err = error_get_last();
        echo "ERROR: " . $err['message'];
    } else {
        echo "SUCCESS";
    }
    exit;
}

// Load settings for the form
$api_url = "https://christmas.onthehill.us/wp-json/santa/v1/list"; 
$model_header = "Screen1"; 
$model_names = "Screen2"; 
$interval = "10";

if (file_exists($settingsFile)) {
    $cur = json_decode(file_get_contents($settingsFile), true);
    if ($cur) {
        $api_url = $cur['api_url'];
        $model_header = $cur['model_header'];
        $model_names = $cur['model_names'];
        $interval = $cur['interval'];
    }
}
?>

<div id="fpp-santa-list" class="settings">
    <fieldset>
        <legend>🎅 Santa's List Config</legend>
        <table class="table">
            <tr><td class="settingLabel">API URL:</td><td><input type="text" id="api_url" size="64" value="<?php echo $api_url; ?>"></td></tr>
            <tr><td class="settingLabel">Header Model:</td><td><input type="text" id="model_header" value="<?php echo $model_header; ?>"></td></tr>
            <tr><td class="settingLabel">Names Model:</td><td><input type="text" id="model_names" value="<?php echo $model_names; ?>"></td></tr>
            <tr><td class="settingLabel">Interval (s):</td><td><input type="number" id="interval" value="<?php echo $interval; ?>"></td></tr>
        </table>
        <div style="margin-top:15px;">
            <button type="button" class="buttons btn-success" onclick="SaveSantaSettings();">Save Settings</button>
        </div>
    </fieldset>
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
    
    // We post back to this same file
    $.post(window.location.href, params, function(response) {
        if(response.trim() === "SUCCESS") {
            $.jGrowl("Settings Saved Successfully!", {theme: 'success'});
        } else {
            // This will now show the actual Linux error if it fails
            alert("Save Failed: " + response);
        }
    }).fail(function(xhr) {
        alert("Server Error: " + xhr.statusText);
    });
}
</script>
