#!/usr/bin/env python3

import os
import sys
import json
from datetime import datetime
from urllib.parse import parse_qs

method = os.environ.get("REQUEST_METHOD", "UNKNOWN")
content_type = os.environ.get("CONTENT_TYPE", "")
ip = os.environ.get("REMOTE_ADDR", "Unknown")
agent = os.environ.get("HTTP_USER_AGENT", "Unknown")
host = os.environ.get("HTTP_HOST", "Unknown")
now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

data = {}

# Read input
if method in ["POST", "PUT", "DELETE"]:
    length = int(os.environ.get("CONTENT_LENGTH", 0))
    body = sys.stdin.read(length)

    if "application/json" in content_type and body:
        data = json.loads(body)
    else:
        data = {k: v[0] for k, v in parse_qs(body).items()}

elif method == "GET":
    data = {k: v[0] for k, v in parse_qs(os.environ.get("QUERY_STRING", "")).items()}

# Output
print("Content-Type: text/html\n")

print(f"""
<!DOCTYPE html>
<html>
<head>
  <title>Python Echo</title>
</head>
<body>
  <h1>Python Echo Response</h1>

  <p><strong>Team Member:</strong> Sanila Silva (Solo)</p>
  <p><strong>Method:</strong> {method}</p>
  <p><strong>Host:</strong> {host}</p>
  <p><strong>Generated at:</strong> {now}</p>
  <p><strong>User Agent:</strong> {agent}</p>
  <p><strong>IP Address:</strong> {ip}</p>

  <h3>Echoed Data</h3>
  <pre>{json.dumps(data, indent=2)}</pre>

  <p><a href="/echo-form.html">Back to Echo Form</a></p>
</body>
</html>
""")
