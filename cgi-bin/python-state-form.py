#!/usr/bin/env python3

import os
import uuid

print("Content-Type: text/html")

# Get or create session ID
cookies = os.environ.get("HTTP_COOKIE", "")
session_id = None

for cookie in cookies.split(";"):
    if cookie.strip().startswith("PYSESSID="):
        session_id = cookie.strip().split("=")[1]

if not session_id:
    session_id = str(uuid.uuid4())
    print(f"Set-Cookie: PYSESSID={session_id}; Path=/")

print()

print(f"""<!DOCTYPE html>
<html>
<head>
  <title>Python State Form</title>
</head>
<body>
  <h1>Python State Demo</h1>
  <p>Team Member: Sanila Silva (Solo)</p>

  <form method="POST" action="/cgi-bin/python-state-view.py">
    <label>Enter some text to save:</label><br>
    <input type="text" name="data"><br><br>
    <button type="submit">Save Data</button>
  </form>

  <p><a href="/cgi-bin/python-state-view.py">View Saved Data</a></p>
  <p><a href="/">Back to Home</a></p>
</body>
</html>
""")
