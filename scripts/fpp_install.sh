#!/bin/bash
# Get current directory
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="$(dirname "$DIR")"

echo "Setting executable permissions..."
chmod +x "${DIR}/fpp_uninstall.sh"
chmod +x "${DIR}/fpp_start.sh"
chmod +x "${PLUGIN_DIR}/santa_worker.py"

echo "Installing Python dependencies..."
sudo apt-get update
sudo apt-get install -y python3-requests

echo "Installation complete."
