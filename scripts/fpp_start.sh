#!/bin/bash
# Kill old instance
sudo pkill -f santa_worker.py

# Start the worker in the background
# Redirecting output to a log file so you can debug in Web Shell
/usr/bin/python3 /home/fpp/media/plugins/fpp-santa-list/santa_worker.py > /home/fpp/media/logs/santa_worker.log 2>&1 &
