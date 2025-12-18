<?php
$pluginName = "fpp-santa-list";
$pluginSettings = $settings['pluginSettings'];

$api_url = isset($pluginSettings['api_url']) ? $pluginSettings['api_url'] : '';
$model_header = isset($pluginSettings['model_header']) ? $pluginSettings['model_header'] : 'Screen1';
$model_names = isset($pluginSettings['model_names']) ? $pluginSettings['model_names'] : 'Screen2';
$interval = isset($pluginSettings['interval']) ? $pluginSettings['interval'] : '10';
?>

<div id="santa_list" class="settings">
    <fieldset>
        <legend>🎅 Santa's List FPP Settings</legend>
        
        <table class="table">
            <tr>
                <td><b>WordPress API URL:</b></td>
                <td><input type="text" id="api_url" size="64" value="<?php echo $api_url; ?>" placeholder="https://yoursite.com/wp-json/santa/v1/list"></td>
            </tr>
            <tr>
                <td><b>Screen 1 (Header):</b></td>
                <td><input type="text" id="model_header" value="<?php echo $model_header; ?>"></td>
            </tr>
            <tr>
                <td><b>Screen 2 (Names):</b></td>
                <td><input type="text" id="model_names" value="<?php echo $model_names; ?>"></td>
            </tr>
        </table>
        
        <div style="margin-top:10px;">
            <button type="button" class="buttons" onclick="SaveSantaSettings();">Save Settings</button>
            <button type="button" class="buttons" style="background:#165b33; color:white;" onclick="TestConnection();">⚡ Test Connection & Preview</button>
        </div>
    </fieldset>

    <div id="preview_area" style="display:none; margin-top:30px; padding:20px; background:#222; border-radius:10px;">
        <h3 style="color:white; margin-top:0;">🖥️ LED Matrix Live Preview</h3>
        <div style="display:flex; gap:20px; justify-content:center; align-items:center;">
            
            <div style="text-align:center;">
                <small style="color:#aaa;">Screen 1 (Header)</small>
                <div id="v_screen1" style="width:200px; height:100px; background:black; border:3px solid #444; display:flex; justify-content:center; align-items:center; color:red; font-family:'Courier New', monospace; font-size:24px; font-weight:bold; text-shadow: 0 0 5px red;">
                    OFF
                </div>
            </div>

            <div style="text-align:center;">
                <small style="color:#aaa;">Screen 2 (Name)</small>
                <div id="v_screen2" style="width:300px; height:100px; background:black; border:3px solid #444; display:flex; justify-content:center; align-items:center; color:#00ff00; font-family:'Courier New', monospace; font-size:24px; font-weight:bold; text-shadow: 0 0 5px #00ff00;">
                    OFF
                </div>
            </div>

        </div>
        <div id="test_status" style="margin-top:15px; color:#00ff00; font-family:monospace; text-align:center;"></div>
    </div>
</div>

<script>
function SaveSantaSettings() {
    var settings = {
        "api_url": $("#api_url").val(),
        "model_header": $("#model_header").val(),
        "model_names": $("#model_names").val(),
        "interval": $("#interval").val()
    };
    SetPluginSettings("<?php echo $pluginName; ?>", settings);
    alert("Settings saved!");
}

function TestConnection() {
    var url = $("#api_url").val();
    if(!url) { alert("Please enter an API URL first."); return; }

    $("#test_status").html("Connecting to North Pole...");
    $("#preview_area").fadeIn();

    // Use AJAX to call your WordPress API directly from the browser
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        timeout: 5000,
        success: function(data) {
            $("#test_status").html("✅ Connection Successful! Data received.");
            
            // Logic to pick the display name
            let headerText = "WAITING";
            let nameText = "---";

            if(data.nice && data.nice.length > 0) {
                headerText = "NICE";
                nameText = data.nice[0].toUpperCase();
                $("#v_screen1").css("color", "#f8b229").css("text-shadow", "0 0 8px #f8b229");
                $("#v_screen2").css("color", "#00ff00").css("text-shadow", "0 0 8px #00ff00");
            } else if(data.naughty && data.naughty.length > 0) {
                headerText = "NAUGHTY";
                nameText = data.naughty[0].toUpperCase();
                $("#v_screen1").css("color", "red").css("text-shadow", "0 0 8px red");
                $("#v_screen2").css("color", "#555").css("text-shadow", "none");
            }

            $("#v_screen1").html(headerText);
            $("#v_screen2").html(nameText);
        },
        error: function(xhr, status, error) {
            $("#test_status").html("<span style='color:red;'>❌ Connection Failed: " + error + "</span>");
            $("#v_screen1").html("ERR").css("color", "red");
            $("#v_screen2").html("RETRY").css("color", "red");
        }
    });
}
</script>