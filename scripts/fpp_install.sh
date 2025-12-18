#!/bin/bash

# 1. Install Dependencies
sudo apt-get update
sudo apt-get install -y python3-requests

# 2. Set Permissions for all Plugin Components
# We target the specific scripts to ensure they can be executed by FPP
PLUGIN_DIR="/home/fpp/media/plugins/fpp-santa-list"

chmod +x "${PLUGIN_DIR}/santa_worker.py"
chmod +x "${PLUGIN_DIR}/scripts/fpp_start.sh"
chmod +x "${PLUGIN_DIR}/scripts/uninstall.sh"

# 3. Start the worker immediately so the user doesn't have to reboot
# We check if it's already running first to avoid double-processes
if ! pgrep -f "santa_worker.py" > /dev/null; then
    nohup python3 "${PLUGIN_DIR}/santa_worker.py" > /dev/null 2>&1 &
fi

echo "Santa List: Permissions set and background worker started."
