#!/usr/bin/env python3
import sys
import json
import os
import time
import traceback

# THIS MUST be writable by Apache (www-data)
LOG_FILE = "/var/www/collector.sanisilva.site/public_html/collector_log.txt"
ALLOWED_ORIGIN = "https://test.sanisilva.site"

method = os.environ.get("REQUEST_METHOD", "GET")

# --- Handle CORS Preflight (VERY important for browser POST) ---
if method == "OPTIONS":
    print("Status: 204 No Content")
    print(f"Access-Control-Allow-Origin: {ALLOWED_ORIGIN}")
    print("Access-Control-Allow-Methods: POST, OPTIONS")
    print("Access-Control-Allow-Headers: Content-Type")
    print()
    sys.exit(0)

# --- Standard headers ---
print(f"Access-Control-Allow-Origin: {ALLOWED_ORIGIN}")
print("Content-Type: application/json\n")

# --- Ignore GET requests so your log is not spammed ---
if method == "GET":
    print(json.dumps({
        "status": "ok",
        "note": "collector endpoint active (send POST JSON)"
    }))
    sys.exit(0)

try:
    # Read POST body safely
    content_length = int(os.environ.get("CONTENT_LENGTH", "0"))
    raw_data = sys.stdin.read(content_length) if content_length > 0 else ""

    if raw_data:
        try:
            data = json.loads(raw_data)
        except json.JSONDecodeError:
            data = {"raw": raw_data, "note": "invalid JSON"}
    else:
        data = {"note": "empty POST body"}

    # Add server-side metadata (useful for grading)
    data["_server_time"] = time.time()
    data["_ip"] = os.environ.get("REMOTE_ADDR", "unknown")
    data["_user_agent"] = os.environ.get("HTTP_USER_AGENT", "unknown")

    # Write to log file (append mode)
    with open(LOG_FILE, "a") as f:
        f.write(json.dumps(data) + "\n")

    # Return success JSON
    print(json.dumps({
        "status": "success",
        "logged": True
    }))

except Exception as e:
    # Log the error so you can debug
    try:
        with open(LOG_FILE, "a") as f:
            f.write("ERROR: " + str(e) + "\n")
            f.write(traceback.format_exc() + "\n")
    except:
        pass

    print(json.dumps({
        "status": "error",
        "message": str(e)
    }))
