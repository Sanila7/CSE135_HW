<?php http_response_code(403); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>403 — Forbidden</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#0f0e0c; color:#f5f0e8; display:flex; align-items:center; justify-content:center; min-height:100vh; text-align:center; }
.code { font-size:8rem; font-family:Georgia,serif; color:#d63b2f; line-height:1; }
.msg { font-size:.7rem; letter-spacing:.2em; text-transform:uppercase; color:#8a8070; margin-bottom:2rem; }
a { color:#d63b2f; font-size:.8rem; letter-spacing:.08em; }
</style>
</head>
<body>
<div>
  <div class="code">403</div>
  <div class="msg">Access Forbidden — You don't have permission to view this page</div>
  <a href="/dashboard.php">← Back to Dashboard</a>
</div>
</body>
</html>
