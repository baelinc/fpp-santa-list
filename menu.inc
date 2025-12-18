<?php

// Check if the function exists to avoid errors on very old FPP versions
if (function_exists('addMenuItem')) {
    // This adds the link to the "Input/Output Setup" menu
    addMenuItem('Santa\'s List', 'plugin_setup.php', 'Input/Output Setup');
}

?>
