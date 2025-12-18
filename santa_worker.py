import json
import time
import requests
import subprocess
import os

# Path to the settings file we created in plugin_setup.php
SETTINGS_FILE = '/home/fpp/media/config/plugin.fpp-santa-list.json'

def run_fppmm(model_name, command, value=""):
    """Helper to send commands to FPP Memory Maps"""
    try:
        # Commands: 'on', 'off', 'set'
        cmd = ["/opt/fpp/bin/fppmm", "-m", model_name, "-o", command]
        if value:
            cmd.append(value)
        subprocess.run(cmd, check=True)
    except Exception as e:
        print(f"FPPMM Error: {e}")

def display_on_matrix(header_model, names_model, status, name):
    """Updates the LED Matrix via FPP Memory Maps"""
    # 1. Update Header (NICE/NAUGHTY)
    # Note: 'set' command with fppmm requires the model to be in 'on' mode
    run_fppmm(header_model, "on")
    run_fppmm(header_model, "set", status.upper())

    # 2. Update Name
    run_fppmm(names_model, "on")
    run_fppmm(names_model, "set", name.upper())

def main():
    print("Santa Worker Starting...")
    
    while True:
        # Check if settings exist; if not, wait and retry
        if not os.path.exists(SETTINGS_FILE):
            print("Waiting for settings file...")
            time.sleep(10)
            continue

        try:
            with open(SETTINGS_FILE, 'r') as f:
                config = json.load(f)
            
            api_url = config.get('api_url')
            model_header = config.get('model_header', 'Screen1')
            model_names = config.get('model_names', 'Screen2')
            interval = int(config.get('interval', 10))

            # Fetch from WordPress
            response = requests.get(api_url, timeout=5)
            data = response.json()

            # Logic: Prioritize Nice list, then Naughty
            if data.get('nice'):
                # Get the most recent name (assuming last in list or adjust as needed)
                display_on_matrix(model_header, model_names, "NICE", data['nice'][0])
            elif data.get('naughty'):
                display_on_matrix(model_header, model_names, "NAUGHTY", data['naughty'][0])
            else:
                # Optional: Clear screens if lists are empty
                run_fppmm(model_header, "off")
                run_fppmm(model_names, "off")

        except Exception as e:
            print(f"Worker Loop Error: {e}")

        # Sleep based on the user-defined interval
        time.sleep(interval)

if __name__ == "__main__":
    main()
