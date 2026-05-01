<?php
include_once 'common.php';
$pluginName = "fpp-santa-list";

function getS($key, $default) {
    global $pluginSettings;
    return isset($pluginSettings[$key]) ? $pluginSettings[$key] : $default;
}
?>

<div id="santa_list" class="settings">
    <div style="display: flex; gap: 20px; flex-wrap: wrap;">

        <!-- ── Left column: settings ───────────────────────────────────── -->
        <div style="flex: 1; min-width: 450px;">

            <fieldset>
                <legend>🎅 Santa's Workshop Settings</legend>
                <table cellspacing="5" cellpadding="5" style="width:100%;">
                    <tr>
                        <td>WP API URL:</td>
                        <td><input type="text" id="wp_url" style="width:100%;"
                            value="<?php echo getS('wp_url', ''); ?>"
                            placeholder="https://your-site.com/wp-json/sld/v1/names"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'wp_url', this.value);">
                        </td>
                    </tr>
                    <tr>
                        <td>API Token:</td>
                        <td>
                            <input type="text" id="api_token" style="width:100%; font-family:monospace;"
                                value="<?php echo getS('api_token', ''); ?>"
                                placeholder="Paste token from WordPress → Santa's List → API &amp; Security"
                                onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'api_token', this.value);">
                            <small style="color:#aaa;">Paste exactly as shown in WordPress — no spaces</small>
                        </td>
                    </tr>
                    <tr>
                        <td>Sync Every (Sec):</td>
                        <td><input type="number" id="sync_interval"
                            value="<?php echo getS('sync_interval', '60'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'sync_interval', this.value);">
                        </td>
                    </tr>
                    <tr>
                        <td>Flip Speed (Sec):</td>
                        <td><input type="number" id="flip_speed"
                            value="<?php echo getS('flip_speed', '10'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'flip_speed', this.value);">
                        </td>
                    </tr>
                    <tr>
                        <td>Name Limit:</td>
                        <td><input type="number" id="name_limit"
                            value="<?php echo getS('name_limit', '6'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'name_limit', this.value);">
                        </td>
                    </tr>
                </table>
            </fieldset>

            <fieldset style="margin-top:15px;">
                <legend>📐 Matrix Dimensions (Pixels)</legend>
                <table cellspacing="5" cellpadding="5" style="width:100%;">
                    <tr style="border-bottom: 1px solid #444;"><td colspan="2"><b>Header (Title) Panel — not rotated</b></td></tr>
                    <tr>
                        <td>Width:</td>
                        <td><input type="number" id="h_width"
                            value="<?php echo getS('h_width', '64'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'h_width', this.value); UpdatePreviewLayout();">
                        </td>
                    </tr>
                    <tr>
                        <td>Height:</td>
                        <td><input type="number" id="h_height"
                            value="<?php echo getS('h_height', '32'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'h_height', this.value); UpdatePreviewLayout();">
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #444;"><td colspan="2"><b>Names Panel — rotated via FPP matrix arrows</b></td></tr>
                    <tr>
                        <td>Width:</td>
                        <td><input type="number" id="n_width"
                            value="<?php echo getS('n_width', '96'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'n_width', this.value); UpdatePreviewLayout();">
                        </td>
                    </tr>
                    <tr>
                        <td>Height:</td>
                        <td><input type="number" id="n_height"
                            value="<?php echo getS('n_height', '128'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'n_height', this.value); UpdatePreviewLayout();">
                        </td>
                    </tr>
                </table>
            </fieldset>

            <fieldset style="margin-top:15px;">
                <legend>🎨 Appearance &amp; Alignment</legend>
                <table cellspacing="5" cellpadding="5" style="width:100%;">
                    <tr>
                        <td>Nice List Text:</td>
                        <td><input type="text" id="nice_text"
                            value="<?php echo getS('nice_text', 'NICE LIST'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'nice_text', this.value);">
                        </td>
                    </tr>
                    <tr>
                        <td>Naughty List Text:</td>
                        <td><input type="text" id="naughty_text"
                            value="<?php echo getS('naughty_text', 'NAUGHTY LIST'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'naughty_text', this.value);">
                        </td>
                    </tr>
                    <tr>
                        <td>Text Alignment:</td>
                        <td>
                            <select id="text_align" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'text_align', this.value); UpdatePreviewLayout();">
                                <option value="Center" <?php echo (getS('text_align', 'Center') == 'Center') ? 'selected' : ''; ?>>Center</option>
                                <option value="Left"   <?php echo (getS('text_align', 'Center') == 'Left')   ? 'selected' : ''; ?>>Left</option>
                                <option value="Right"  <?php echo (getS('text_align', 'Center') == 'Right')  ? 'selected' : ''; ?>>Right</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Name Font Styles:</td>
                        <td>
                            <input type="checkbox" id="font_bold"
                                <?php echo (getS('font_bold', '0') == '1') ? 'checked' : ''; ?>
                                onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'font_bold', this.checked ? '1' : '0'); UpdatePreviewLayout();"> <b>Bold</b>&nbsp;&nbsp;
                            <input type="checkbox" id="font_italic"
                                <?php echo (getS('font_italic', '0') == '1') ? 'checked' : ''; ?>
                                onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'font_italic', this.checked ? '1' : '0'); UpdatePreviewLayout();"> <i>Italic</i>
                        </td>
                    </tr>
                    <tr>
                        <td>Rainbow Names:</td>
                        <td>
                            <input type="checkbox" id="rainbow_names"
                                <?php echo (getS('rainbow_names', '0') == '1') ? 'checked' : ''; ?>
                                onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'rainbow_names', this.checked ? '1' : '0');"> 🌈 Animated Rainbow
                        </td>
                    </tr>
                    <tr>
                        <td>Header Model (Matrix):</td>
                        <td>
                            <select id="header_model" style="width: 150px;"
                                onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'header_model', this.value);">
                                <option value="<?php echo getS('header_model', 'Title'); ?>"><?php echo getS('header_model', 'Title'); ?></option>
                            </select>
                            <button class="buttons btn-mini" title="Refresh Models" onclick="LoadFPPModels();">🔄</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Names Model (Matrix):</td>
                        <td>
                            <select id="names_model" style="width: 150px;"
                                onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'names_model', this.value);">
                                <option value="<?php echo getS('names_model', 'Names'); ?>"><?php echo getS('names_model', 'Names'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Header Font Size:</td>
                        <td><input type="number" id="header_font"
                            value="<?php echo getS('header_font', '18'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'header_font', this.value);">
                        </td>
                    </tr>
                    <tr>
                        <td>Names Font Size:</td>
                        <td><input type="number" id="names_font"
                            value="<?php echo getS('names_font', '12'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'names_font', this.value);">
                        </td>
                    </tr>
                    <tr>
                        <td>Nice Color:</td>
                        <td><input type="color" id="nice_color"
                            value="<?php echo getS('nice_color', '#00FF00'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'nice_color', this.value);">
                        </td>
                    </tr>
                    <tr>
                        <td>Naughty Color:</td>
                        <td><input type="color" id="naughty_color"
                            value="<?php echo getS('naughty_color', '#FF0000'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'naughty_color', this.value);">
                        </td>
                    </tr>
                    <tr>
                        <td>Names Text Color:</td>
                        <td><input type="color" id="text_color"
                            value="<?php echo getS('text_color', '#FFFFFF'); ?>"
                            onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'text_color', this.value);">
                        </td>
                    </tr>
                </table>
            </fieldset>

            <div style="margin-top:20px; display: flex; align-items: center; gap: 10px;">
                <button class="buttons btn-success" onclick="StartSantaService();">🚀 Start Service</button>
                <button class="buttons btn-danger"  onclick="StopSantaService();">🛑 Stop Service</button>
                <div id="service_status" style="padding: 5px 15px; border-radius: 20px; background: #555; color: #fff; font-weight: bold; min-width: 120px; text-align: center;">Checking...</div>
            </div>
            <div style="margin-top:10px; display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="buttons" onclick="TestAPI();">🔍 Test API</button>
                <button class="buttons" onclick="TestMode();">🌈 Test Preview</button>
                <button class="buttons btn-danger" onclick="ClearPanels();">🧹 Clear Matrix</button>
                <button class="buttons" onclick="ViewLog();">📄 View Log</button>
            </div>
        </div>

        <!-- ── Right column: preview + debug ───────────────────────────── -->
        <div style="flex: 1; min-width: 350px;">
            <fieldset>
                <legend>🖼️ Scale-Accurate Preview</legend>
                <div id="preview_outer" style="background:#1a1a2e; padding:20px; border-radius:10px; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:400px; border: 3px solid #444; gap: 12px;">
                    <!-- Header panel: landscape 64×32 -->
                    <div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                        <small style="color:#666;font-size:10px;">HEADER PANEL (64×32)</small>
                        <div id="v_header" style="background:#000; border:2px solid #c41e1e; display:flex; align-items:center; justify-content:center; overflow:hidden; font-family:Arial Black, sans-serif; text-transform:uppercase; color:#00ff00; font-size:14px;">NICE LIST</div>
                    </div>
                    <!-- Names panel: portrait after rotation -->
                    <div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                        <small style="color:#666;font-size:10px;">NAMES PANEL (96×128)</small>
                        <div id="v_names" style="background:#000; border:2px solid #333; overflow:hidden; font-family: 'Courier New', monospace; white-space:pre; padding:4px; color:#fff; font-size:11px;">(Test API to see names)</div>
                    </div>
                </div>
                <p style="font-size:10px; color:#888; text-align:center; margin-top:8px;">Preview reflects panel dimensions. Toggles every 5 seconds.</p>
            </fieldset>

            <fieldset style="margin-top:15px;">
                <legend>📡 API Console Output</legend>
                <pre id="api_debug" style="background:#000; color:#0f0; padding:10px; height:140px; overflow:auto; font-size:11px; border:1px solid #333; white-space:pre-wrap;">Waiting for Test API click...</pre>
            </fieldset>

            <fieldset style="margin-top:15px;">
                <legend>📄 Recent Log</legend>
                <pre id="log_output" style="background:#000; color:#aaa; padding:10px; height:120px; overflow:auto; font-size:11px; border:1px solid #333; white-space:pre-wrap;">Click "View Log" to load...</pre>
            </fieldset>
        </div>

    </div>
