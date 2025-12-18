#!/bin/bash

# 1. Install Dependencies
sudo apt-get update
sudo apt-get install -y python3-requests

# 2. Set Permissions for all Plugin Components
# Get the directory of the current script
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Force permissions so the buttons work
chmod +x "${DIR}/fpp_uninstall.sh"
chmod +x "${DIR}/fpp_start.sh"
chmod +x "${DIR}/../santa_worker.py"

echo "Permissions updated for Santa List scripts."

# 3. Start the worker immediately so the user doesn't have to reboot
# We check if it's already running first to avoid double-processes
if ! pgrep -f "santa_worker.py" > /dev/null; then
    nohup python3 "${PLUGIN_DIR}/santa_worker.py" > /dev/null 2>&1 &
fi

echo "Santa List: Permissions set and background worker started."
