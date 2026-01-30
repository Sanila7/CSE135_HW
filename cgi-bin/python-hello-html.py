#!/usr/bin/env python3

import os
from datetime import datetime

# CGI header
print("Content-Type: text/html\n")

# Data
language = "Python"
team_member = "Sanila Silva"
now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
ip = os.environ.get("REMOTE_ADDR", "Unknown")

# HTML output
print(f"""<!DOCTYPE html>
<html>
<head>
    <title>Hello HTML - Python</title>
</head>
<body>
    <h1>Hello from {language}!</h1>

    <p><strong>Team Member:</strong> {team_member} (Solo)</p>
    <p><strong>Language:</strong> {language}</p>
    <p><strong>Generated at:</strong> {now}</p>
    <p><strong>Your IP address:</strong> {ip}</p>

    <p><a href="/">Back to Home</a></p>
</body>
</html>
""")