</div>

<script>
let rainbowHue = 0;
let rainbowInterval = null;

// ── Load FPP overlay models into dropdowns ────────────────────────────────
function LoadFPPModels() {
    $.ajax({
        url: '/api/overlays/models',
        type: 'GET',
        dataType: 'json',
        success: function(data) { populateDropdowns(data); },
        error: function() {
            $.getJSON('/api/overlay/models', function(data) {
                populateDropdowns(data);
            }).fail(function() {
                // Try the older /api/models path
                $.getJSON('/api/models', function(data) {
                    populateDropdowns(data);
                }).fail(function() {
                    $.jGrowl("Could not load FPP models. Are any Matrix models defined?", { theme: 'danger' });
                });
            });
        }
    });
}

function populateDropdowns(data) {
    let headerSelect  = $('#header_model');
    let namesSelect   = $('#names_model');
    let currentHeader = "<?php echo getS('header_model', 'Title'); ?>";
    let currentNames  = "<?php echo getS('names_model',  'Names'); ?>";

    headerSelect.empty();
    namesSelect.empty();

    let models = Array.isArray(data) ? data : (data.models || []);

    if (models.length > 0) {
        $.each(models, function(i, model) {
            let name = model.Name || model.name;
            if (name) {
                let w = model.Width || model.width || 0;
                let h = model.Height || model.height || 0;
                let label = name + (w || h ? ' (' + w + '×' + h + ')' : '');
                headerSelect.append($('<option>', { value: name, text: label, selected: (name === currentHeader) }));
                namesSelect.append($('<option>', { value: name, text: label, selected: (name === currentNames) }));
            }
        });
        $.jGrowl("Matrix models loaded (" + models.length + " found).", { theme: 'success' });
    } else {
        headerSelect.append(new Option(currentHeader, currentHeader));
        namesSelect.append(new Option(currentNames, currentNames));
        $.jGrowl("No models returned from FPP. Make sure LED Panel Matrix outputs are configured.", { theme: 'warn' });
    }
}

