#!/bin/bash

# Move to the plugin directory
cd "$(dirname "$0")"/..

# Make sure the worker script is executable
chmod +x fpp_santa_worker.php

# (Optional) You can add a cron job here or just run it manually
echo "Santa List Plugin installed successfully."
