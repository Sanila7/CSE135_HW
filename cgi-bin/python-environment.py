#!/usr/bin/env python3

import os

# CGI header
print("Content-Type: text/html\n")

print("""<!DOCTYPE html>
<html>
<head>
    <title>Python Environment Variables</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Python Environment Variables</h1>
    <p><strong>Team Member:</strong> Sanila Silva (Solo)</p>
    <p>This page displays the environment variables for the current request.</p>

    <table>
        <tr>
            <th>Variable</th>
            <th>Value</th>
        </tr>
""")

# Print environment variables
for key, value in sorted(os.environ.items()):
    print(f"""
        <tr>
            <td>{key}</td>
            <td>{value}</td>
        </tr>
    """)

print("""
    </table>

    <p><a href="/">Back to Home</a></p>
</body>
</html>
""")