// ── Update preview panel sizes ────────────────────────────────────────────
function UpdatePreviewLayout() {
    let hw     = parseInt($('#h_width').val())  || 64;
    let hh     = parseInt($('#h_height').val()) || 32;
    let nw     = parseInt($('#n_width').val())  || 96;
    let nh     = parseInt($('#n_height').val()) || 128;
    let align  = $('#text_align').val().toLowerCase();
    let scale  = 2.5;

    let fontWeight = $('#font_bold').is(':checked')   ? 'bold'   : 'normal';
    let fontStyle  = $('#font_italic').is(':checked') ? 'italic' : 'normal';
    let flexAlign  = align === 'center' ? 'center' : (align === 'left' ? 'flex-start' : 'flex-end');

    $('#v_header').css({
        'width':           Math.round(hw * scale) + 'px',
        'height':          Math.round(hh * scale) + 'px',
        'justify-content': flexAlign,
        'text-align':      align,
    });

    $('#v_names').css({
        'width':       Math.round(nw * scale) + 'px',
        'height':      Math.round(nh * scale) + 'px',
        'text-align':  align,
        'font-weight': fontWeight,
        'font-style':  fontStyle,
    });
}

// ── Service controls ──────────────────────────────────────────────────────
function CheckServiceStatus() {
    $.ajax({
        url:      'plugin.php?plugin=<?php echo $pluginName; ?>&page=scripts/get_status.php&nopage=1',
        type:     'POST',
        dataType: 'json',
        success:  function(data) {
            let running = data && (data.running === true || data.running === 1 || data.running === "1");
            $('#service_status')
                .text(running ? 'RUNNING' : 'STOPPED')
                .css('background', running ? '#28a745' : '#dc3545');
        },
        error: function() { $('#service_status').text('UNKNOWN').css('background', '#555'); }
    });
}

