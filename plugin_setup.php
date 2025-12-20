<?php
include_once 'common.php';
$pluginName = "fpp-santa-list";

// Helper to get settings with defaults
function getSetting($key, $default) {
    global $pluginSettings;
    return isset($pluginSettings[$key]) ? $pluginSettings[$key] : $default;
}
?>
<div id="santa_list" class="settings">
    <fieldset>
        <legend>Santa's List Configuration</legend>
        <p>WordPress API URL: <input type="text" id="wp_url" size="50" value="<?php echo getSetting('wp_url', ''); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'wp_url', this.value);"></p>
        <p>API Sync Interval (Seconds): <input type="number" id="sync_interval" value="<?php echo getSetting('sync_interval', '60'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'sync_interval', this.value);"></p>
        <p>List Flip Speed (Seconds): <input type="number" id="flip_speed" value="<?php echo getSetting('flip_speed', '10'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'flip_speed', this.value);"></p>
        <p>Names to Show: <input type="number" id="name_limit" value="<?php echo getSetting('name_limit', '6'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'name_limit', this.value);"></p>
        <hr>
        <h3>Matrix Models</h3>
        <p>Top Model (Header): <input type="text" id="header_model" value="<?php echo getSetting('header_model', 'Matrix_Header'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'header_model', this.value);"></p>
        <p>Bottom Model (Names): <input type="text" id="names_model" value="<?php echo getSetting('names_model', 'Matrix_Names'); ?>" onchange="SetPluginSetting('<?php echo $pluginName; ?>', 'names_model', this.value);"></p>
    </fieldset>
    <br>
    <button class="buttons" onclick="StartSantaService();">Start/Restart Santa Service</button>
</div>

<script>
function StartSantaService() {
    $.get('plugin.php?plugin=<?php echo $pluginName; ?>&page=scripts/start_service.php&nopage=1', function(data) {
        $.jGrowl("Santa Service Started!");
    });
}
</script>
