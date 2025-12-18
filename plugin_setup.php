<?php
/*
 * Santa's Naughty and Nice List - Setup Page
 */

// DYNAMIC FOLDER DETECTION: This ensures the Save button always works
$pluginName = basename(dirname(__FILE__));

// Fetch current settings
$pluginSettings = $settings['pluginSettings'];

// Default values
$api_url = isset($pluginSettings['api_url']) ? $pluginSettings['api_url'] : 'https://christmas.onthehill.us/wp-json/santa/v1/list';
$model_header = isset($pluginSettings['model_header']) ? $pluginSettings['model_header'] : 'Screen1';
$model_names = isset($pluginSettings['model_names']) ? $pluginSettings['model_names'] : 'Screen2';
$interval = isset($pluginSettings['interval']) ? $pluginSettings['interval'] : '10';
?>

<div id="fpp_santa_list" class="settings">
    <fieldset>
        <legend>🎅 Santa's List Settings (Detected Folder: <?php echo $pluginName; ?>)</legend>
        
        <table class="table">
            <tr>
                <td class="settingLabel"><b>WordPress API URL:</b></td>
                <td>
                    <input type="text" id="api_url" size="64" value="<?php echo htmlspecialchars($api_url); ?>">
                </td>
            </tr>
            <tr>
                <td class="settingLabel"><b>Header Model (NICE/NAUGHTY):</b></td>
                <td>
                    <input type="text" id="model_header" value="<?php echo htmlspecialchars($model_header); ?>">
                </td>
            </tr>
            <tr>
                <td class="settingLabel"><b>Name Model (Child Name):</b></td>
                <td>
                    <input type="text" id="model_names" value="<?php echo htmlspecialchars($model_names); ?>">
                </td>
            </tr>
            <tr>
                <td class="settingLabel"><b>Refresh Interval (Seconds):</b></td>
                <td>
                    <input type="number" id="interval" value="<?php echo $interval; ?>" min="2">
                </td>
            </tr>
        </table>

        <div style="margin-top:20px;">
            <button type="button" class="buttons btn-success" onclick="SaveSantaSettings();">Save Settings</button>
            <button type="button" class="buttons" style="background:#165b33; color:white;" onclick="TestConnection();">⚡ Test Live Data</button>
        </div>
    </fieldset>

    <div id="preview_area" style="display:none; margin-top:30px; padding:20px; background:#000; border: 2px solid #333; border-radius:10px;">
        <h3 style="color:#fff; margin:0;">🖥️ Live Matrix Preview</h3>
        <div style="display:flex; gap:20px; justify-content:center; padding: 20px;">
            <div id="v_screen1" style="width:180px; height:80px; background:#111; border:2px solid #444; display:flex; justify-content:center; align-items:center; color:#333; font-family:monospace; font-size:22px; font-weight:bold;">OFF</div>
            <div id="v_screen2" style="width:280px; height:80px; background:#111; border:2px solid #444; display:flex; justify-content:center; align-items:center; color:#333; font-family:monospace; font-size:22px; font-weight:bold;">OFF</div>
        </div>
        <div id="test_status" style="margin-top:10px; color:#00ff00; font-family:monospace; text-align:center;"></div>
    </div>
</div>

<script>
function SaveSantaSettings() {
    // We pull the folder name dynamically from the PHP variable
    var plugin = "<?php echo $pluginName; ?>";
    
    var settingsData = {
        "api_url": $("#api_url").val(),
        "model_header": $("#model_header").val(),
        "model_names": $("#model_names").val(),
        "interval": $("#interval").val()
    };
    
    // FPP API call to save settings
    $.post("api/config/plugin/" + plugin, JSON.stringify(settingsData))
        .done(function() {
            $.jGrowl("Settings Saved to: " + plugin, {theme: 'success'});
        })
        .fail(function(xhr) {
            alert("Save Failed! FPP returned: " + xhr.statusText);
        });
}

function TestConnection() {
    var url = $("#api_url").val();
    $("#test_status").text("Fetching from: " + url);
    $("#preview_area").show();
    
    $.getJSON(url, function(data) {
        $("#test_status").html("✅ Success! " + data.nice.length + " Nice / " + data.naughty.length + " Naughty");
        
        // Show the first 'Nice' name if it exists
        if(data.nice && data.nice.length > 0) {
            $("#v_screen1").text("NICE").css({"color": "#00ff00", "text-shadow": "0 0 8px #00ff00"});
            $("#v_screen2").text(data.nice[0].toUpperCase()).css({"color": "#00ff00", "text-shadow": "0 0 8px #00ff00"});
        }
    }).fail(function() {
        $("#test_status").html("<span style='color:red;'>❌ API Connection Failed</span>");
    });
}
</script>
