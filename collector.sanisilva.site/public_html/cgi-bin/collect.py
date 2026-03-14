#!/usr/bin/env python3
import sys, os, json
import pymysql
import datetime

print("Content-Type: application/json")
print("Access-Control-Allow-Origin: *")
print("Access-Control-Allow-Methods: POST, OPTIONS")
print("Access-Control-Allow-Headers: Content-Type")
print()

method = os.environ.get("REQUEST_METHOD", "")

if method == "OPTIONS":
    print(json.dumps({"status": "ok"}))
    sys.exit(0)

if method != "POST":
    print(json.dumps({"error": "POST only"}))
    sys.exit(0)

try:
    length = int(os.environ.get("CONTENT_LENGTH", 0))
    body = sys.stdin.read(length)
    data = json.loads(body)

    conn = pymysql.connect(
        host="localhost",
        user="collector",
        password="Collector@123!",
        database="collector_db"
    )
    cur = conn.cursor()

    cur.execute("""
        INSERT INTO events (session_id, page, type, event, ts, payload)
        VALUES (%s, %s, %s, %s, %s, %s)
    """, (
        data.get("session_id"),
        data.get("page"),
        data.get("type"),
        data.get("event"),
        data.get("ts"),
        json.dumps(data)
    ))

    conn.commit()
    conn.close()
    print(json.dumps({"status": "ok"}))

except Exception as e:
    print(json.dumps({"error": str(e)}))
