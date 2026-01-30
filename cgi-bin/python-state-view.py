#!/usr/bin/env python3

import os
import sys
from urllib.parse import parse_qs

print("Content-Type: text/html")

cookies = os.environ.get("HTTP_COOKIE", "")
session_id = None

for cookie in cookies.split(";"):
    if cookie.strip().startswith("PYSESSID="):
        session_id = cookie.strip().split("=")[1]

print()

data_file = f"/tmp/python_state_{session_id}.txt" if session_id else None

# Save incoming data
if os.environ.get("REQUEST_METHOD") == "POST" and session_id:
    length = int(os.environ.get("CONTENT_LENGTH", 0))
    body = sys.stdin.read(length)
    params = parse_qs(body)
    value = params.get("data", [""])[0]

    with open(data_file, "w") as f:
        f.write(value)

# Load saved data
saved_data = ""
if data_file and os.path.exists(data_file):
    with open(data_file) as f:
        saved_data = f.read()

print(f"""<!DOCTYPE html>
<html>
<head>
  <title>Python State View</title>
</head>
<body>
  <h1>Saved State</h1>
  <p>Team Member: Sanila Silva (Solo)</p>

  <p><strong>Saved Value:</strong> {saved_data or "(nothing saved yet)"}</p>

  <p><a href="/cgi-bin/python-state-form.py">Back to Form</a></p>
  <p><a href="/cgi-bin/python-state-clear.py">Clear State</a></p>
  <p><a href="/">Back to Home</a></p>
</body>
</html>
""")
