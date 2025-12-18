#!/bin/bash
# fpp-santa-list Startup Script

# 1. Define paths
PLUGIN_DIR="/home/fpp/media/plugins/fpp-santa-list"
LOG_FILE="/home/fpp/media/logs/santa_worker.log"

# 2. Ensure log file exists and is writable
touch "$LOG_FILE"
chmod 666 "$LOG_FILE"

echo "Starting Santa List Worker..." >> "$LOG_FILE"

# 3. Kill any existing instances of the worker to prevent duplicates
# We use pkill -f to match the full process name
sudo pkill -f santa_worker.py

# 4. Start the worker using the absolute path to python3
# We run it in the background (&) and redirect all output to the log
/usr/bin/python3 "${PLUGIN_DIR}/santa_worker.py" >> "$LOG_FILE" 2>&1 &

echo "Santa List Worker started in background." >> "$LOG_FILE"
