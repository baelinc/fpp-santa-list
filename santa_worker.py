#!/usr/bin/env python3
import requests
import time
import subprocess
import json
import os

SETTINGS_FILE = "/home/fpp/media/config/plugin.fpp-santa-list.json"

def get_settings():
    if os.path.exists(SETTINGS_FILE):
        with open(SETTINGS_FILE, 'r') as f:
            return json.load(f)
    return None

def update_fpp(model, text):
    # -m: model name, -o on: turn on overlay, -t: text
    # We don't use -s (scroll) so the text remains static
    subprocess.run(["/opt/fpp/bin/fppmm", "-m", model, "-o", "on"])
    subprocess.run(["/opt/fpp/bin/fppmm", "-m", model, "-t", text])

def main():
    last_processed_name = ""
    
    while True:
        settings = get_settings()
        if not settings or not settings.get('api_url'):
            time.sleep(10)
            continue

        try:
            r = requests.get(settings['api_url'], timeout=5)
            data = r.json()

            # Determine which name to show (prioritize most recent from either list)
            # This logic assumes 'nice' and 'naughty' come from your WP API
            newest_nice = data['nice'][0] if data['nice'] else None
            newest_naughty = data['naughty'][0] if data['naughty'] else None
            
            # Logic: Show the latest entry added to the system
            # For simplicity, we alternate or just show Nice if available
            if newest_nice:
                update_fpp(settings['model_header'], "NICE")
                update_fpp(settings['model_names'], newest_nice.upper())
            elif newest_naughty:
                update_fpp(settings['model_header'], "NAUGHTY")
                update_fpp(settings['model_names'], newest_naughty.upper())

        except Exception as e:
            print(f"Santa API Error: {e}")

        time.sleep(int(settings.get('interval', 10)))

if __name__ == "__main__":
    main()