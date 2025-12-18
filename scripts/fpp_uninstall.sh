#!/bin/bash
echo "Stopping Santa Worker..."
sudo pkill -f santa_worker.py

echo "Cleaning up..."
rm -f /home/fpp/media/config/plugin.fpp-santa-list.json

echo "Uninstall complete."
