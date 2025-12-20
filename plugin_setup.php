<?php
include_once 'common.php';
$pluginName = "fpp-santa-list";

// Helper function to fetch saved settings from FPP's config file
function getS($key, $default) {
    global $pluginSettings;
    return isset($pluginSettings[$key]) ? $pluginSettings[$key] : $default;
}
?>

<div id="santa_list" class="settings">
    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 450px;">
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
                <legend>📐 Matrix Dimensions (Pixels)</legend>
                <table cellspacing="5" cellpadding="5" style="width:100%;">
                    <tr style="border-bottom: 1px solid #444;"><td colspan="2"><b>Top (Header) Panel</b></td></tr>
                    <tr>
                        <td>Width:</td>
                        <td><input type="number" id="h_width" value="<?php echo getS('h_width', '64'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'h_width', this.value); UpdatePreviewLayout();"></td>
                    </tr>
                    <tr>
                        <td>Height:</td>
                        <td><input type="number" id="h_height" value="<?php echo getS('h_height', '32'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'h_height', this.value); UpdatePreviewLayout();"></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #444;"><td colspan="2"><b>Bottom (Names) Panel</b></td></tr>
                    <tr>
                        <td>Width:</td>
                        <td><input type="number" id="n_width" value="<?php echo getS('n_width', '64'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'n_width', this.value); UpdatePreviewLayout();"></td>
                    </tr>
                    <tr>
                        <td>Height:</td>
                        <td><input type="number" id="n_height" value="<?php echo getS('n_height', '64'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'n_height', this.value); UpdatePreviewLayout();"></td>
                    </tr>
                </table>
            </fieldset>

            <fieldset style="margin-top:15px;">
                <legend>🎨 Appearance & Alignment</legend>
                <table cellspacing="5" cellpadding="5" style="width:100%;">
                    <tr>
                        <td>Nice List Text:</td>
                        <td><input type="text" id="nice_text" value="<?php echo getS('nice_text', 'NICE LIST'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'nice_text', this.value);"></td>
                    </tr>
                    <tr>
                        <td>Naughty List Text:</td>
                        <td><input type="text" id="naughty_text" value="<?php echo getS('naughty_text', 'NAUGHTY LIST'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'naughty_text', this.value);"></td>
                    </tr>
                    <tr>
                        <td>Text Alignment:</td>
                        <td>
                            <select id="text_align" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'text_align', this.value); UpdatePreviewLayout();">
                                <option value="Center" <?php echo (getS('text_align', 'Center') == 'Center') ? 'selected' : ''; ?>>Center</option>
                                <option value="Left" <?php echo (getS('text_align', 'Center') == 'Left') ? 'selected' : ''; ?>>Left</option>
                                <option value="Right" <?php echo (getS('text_align', 'Center') == 'Right') ? 'selected' : ''; ?>>Right</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Name Font Styles:</td>
                        <td>
                            <input type="checkbox" id="font_bold" <?php echo (getS('font_bold', '0') == '1') ? 'checked' : ''; ?> onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'font_bold', this.checked ? '1' : '0'); UpdatePreviewLayout();"> <b>Bold</b>&nbsp;&nbsp;
                            <input type="checkbox" id="font_italic" <?php echo (getS('font_italic', '0') == '1') ? 'checked' : ''; ?> onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'font_italic', this.checked ? '1' : '0'); UpdatePreviewLayout();"> <i>Italic</i>
                        </td>
                    </tr>
                    <tr>
                        <td>Rainbow Names:</td>
                        <td>
                            <input type="checkbox" id="rainbow_names" <?php echo (getS('rainbow_names', '0') == '1') ? 'checked' : ''; ?> onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'rainbow_names', this.checked ? '1' : '0');"> 🌈 Animated Rainbow
                        </td>
                    </tr>
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

            <div style="margin-top:20px; display: flex; align-items: center; gap: 10px;">
                <button class="buttons btn-success" onclick="StartSantaService();">🚀 Start Service</button>
                <button class="buttons btn-danger" onclick="StopSantaService();">🛑 Stop Service</button>
                <div id="service_status" style="padding: 5px 15px; border-radius: 20px; background: #555; color: #fff; font-weight: bold; min-width: 120px; text-align: center;">Checking...</div>
            </div>
            <div style="margin-top:10px; display: flex; gap: 10px;">
                <button class="buttons" onclick="TestAPI();">🔍 Test API Connection</button>
                <button class="buttons" onclick="TestMode();">🌈 Test Rainbow Preview</button>
                <button class="buttons btn-danger" onclick="ClearPanels();">🧹 Clear Matrix</button>
            </div>
        </div>

        <div style="flex: 1; min-width: 350px;">
            <fieldset>
                <legend>🖼️ Scale-Accurate Preview</legend>
                <div id="preview_outer" style="background:#222; padding:20px; border-radius:10px; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:400px; border: 5px solid #333;">
                    <div id="v_header" style="background:#000; border:1px solid #444; margin-bottom:15px; display:flex; align-items:center; overflow:hidden; font-family:Arial Black, sans-serif; text-transform:uppercase;">WAITING</div>
                    <div id="v_names" style="background:#000; border:1px solid #444; display:block; overflow:hidden; font-family: 'Courier New', monospace; white-space:pre; padding:2px;">(Test API)</div>
                </div>
                <p style="font-size:10px; color:#888; text-align:center; margin-top:10px;">Preview reflects dual pixel dimensions and alignment.</p>
            </fieldset>

            <fieldset style="margin-top:15px;">
                <legend>📡 API Console Output</legend>
                <pre id="api_debug" style="background:#000; color:#0f0; padding:10px; height:120px; overflow:auto; font-size:11px; border:1px solid #333;">Raw data will appear here...</pre>
            </fieldset>
        </div>
    </div>
