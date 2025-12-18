#!/bin/bash

# 1. Stop the Background Process
# This kills the worker immediately
sudo pkill -f santa_worker.py

# 2. Clear the Matrix Screens
# We get the model names from the config file if possible, 
# otherwise we default to Screen1/Screen2 to ensure they go blank.
/opt/fpp/bin/fppmm -m Screen1 -o off
/opt/fpp/bin/fppmm -m Screen2 -o off

# 3. Cleanup Files
# Remove the settings file and the startup hook log if it exists
[ -f "/home/fpp/media/config/plugin.fpp-santa-list.json" ] && rm "/home/fpp/media/config/plugin.fpp-santa-list.json"

echo "Santa List: Process killed, screens cleared, and settings removed."