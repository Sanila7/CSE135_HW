#!/usr/bin/env python3

import os
import json
from datetime import datetime

# Data
response = {
    "message": "Hello from Python!",
    "language": "Python",
    "team_member": "Sanila Silva (Solo)",
    "generated_at": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
    "ip_address": os.environ.get("REMOTE_ADDR", "Unknown")
}

# CGI header
print("Content-Type: application/json\n")

# JSON output
print(json.dumps(response, indent=2))