function StartSantaService() {
    $.get('plugin.php?plugin=<?php echo $pluginName; ?>&page=scripts/start_service.php&nopage=1', function() {
        $.jGrowl("Santa Worker Started!", { theme: 'success' });
        setTimeout(CheckServiceStatus, 1500);
    });
}

function StopSantaService() {
    $.get('plugin.php?plugin=<?php echo $pluginName; ?>&page=scripts/stop_service.php&nopage=1', function() {
        $.jGrowl("Santa Worker Stopped.");
        setTimeout(CheckServiceStatus, 1000);
    });
}

function ClearPanels() {
    if (confirm("Stop the service and clear the matrix panels?")) {
        StopSantaService();
        let hm = $('#header_model').val();
        let nm = $('#names_model').val();
        $.ajax({ url: '/api/overlays/model/' + hm + '/state', type: 'PUT', data: JSON.stringify({"State": 0}) });
        $.ajax({ url: '/api/overlays/model/' + nm + '/state', type: 'PUT', data: JSON.stringify({"State": 0}) });
        $.jGrowl("Panels cleared.", { theme: 'info' });
    }
}

// ── Test API connection ───────────────────────────────────────────────────
function TestAPI() {
    let url   = $('#wp_url').val();
    let token = $('#api_token').val();
    if (!url) { alert('Enter your WordPress API URL first!'); return; }
    if (!token) { alert('Enter your API token first!'); return; }

    $('#api_debug').text('Contacting WordPress...');
    $.ajax({
        url: 'plugin.php?plugin=<?php echo $pluginName; ?>&page=scripts/test_proxy.php&nopage=1&test_url=' + encodeURIComponent(url) + '&test_token=' + encodeURIComponent(token),
        type:     'GET',
        dataType: 'json',
        success:  function(data) {
            $('#api_debug').text(JSON.stringify(data, null, 2));
            // Convert flat names array to nice/naughty split for preview
            let preview = { nice: [], naughty: [] };
            if (data.names) {
                data.names.forEach(function(n) {
                    if (n.list_type === 'nice')    preview.nice.push(n.display_name);
                    if (n.list_type === 'naughty') preview.naughty.push(n.display_name);
                });
            }
            UpdatePreview(preview);
        },
        error: function(xhr) {
            $('#api_debug').text('ERROR: Could not reach WordPress.\n' + xhr.responseText);
        }
    });
}

// ── Test mode with dummy data ─────────────────────────────────────────────
function TestMode() {
    let dummy = {
        nice:    ["Emma S.", "Liam T.", "Olivia R.", "Noah B.", "Ava M.", "Lucas W."],
        naughty: ["E. Scrooge", "The Grinch", "Hans G.", "Mr. Potter"]
    };
    UpdatePreview(dummy);
    $.jGrowl("Test Mode: Previewing dummy data.", { theme: 'info' });
}

// ── View log ──────────────────────────────────────────────────────────────
function ViewLog() {
    $.get('plugin.php?plugin=<?php echo $pluginName; ?>&page=scripts/get_log.php&nopage=1', function(data) {
        $('#log_output').text(data || '(Log is empty)');
    }).fail(function() {
        $('#log_output').text('Could not load log file.');
    });
}

// ── Preview updater ───────────────────────────────────────────────────────
function UpdatePreview(data) {
    let types   = ['nice', 'naughty'];
    let current = 0;

    function toggle() {
        UpdatePreviewLayout();
        let type      = types[current];
        let hText     = (type === 'nice') ? $('#nice_text').val()    : $('#naughty_text').val();
        let hColor    = (type === 'nice') ? $('#nice_color').val()   : $('#naughty_color').val();
        let hSize     = (parseInt($('#header_font').val()) * 1.2)    + "px";
        let nSize     = (parseInt($('#names_font').val())  * 1.2)    + "px";
        let limit     = parseInt($('#name_limit').val());
        let namesList = (data[type] || []).slice(0, limit);
        let namesText = namesList.join('\n') || '(No names)';

        $('#v_header').text(hText).css({ 'color': hColor, 'font-size': hSize });
        $('#v_names').text(namesText).css({ 'font-size': nSize });

        if ($('#rainbow_names').is(':checked')) {
            if (!rainbowInterval) {
                rainbowInterval = setInterval(function() {
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
    if (window.previewInterval) clearInterval(window.previewInterval);
    window.previewInterval = setInterval(toggle, 5000);
}

// ── Init ──────────────────────────────────────────────────────────────────
$(document).ready(function() {
    UpdatePreviewLayout();
    CheckServiceStatus();
    LoadFPPModels();
    setInterval(CheckServiceStatus, 5000);
});
</script>
