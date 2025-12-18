<?php
$pluginName = "fpp-santa-list";

// Check for the save action before anything else is loaded
if (isset($_POST['saveSettings'])) {
    // We wrap this in a clean buffer to ensure no extra HTML is sent back
    ob_clean(); 
    
    $api = $_POST['api_url'];
    $h = $_POST['model_header'];
    $n = $_POST['model_names'];
    $i = $_POST['interval'];

    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.api_url \"$api\"");
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.model_header \"$h\"");
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.model_names \"$n\"");
    exec("/opt/fpp/bin/config set plugin.fpp-santa-list.interval \"$i\"");
    
    echo "SUCCESS";
    exit; // Stop execution immediately so no FPP HTML is appended
}

// Load settings
$api_url = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.api_url");
$model_header = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.model_header");
$model_names = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.model_names");
$interval = exec("/opt/fpp/bin/config get plugin.fpp-santa-list.interval");

// Defaults
if (empty($api_url)) $api_url = "https://christmas.onthehill.us/wp-json/santa/v1/list";
if (empty($model_header)) $model_header = "Screen1";
if (empty($model_names)) $model_names = "Screen2";
if (empty($interval)) $interval = "10";
?>

<script>
function SaveSantaSettings() {
    var params = {
        saveSettings: 1,
        api_url: $("#api_url").val(),
        model_header: $("#model_header").val(),
        model_names: $("#model_names").val(),
        interval: $("#interval").val()
    };

    // Use a specific path instead of location.href to avoid loading the whole UI
    $.post("plugin.php?plugin=fpp-santa-list&page=plugin_setup.php", params, function(response) {
        // We look for "SUCCESS" anywhere in the response
        if(response.indexOf("SUCCESS") !== -1) {
            $.jGrowl("Settings Saved!", {theme: 'success'});
        } else {
            alert("Save Error: Check FPP logs.");
        }
    });
}
</script>
