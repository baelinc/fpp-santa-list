<?php
include_once 'common.php';
$pluginName = "fpp-santa-list";
// Fetch saved settings or use defaults
$wp_url = isset($pluginSettings['wp_url']) ? $pluginSettings['wp_url'] : "";
$name_limit = isset($pluginSettings['name_limit']) ? $pluginSettings['name_limit'] : "6";
$header_font = isset($pluginSettings['header_font']) ? $pluginSettings['header_font'] : "18";
$names_font = isset($pluginSettings['names_font']) ? $pluginSettings['names_font'] : "12";
?>
<div id="santa_list" class="settings">
    <fieldset>
        <legend>Santa's List Configuration</legend>
        <table cellspacing="5" cellpadding="5">
            <tr><td>WordPress API URL:</td><td><input type="text" id="wp_url" size="60" value="<?php echo $wp_url; ?>"></td></tr>
            <tr><td>Names to Show:</td><td><input type="number" id="name_limit" value="<?php echo $name_limit; ?>"></td></tr>
        </table>
        <hr>
        <h3>Matrix Overlay Models</h3>
        <table cellspacing="5" cellpadding="5">
            <tr><td>Header Model (Top):</td><td><input type="text" id="header_model" value="Matrix_Header"></td></tr>
            <tr><td>Names Model (Bottom):</td><td><input type="text" id="names_model" value="Matrix_Names"></td></tr>
        </table>
        <hr>
        <h3>Appearance</h3>
        <table cellspacing="5" cellpadding="5">
            <tr><td>Header Font Size:</td><td><input type="number" id="header_font" value="<?php echo $header_font; ?>"></td></tr>
            <tr><td>Names Font Size:</td><td><input type="number" id="names_font" value="<?php echo $names_font; ?>"></td></tr>
            <tr><td>Nice List Color:</td><td><input type="color" id="nice_color" value="#00FF00"></td></tr>
            <tr><td>Naughty List Color:</td><td><input type="color" id="naughty_color" value="#FF0000"></td></tr>
        </table>
    </fieldset>
</div>