</div>

<script>
// --- GLOBAL VARS FOR ANIMATION ---
let rainbowHue = 0;
let rainbowInterval = null;

// --- LAYOUT & PREVIEW LOGIC ---

function UpdatePreviewLayout() {
    let hw = parseInt($('#h_width').val()) || 64;
    let hh = parseInt($('#h_height').val()) || 32;
    let nw = parseInt($('#n_width').val()) || 64;
    let nh = parseInt($('#n_height').val()) || 64;
    let align = $('#text_align').val().toLowerCase();
    let scale = 3; 

    // Font Styling for Preview
    let fontWeight = $('#font_bold').is(':checked') ? 'bold' : 'normal';
    let fontStyle = $('#font_italic').is(':checked') ? 'italic' : 'normal';

    $('#v_header').css({
        'width': (hw * scale) + 'px',
        'height': (hh * scale) + 'px',
        'text-align': align
    });

    $('#v_names').css({
        'width': (nw * scale) + 'px',
        'height': (nh * scale) + 'px',
        'text-align': align,
        'font-weight': fontWeight,
        'font-style': fontStyle
    });

    let flexAlign = (align === 'center') ? 'center' : (align === 'left' ? 'flex-start' : 'flex-end');
    $('#v_header').css('justify-content', flexAlign);
}

// --- SERVICE MANAGEMENT ---

function CheckServiceStatus() {
    $.get('plugin.php?plugin=<?php echo $pluginName; ?>&page=scripts/get_status.php&nopage=1', function(data) {
        if(data.running) {
            $('#service_status').text('RUNNING').css('background', '#28a745');
        } else {
            $('#service_status').text('STOPPED').css('background', '#dc3545');
        }
    });
}

function StartSantaService() {
    $.get('plugin.php?plugin=<?php echo $pluginName; ?>&page=scripts/start_service.php&nopage=1', function() {
        $.jGrowl("Santa Worker Process Started!", { theme: 'success' });
        setTimeout(CheckServiceStatus, 1000);
    });
}

function StopSantaService() {
    $.get('plugin.php?plugin=<?php echo $pluginName; ?>&page=scripts/stop_service.php&nopage=1', function() {
        $.jGrowl("Santa Worker Stopped.");
        setTimeout(CheckServiceStatus, 1000);
    });
}

function ClearPanels() {
    // This calls fppmm directly via a helper to clear matrices
    $.jGrowl("Clearing Matrix Panels...");
    // You would typically have a small script for this, or just stop the service
    StopSantaService();
}

// --- API TESTING & PREVIEW ---

function TestAPI() {
    var url = $('#wp_url').val();
    if(!url) { alert('Please enter your WordPress API URL first!'); return; }
    $('#api_debug').text('FPP is contacting your website...');
    $.ajax({
        url: 'plugin.php?plugin=<?php echo $pluginName; ?>&page=scripts/test_proxy.php&nopage=1&test_url=' + encodeURIComponent(url),
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#api_debug').text(JSON.stringify(data, null, 4));
            UpdatePreview(data);
        },
        error: function(xhr) {
            $('#api_debug').text('ERROR: FPP could not reach your website.');
        }
    });
}

function TestMode() {
    let dummy = {
        "nice": ["Kris Kringle", "Buddy Elf", "Cindy Lou", "Tiny Tim"],
        "naughty": ["E. Scrooge", "The Grinch", "Hans Gruber"]
    };
    UpdatePreview(dummy);
    $.jGrowl("Test Mode Active: Previewing local data.", { theme: 'info' });
}

function UpdatePreview(data) {
    let types = ['nice', 'naughty'];
    let current = 0;
    
    function toggle() {
        UpdatePreviewLayout();
        let type = types[current];
        
        let h_text = (type === 'nice') ? $('#nice_text').val() : $('#naughty_text').val();
        let h_color = (type === 'nice') ? $('#nice_color').val() : $('#naughty_color').val();
        
        let h_size = (parseInt($('#header_font').val()) * 1.2) + "px";
        let n_size = (parseInt($('#names_font').val()) * 1.2) + "px";
        let limit = parseInt($('#name_limit').val());
        
        let namesList = data[type] ? data[type].slice(0, limit) : [];
        let names = namesList.join('\n');
        
        $('#v_header').text(h_text).css({'color': h_color, 'font-size': h_size});
        $('#v_names').text(names ? names : '(No names found)').css({'font-size': n_size});

        // Handle Rainbow Preview Logic
        if ($('#rainbow_names').is(':checked')) {
            if (!rainbowInterval) {
                rainbowInterval = setInterval(() => {
                    rainbowHue = (rainbowHue + 5) % 360;
                    $('#v_names').css('color', 'hsl(' + rainbowHue + ', 100%, 50%)');
                }, 50);
            }
        } else {
            clearInterval(rainbowInterval);
            rainbowInterval = null;
            $('#v_names').css('color', $('#text_color').val());
        }
        
        current = (current + 1) % 2;
    }
    
    toggle();
    if(window.previewInterval) clearInterval(window.previewInterval);
    window.previewInterval = setInterval(toggle, 5000);
}

$(document).ready(function() {
    UpdatePreviewLayout();
    CheckServiceStatus();
    setInterval(CheckServiceStatus, 5000);
});
</script>
