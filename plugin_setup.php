<div id="santa_settings" class="settings">
    <fieldset>
        <legend>Santa Matrix Settings</legend>
        <p>WordPress API URL: <input type='text' id='wp_url' size='40' value='<?php echo $pluginSettings['wp_url']; ?>'></p>
        <hr>
        <h3>Top Screen (Header)</h3>
        <p>Model Name: <input type='text' id='header_model' value='Matrix_Header'></p>
        <p>Font Size: <input type='number' id='header_font' value='18'></p>
        <p>Nice Color: <input type='color' id='nice_color' value='#00FF00'></p>
        <p>Naughty Color: <input type='color' id='naughty_color' value='#FF0000'></p>
        <hr>
        <h3>Bottom Screen (Names)</h3>
        <p>Model Name: <input type='text' id='names_model' value='Matrix_Names'></p>
        <p>Font Size: <input type='number' id='names_font' value='12'></p>
        <p>Text Color: <input type='color' id='text_color' value='#FFFFFF'></p>
        <p>Names to Show: <input type='number' id='name_limit' value='6'></p>
    </fieldset>
</div>
