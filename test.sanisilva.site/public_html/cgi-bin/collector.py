#!/usr/bin/env python3
import sys
import json
import os
import traceback

LOG_FILE = "/var/www/test.sanisilva.site/public_html/collector_log.txt"

print("Content-Type: application/json\n")

try:
    content_length = int(os.environ.get("CONTENT_LENGTH", "0"))
    raw_data = sys.stdin.read(content_length) if content_length > 0 else ""

    if raw_data:
        data = json.loads(raw_data)
    else:
        data = {"note": "no POST body"}

    # Write log (guaranteed writable location)
    with open(LOG_FILE, "a") as f:
        f.write(json.dumps(data) + "\n")

    print(json.dumps({
        "status": "success",
        "log_file": LOG_FILE
    }))

except Exception as e:
    # Log the error so we can debug
    with open(LOG_FILE, "a") as f:
        f.write("ERROR: " + str(e) + "\n")
        f.write(traceback.format_exc() + "\n")

    print(json.dumps({
        "status": "error",
        "message": str(e)
    }))
