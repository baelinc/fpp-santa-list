import time
import requests
import subprocess

def get_fpp_setting(setting_name, default=""):
    """Reads a setting directly from the FPP database"""
    try:
        result = subprocess.run(["/opt/fpp/bin/config", "get", setting_name], 
                                capture_output=True, text=True)
        val = result.stdout.strip()
        return val if val else default
    except:
        return default

def run_fppmm(model_name, command, value=""):
    try:
        cmd = ["/opt/fpp/bin/fppmm", "-m", model_name, "-o", command]
        if value: cmd.append(value)
        subprocess.run(cmd)
    except:
        pass

def main():
    while True:
        # Pull latest settings from the database
        api_url = get_fpp_setting("plugin.fpp-santa-list.api_url", "https://christmas.onthehill.us/wp-json/santa/v1/list")
        model_header = get_fpp_setting("plugin.fpp-santa-list.model_header", "Screen1")
        model_names = get_fpp_setting("plugin.fpp-santa-list.model_names", "Screen2")
        interval = int(get_fpp_setting("plugin.fpp-santa-list.interval", 10))

        try:
            r = requests.get(api_url, timeout=5)
            data = r.json()
            
            if data.get('nice'):
                run_fppmm(model_header, "on")
                run_fppmm(model_header, "set", "NICE")
                run_fppmm(model_names, "on")
                run_fppmm(model_names, "set", data['nice'][0].upper())
            elif data.get('naughty'):
                run_fppmm(model_header, "on")
                run_fppmm(model_header, "set", "NAUGHTY")
                run_fppmm(model_names, "on")
                run_fppmm(model_names, "set", data['naughty'][0].upper())
        except Exception as e:
            print(f"Loop Error: {e}")

        time.sleep(interval)

if __name__ == "__main__":
    main()
