#!/bin/bash
# fpp-santa-list Uninstall Script

echo "Stopping Santa List background processes..."
sudo pkill -f santa_worker.py

echo "Removing FPP database configuration..."
/opt/fpp/bin/config set plugin.fpp-santa-list.api_url ""
/opt/fpp/bin/config set plugin.fpp-santa-list.model_header ""
/opt/fpp/bin/config set plugin.fpp-santa-list.model_names ""
/opt/fpp/bin/config set plugin.fpp-santa-list.interval ""

echo "Cleaning up matrix displays..."
/opt/fpp/bin/fppmm -m "$( /opt/fpp/bin/config get plugin.fpp-santa-list.model_header )" -o off
/opt/fpp/bin/fppmm -m "$( /opt/fpp/bin/config get plugin.fpp-santa-list.model_names )" -o off

echo "Uninstall complete."
