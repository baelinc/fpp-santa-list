#!/bin/bash

echo "Stopping Santa List background worker..."
sudo pkill -f santa_worker.py

echo "Clearing LED Matrix screens..."
/opt/fpp/bin/fppmm -m Screen1 -o off
/opt/fpp/bin/fppmm -m Screen2 -o off

echo "Removing configuration files..."
rm -f /home/fpp/media/config/plugin.fpp-santa-list.json

echo "Santa List Plugin uninstalled successfully."
