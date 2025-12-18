#!/bin/bash
# Start the Santa List background worker
# We use the '&' to ensure it doesn't block FPP from starting up
python3 /home/fpp/media/plugins/fpp-santa-list/santa_worker.py &