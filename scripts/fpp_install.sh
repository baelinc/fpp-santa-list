#!/bin/bash

# Navigate to the plugin directory
cd "$(dirname "$0")"/..

# Make scripts executable
chmod +x scripts/start_service.php
chmod +x fpp_santa_worker.php

# Add to crontab so it starts automatically on every reboot
# (Checks if the entry already exists first so we don't double up)
CRON_ENTRY="@reboot /usr/bin/php /home/fpp/media/plugins/fpp-santa-list/fpp_santa_worker.php > /dev/null 2>&1 &"
(crontab -l | grep -Fq "$CRON_ENTRY") || (crontab -l; echo "$CRON_ENTRY") | crontab -

echo "Santa List Plugin installation complete."
