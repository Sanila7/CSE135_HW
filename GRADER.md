# GRADER.md — Pulsar Analytics

## Login Credentials

| Role | Username | Password |
|------|----------|----------|
| Super Admin | superadmin | super123 |
| Analyst | analyst | analyst123 |
| Viewer | viewer | viewer123 |

## Grading Scenario

1. Go to https://reporting.sanisilva.site
2. Log in as `superadmin` / `super123`
3. You will see the main dashboard with total events, unique sessions, charts, and recent events table
4. Click **STATIC** in the navbar — view the static data report with browser language chart, screen resolution chart, raw data table, and analyst comments box
5. Click **PERFORMANCE** — view load time stats (avg/min/max), distribution chart, line chart, and raw data table
6. Click **ACTIVITY** — view top activity events bar chart, activity over time line chart, and raw data table
7. Add an analyst comment on any report using the text box at the bottom
8. Click **Export PDF** on any report — a print-friendly version opens in a new tab
9. Click **USERS** — add a new user, change roles, restrict report access
10. Log out, then log in as `analyst` / `analyst123` — same access but no USERS page
11. Log in as `viewer` / `viewer123` — can view reports but cannot add comments or export
12. Try visiting https://reporting.sanisilva.site/users.php as viewer — you will get a 403 page
13. Try visiting https://reporting.sanisilva.site/nonexistent — you will get a 404 page

## Known Issues / Architecture Notes

- The viewer role currently has access to all 3 reports by default. Report restriction
  per analyst is configurable via the User Management page by the superadmin.
- PDF export uses browser print dialog rather than server-side PDF generation.
  A future improvement would be server-side PDF via a library like mPDF.
- The collector.js throttles mousemove and scroll events to 1/second to avoid
  overwhelming the database with requests.
- No CSRF protection on forms — this is a known gap for a production system.
- Session data is stored server-side via PHP sessions (not JWT).
