<?php
include_once 'common.php';
$pluginName = "fpp-santa-list";
// Fetch saved settings
$wp_url = isset($pluginSettings['wp_url']) ? $pluginSettings['wp_url'] : "";
?>

<div id="santa_list" class="settings">
    <div style="display: flex; gap: 20px;">
        <div style="flex: 1;">
            <fieldset>
                <legend>Settings</legend>
                <p>WP API URL: <input type="text" id="wp_url" size="40" value="<?php echo $wp_url; ?>" onchange="SetPluginSetting('fpp-santa-list', 'wp_url', this.value);"></p>
                <button class="buttons" onclick="TestAPI();">🔍 Test API Connection</button>
                <button class="buttons" onclick="StartSantaService();">🚀 Start Service</button>
            </fieldset>

            <fieldset style="margin-top:15px;">
                <legend>Live API Data</legend>
                <pre id="api_debug" style="background:#000; color:#0f0; padding:10px; height:150px; overflow:auto; font-size:11px;">Click "Test API" to pull data...</pre>
            </fieldset>
        </div>

        <div style="flex: 1;">
            <fieldset>
                <legend>Virtual Prop Preview</legend>
                <div id="virtual_prop" style="background:#222; padding:20px; border-radius:10px; text-align:center; border: 5px solid #333;">
                    <div id="v_header" style="background:#000; width:200px; height:40px; margin:0 auto 10px; border:1px solid #444; color:#0f0; display:flex; align-items:center; justify-content:center; font-family:Arial; font-weight:bold;">
                        OFF
                    </div>
                    <div id="v_names" style="background:#000; width:200px; height:120px; margin:0 auto; border:1px solid #444; color:#fff; padding:5px; font-family:monospace; font-size:12px; text-align:left; white-space:pre;">
                        (Waiting for data)
                    </div>
                </div>
                <p style="font-size:10px; color:#666; text-align:center;">Note: This is a simulation of your Pixel Overlay Models.</p>
            </fieldset>
        </div>
    </div>
</div>

<script>
function TestAPI() {
    var url = $('#wp_url').val();
    $('#api_debug').text('Pinging Santa...');
    
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            $('#api_debug').text(JSON.stringify(data, null, 4));
            UpdatePreview(data);
        },
        error: function() {
            $('#api_debug').text('ERROR: Could not reach WordPress API. Check your URL or Public settings.');
        }
    });
}

function UpdatePreview(data) {
    // Logic to simulate what the worker script does
    let type = (Math.random() > 0.5) ? 'nice' : 'naughty'; // Randomly pick one to show for the test
    let color = (type === 'nice') ? '#00FF00' : '#FF0000';
    let names = data[type].slice(0, 6).join('\n');

    $('#v_header').text(type.toUpperCase() + ' LIST').css('color', color);
    $('#v_names').text(names);
}

function StartSantaService() {
    $.get('plugin.php?plugin=fpp-santa-list&page=scripts/start_service.php&nopage=1', function() {
        $.jGrowl("Santa Service Started!");
    });
}
</script>
