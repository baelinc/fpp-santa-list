<?php
include_once 'common.php';
$pluginName = "fpp-santa-list";

// Helper to get settings with defaults
function getS($key, $default) {
    global $pluginSettings;
    return isset($pluginSettings[$key]) ? $pluginSettings[$key] : $default;
}
?>

<div id="santa_list" class="settings">
    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 400px;">
            <fieldset>
                <legend>🎅 Santa's Workshop Settings</legend>
                <table cellspacing="5" cellpadding="5" style="width:100%;">
                    <tr>
                        <td>WP API URL:</td>
                        <td><input type="text" id="wp_url" style="width:100%;" value="<?php echo getS('wp_url', ''); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'wp_url', this.value);"></td>
                    </tr>
                    <tr>
                        <td>Sync Every (Sec):</td>
                        <td><input type="number" id="sync_interval" value="<?php echo getS('sync_interval', '60'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'sync_interval', this.value);"></td>
                    </tr>
                    <tr>
                        <td>Flip Speed (Sec):</td>
                        <td><input type="number" id="flip_speed" value="<?php echo getS('flip_speed', '10'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'flip_speed', this.value);"></td>
                    </tr>
                    <tr>
                        <td>Name Limit:</td>
                        <td><input type="number" id="name_limit" value="<?php echo getS('name_limit', '6'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'name_limit', this.value);"></td>
                    </tr>
                </table>
            </fieldset>

            <fieldset style="margin-top:15px;">
                <legend>🎨 Appearance & Models</legend>
                <table cellspacing="5" cellpadding="5" style="width:100%;">
                    <tr>
                        <td>Top Model:</td>
                        <td><input type="text" id="header_model" value="<?php echo getS('header_model', 'Matrix_Header'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'header_model', this.value);"></td>
                    </tr>
                    <tr>
                        <td>Bottom Model:</td>
                        <td><input type="text" id="names_model" value="<?php echo getS('names_model', 'Matrix_Names'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'names_model', this.value);"></td>
                    </tr>
                    <tr>
                        <td>Header Font Size:</td>
                        <td><input type="number" id="header_font" value="<?php echo getS('header_font', '18'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'header_font', this.value);"></td>
                    </tr>
                    <tr>
                        <td>Names Font Size:</td>
                        <td><input type="number" id="names_font" value="<?php echo getS('names_font', '12'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'names_font', this.value);"></td>
                    </tr>
                    <tr>
                        <td>Nice Color:</td>
                        <td><input type="color" id="nice_color" value="<?php echo getS('nice_color', '#00FF00'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'nice_color', this.value);"></td>
                    </tr>
                    <tr>
                        <td>Naughty Color:</td>
                        <td><input type="color" id="naughty_color" value="<?php echo getS('naughty_color', '#FF0000'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'naughty_color', this.value);"></td>
                    </tr>
                    <tr>
                        <td>Names Text Color:</td>
                        <td><input type="color" id="text_color" value="<?php echo getS('text_color', '#FFFFFF'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'text_color', this.value);"></td>
                    </tr>
                </table>
            </fieldset>

            <div style="margin-top:20px;">
                <button class="buttons" onclick="TestAPI();">🔍 Test API Connection</button>
                <button class="buttons btn-success" onclick="StartSantaService();">🚀 Start Service</button>
            </div>
        </div>

        <div style="flex: 1; min-width: 350px;">
            <fieldset>
                <legend>🖼️ Virtual Prop Preview</legend>
                <div id="virtual_prop" style="background:#111; padding:30px; border-radius:10px; text-align:center; border: 8px solid #222;">
                    <div id="v_header" style="background:#000; width:220px; height:45px; margin:0 auto 15px; border:2px solid #333; color:#0f0; display:flex; align-items:center; justify-content:center; font-family:Arial Black, Gadget, sans-serif; font-size:16px; text-transform:uppercase;">
                        WAITING
                    </div>
                    <div id="v_names" style="background:#000; width:220px; height:140px; margin:0 auto; border:2px solid #333; color:#fff; padding:10px; font-family: 'Courier New', Courier, monospace; font-size:14px; text-align:left; white-space:pre; line-height:1.2;">
(Test API to preview)
                    </div>
                </div>
            </fieldset>

            <fieldset style="margin-top:15px;">
                <legend>📡 API Console Output</legend>
                <pre id="api_debug" style="background:#000; color:#0f0; padding:10px; height:120px; overflow:auto; font-size:11px; border:1px solid #333;">Raw data will appear here...</pre>
            </fieldset>
        </div>
    </div>
</div>

<script>
function TestAPI() {
    var url = $('#wp_url').val();
    if(!url) { alert('Please enter your WordPress API URL first!'); return; }
    
    $('#api_debug').text('FPP is contacting the North Pole... (Server-side proxy)');
    
    // We call a small PHP script on FPP instead of calling your website directly
    $.ajax({
        url: 'plugin.php?plugin=<?php echo $pluginName; ?>&page=scripts/test_proxy.php&nopage=1&test_url=' + encodeURIComponent(url),
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#api_debug').text(JSON.stringify(data, null, 4));
            UpdatePreview(data);
        },
        error: function(xhr) {
            $('#api_debug').text('ERROR: FPP could not reach your website.\nThis usually means the URL is wrong or your FPP has no internet.');
        }
    });
}

function UpdatePreview(data) {
    let types = ['nice', 'naughty'];
    let current = 0;
    
    function toggle() {
        let type = types[current];
        let h_color = (type === 'nice') ? $('#nice_color').val() : $('#naughty_color').val();
        let n_color = $('#text_color').val();
        let h_size = $('#header_font').val() + "px";
        let n_size = $('#names_font').val() + "px";
        let limit = parseInt($('#name_limit').val());
        let names = data[type].slice(0, limit).join('\n');
        
        $('#v_header').text(type + ' list').css({'color': h_color, 'font-size': h_size});
        $('#v_names').text(names ? names : '(No names found)').css({'color': n_color, 'font-size': n_size});
        
        current = (current + 1) % 2;
    }
    
    toggle();
    if(window.previewInterval) clearInterval(window.previewInterval);
    window.previewInterval = setInterval(toggle, 3000);
}

function StartSantaService() {
    $.get('plugin.php?plugin=<?php echo $pluginName; ?>&page=scripts/start_service.php&nopage=1', function() {
        $.jGrowl("Santa Worker Process Started!", { theme: 'success' });
    });
}
</script>

