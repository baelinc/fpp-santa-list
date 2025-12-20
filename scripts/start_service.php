<?php
exec("pkill -f fpp_santa_worker.php");
exec("php /home/fpp/media/plugins/fpp-santa-list/fpp_santa_worker.php > /dev/null 2>&1 &");
?>
