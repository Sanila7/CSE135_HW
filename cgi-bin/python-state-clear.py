#!/usr/bin/env python3

import os

print("Content-Type: text/html")

cookies = os.environ.get("HTTP_COOKIE", "")
session_id = None

for cookie in cookies.split(";"):
    if cookie.strip().startswith("PYSESSID="):
        session_id = cookie.strip().split("=")[1]

# Delete stored data
if session_id:
    path = f"/tmp/python_state_{session_id}.txt"
    if os.path.exists(path):
        os.remove(path)

# Expire cookie
print("Set-Cookie: PYSESSID=deleted; Path=/; Max-Age=0")
print()

print("""<!DOCTYPE html>
<html>
<head>
  <title>State Cleared</title>
</head>
<body>
  <h1>State Cleared</h1>
  <p>The saved session data has been removed.</p>

  <p><a href="/cgi-bin/python-state-form.py">Start Again</a></p>
  <p><a href="/">Back to Home</a></p>
</body>
</html>
""")
