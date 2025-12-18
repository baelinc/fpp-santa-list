<?php
/*
 * Santa's Naughty and Nice List - Full Setup Page
 */

// This variable MUST match your folder name in /home/fpp/media/plugins/
$pluginName = "fpp-santa-list";

// Fetch current settings from FPP's system
$pluginSettings = $settings['pluginSettings'];

// Default values if settings haven't been saved yet
$api_url = isset($pluginSettings['api_url']) ? $pluginSettings['api_url'] : '';
$model_header = isset($pluginSettings['model_header']) ? $pluginSettings['model_header'] : 'Screen1';
$model_names = isset($pluginSettings['model_names']) ? $pluginSettings['model_names'] : 'Screen2';
$interval = isset($pluginSettings['interval']) ? $pluginSettings['interval'] : '10';
?>

<div id="fpp_santa_list" class="settings">
    <fieldset>
        <legend>🎅 Santa's List Configuration</legend>
        
        <table class="table">
            <tr>
                <td class="settingLabel"><b>WordPress API URL:</b></td>
                <td>
                    <input type="text" id="api_url" size="64" value="<?php echo htmlspecialchars($api_url); ?>" placeholder="https://yoursite.com/wp-json/santa/v1/list">
                </td>
            </tr>
            <tr>
                <td class="settingLabel"><b>Screen 1 (Header Model):</b></td>
                <td>
                    <input type="text" id="model_header" value="<?php echo htmlspecialchars($model_header); ?>">
                    <small>Matrix model for "NICE/NAUGHTY"</small>
                </td>
            </tr>
            <tr>
                <td class="settingLabel"><b>Screen 2 (Name Model):</b></td>
                <td>
                    <input type="text" id="model_names" value="<?php echo htmlspecialchars($model_names); ?>">
                    <small>Matrix model for child's name</small>
                </td>
            </tr>
            <tr>
                <td class="settingLabel"><b>Refresh Interval:</b></td>
                <td>
                    <input type="number" id="interval" value="<?php echo $interval; ?>" min="2" style="width: 60px;"> seconds
                </td>
            </tr>
        </table>

        <div style="margin-top:20px;">
            <button type="button" class="buttons btn-success" onclick="SaveSantaSettings();">Save Settings</button>
            <button type="button" class="buttons" style="background:#165b33; color:white;" onclick="TestConnection();">⚡ Test Connection</button>
        </div>
    </fieldset>

    <div id="preview_area" style="display:none; margin-top:30px; padding:20px; background:#111; border: 2px solid #333; border-radius:10px;">
        <h3 style="color:white; margin:0; font-family: sans-serif;">🖥️ Matrix Preview</h3>
        <div style="display:flex; flex-wrap: wrap; gap:20px; justify-content:center; align-items:center; padding: 20px;">
            
            <div style="text-align:center;">
                <small style="color:#888;">Header</small>
                <div id="v_screen1" style="width:200px; height:80px; background:black; border:2px solid #444; display:flex; justify-content:center; align-items:center; color:#333; font-family:monospace; font-size:24px; font-weight:bold;">OFF</div>
            </div>

            <div style="text-align:center;">
                <small style="color:#888;">Child Name</small>
                <div id="v_screen2" style="width:300px; height:80px; background:black; border:2px solid #444; display:flex; justify-content:center; align-items:center; color:#333; font-family:monospace; font-size:24px; font-weight:bold;">OFF</div>
            </div>

        </div>
        <div id="test_status" style="margin-top:10px; color:#00ff00; font-family:monospace; text-align:center;"></div>
    </div>
</div>

<script>
function SaveSantaSettings() {
    var plugin = "<?php echo $pluginName; ?>";
    
    // Create the settings object
    var settingsData = {
        "api_url": $("#api_url").val(),
        "model_header": $("#model_header").val(),
        "model_names": $("#model_names").val(),
        "interval": $("#interval").val()
    };
    
    // FPP uses a specific API endpoint for plugin settings
    // We stringify the JSON and send it via POST
    $.post("api/config/plugin/" + plugin, JSON.stringify(settingsData))
        .done(function(data) {
            // Check if FPP provides the jGrowl notification system
            if ($.jGrowl) {
                $.jGrowl("Settings Saved Successfully!", {theme: 'success'});
            } else {
                alert("Settings Saved Successfully!");
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert("Save Failed: " + errorThrown + " (Ensure folder name is " + plugin + ")");
            console.error(jqXHR);
        });
}

function TestConnection() {
    var url = $("#api_url").val();
    if(!url) { alert("Enter URL first."); return; }

    $("#test_status").text("Contacting North Pole...");
    $("#preview_area").show();
    
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        timeout: 5000,
        success: function(data) {
            $("#test_status").html("✅ Connection Success!");
            if(data.nice && data.nice.length > 0) {
                $("#v_screen1").text("NICE").css({"color": "#00ff00", "text-shadow": "0 0 8px #00ff00"});
                $("#v_screen2").text(data.nice[0].toUpperCase()).css({"color": "#00ff00", "text-shadow": "0 0 8px #00ff00"});
            } else if (data.naughty && data.naughty.length > 0) {
                $("#v_screen1").text("NAUGHTY").css({"color": "#ff0000", "text-shadow": "0 0 8px #ff0000"});
                $("#v_screen2").text(data.naughty[0].toUpperCase()).css({"color": "#ffffff"});
            }
        },
        error: function() {
            $("#test_status").html("<span style='color:red;'>❌ API Error</span>");
        }
    });
}
</script>

<style>
.settingLabel { width: 30%; padding: 10px; font-weight: bold; }
#fpp_santa_list input[type="text"] { width: 90%; padding: 5px; }
</style>
