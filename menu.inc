<?php
// This generates the correct URL to load plugin_setup.php inside the FPP interface
$pluginPage = preg_replace('/.*\/plugins\/(.*)/', 'plugin.php?plugin=$1&page=plugin_setup.php', dirname(__FILE__));
?>

<li><a href="<?php echo $pluginPage; ?>">Santa's List</a></li>
