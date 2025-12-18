#!/bin/bash
sudo pkill -f santa_worker.py
# Turn off the matrix models
/opt/fpp/bin/fppmm -m Screen1 -o off
/opt/fpp/bin/fppmm -m Screen2 -o off
rm -f /home/fpp/media/config/plugin.fpp-santa-list.json
