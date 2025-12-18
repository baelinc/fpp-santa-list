<?php
/*
 * Santa's Naughty and Nice List - FPP Setup Page
 */

// This file is included by FPP, so we have access to $settings
$pluginName = "fpp-santa-list";
$pluginSettings = $settings['pluginSettings'];

// Fetch saved settings or use defaults
$api_url = isset($pluginSettings['api_url']) ? $pluginSettings['api_url'] : '';
$model_header = isset($pluginSettings['model_header']) ? $pluginSettings['model_header'] : 'Screen1';
$model_names = isset($pluginSettings['model_names']) ? $pluginSettings['model_names'] : 'Screen2';
$interval = isset($pluginSettings['interval']) ? $pluginSettings['interval'] : '10';
?>

<div id="fpp_santa_list" class="settings">
    <fieldset>
        <legend>🎅 Santa's List API Settings</legend>
        
        <p>Enter your WordPress API URL to pull the newest Naughty and Nice names to your LED Matrix.</p>

        <table class="table">
            <tr>
                <td class="settingLabel"><b>WordPress API URL:</b></td>
                <td>
                    <input type="text" id="api_url" size="64" value="<?php echo $api_url; ?>" placeholder="https://yoursite.com/wp-json/santa/v1/list">
                </td>
            </tr>
            <tr>
                <td class="settingLabel"><b>Screen 1 Model (Header):</b></td>
                <td>
                    <input type="text" id="model_header" value="<?php echo $model_header; ?>">
                    <small>Displays "NICE" or "NAUGHTY"</small>
                </td>
            </tr>
            <tr>
                <td class="settingLabel"><b>Screen 2 Model (Names):</b></td>
                <td>
                    <input type="text" id="model_names" value="<?php echo $model_names; ?>">
                    <small>Displays the Child's Name (Static)</small>
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
            <button type="button" class="buttons" onclick="SaveSantaSettings();">Save Settings</button>
            <button type="button" class="buttons" style="background:#165b33; color:white;" onclick="TestConnection();">⚡ Test Connection & Preview</button>
        </div>
    </fieldset>

    <div id="preview_area" style="display:none; margin-top:30px; padding:20px; background:#111; border: 2px solid #333; border-radius:10px;">
        <h3 style="color:white; margin-top:0; font-family: sans-serif;">🖥️ LED Matrix Live Preview</h3>
        <div style="display:flex; flex-wrap: wrap; gap:20px; justify-content:center; align-items:center; padding: 20px;">
            
            <div style="text-align:center;">
                <small style="color:#888; display:block; margin-bottom:5px;">Screen 1 (Header)</small>
                <div id="v_screen1" style="width:220px; height:100px; background:black; border:4px solid #444; display:flex; justify-content:center; align-items:center; color:#555; font-family:'Courier New', monospace; font-size:28px; font-weight:bold; letter-spacing: 2px;">
                    OFF
                </div>
            </div>

            <div style="text-align:center;">
                <small style="color:#888; display:block; margin-bottom:5px;">Screen 2 (Name)</small>
                <div id="v_screen2" style="width:340px; height:100px; background:black; border:4px solid #444; display:flex; justify-content:center; align-items:center; color:#555; font-family:'Courier New', monospace; font-size:28px; font-weight:bold; letter-spacing: 2px;">
                    OFF
                </div>
            </div>

        </div>
        <div id="test_status" style="margin-top:15px; color:#00ff00; font-family:monospace; text-align:center; font-size: 1.1em;"></div>
    </div>
</div>

<script>
function SaveSantaSettings() {
    // FPP-specific object for saving settings
    var settings = {
        "api_url": $("#api_url").val(),
        "model_header": $("#model_header").val(),
        "model_names": $("#model_names").val(),
        "interval": $("#interval").val()
    };
    
    // SetPluginSettings is a built-in FPP function
    SetPluginSettings("<?php echo $pluginName; ?>", settings);
    $.jGrowl("Santa List Settings Saved!", {theme: 'success'});
}

function TestConnection() {
    var url = $("#api_url").val();
    if(!url) { 
        alert("Please enter a WordPress API URL first."); 
        return; 
    }

    $("#test_status").html("<span style='color:white;'>Attempting to contact the North Pole...</span>");
    $("#preview_area").show();
    
    // Reset Screens
    $("#v_screen1, #v_screen2").css({"color": "#555", "text-shadow": "none"}).text("...");

    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        timeout: 8000,
        success: function(data) {
            $("#test_status").html("✅ Connection Successful! Found " + (data.nice.length + data.naughty.length) + " names.");
            
            // Logic to simulate the matrix display
            if(data.nice && data.nice.length > 0) {
                // Show most recent Nice name
                $("#v_screen1").text("NICE").css({"color": "#00ff00", "text-shadow": "0 0 10px #00ff00"});
                $("#v_screen2").text(data.nice[0].toUpperCase()).css({"color": "#00ff00", "text-shadow": "0 0 10px #00ff00"});
            } else if(data.naughty && data.naughty.length > 0) {
                // Show most recent Naughty name
                $("#v_screen1").text("NAUGHTY").css({"color": "#ff0000", "text-shadow": "0 0 10px #ff0000"});
                $("#v_screen2").text(data.naughty[0].toUpperCase()).css({"color": "#cccccc", "text-shadow": "0 0 5px #ffffff"});
            } else {
                $("#v_screen1").text("EMPTY");
                $("#v_screen2").text("NO NAMES");
            }
        },
        error: function(xhr, status, error) {
            $("#test_status").html("<span style='color:#ff4444;'>❌ Connection Failed: " + error + "</span>");
            $("#v_screen1").text("ERR").css("color", "red");
            $("#v_screen2").text("RETRY").css("color", "red");
        }
    });
}
</script>

<style>
/* FPP styling override for the table labels */
.settingLabel {
    width: 25%;
    padding: 10px;
}
</style>
