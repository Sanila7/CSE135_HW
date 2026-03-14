<?php
require_once 'auth.php';
require_login();
if (!can_access_report('static')) {
    header('Location: /403.php'); exit();
}

$conn = new mysqli('localhost', 'collector', 'Collector@123!', 'collector_db');

// Data for chart - browser languages
$languages = [];
$result = $conn->query("SELECT JSON_UNQUOTE(JSON_EXTRACT(payload, '$.language')) as lang, COUNT(*) as count FROM events WHERE type='static' GROUP BY lang ORDER BY count DESC LIMIT 8");
while ($row = $result->fetch_assoc()) $languages[] = $row;

// Screen sizes
$screens = [];
$result = $conn->query("SELECT JSON_UNQUOTE(JSON_EXTRACT(payload, '$.screen_width')) as w, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.screen_height')) as h, COUNT(*) as count FROM events WHERE type='static' GROUP BY w, h ORDER BY count DESC LIMIT 6");
while ($row = $result->fetch_assoc()) $screens[] = $row;

// Table data
$rows = [];
$result = $conn->query("SELECT id, session_id, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.user_agent')) as user_agent, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.language')) as language, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.screen_width')) as screen_width, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.screen_height')) as screen_height, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.connection_type')) as connection_type, created_at FROM events WHERE type='static' ORDER BY id DESC LIMIT 50");
while ($row = $result->fetch_assoc()) $rows[] = $row;

// Comments
$comments = [];
$res = $conn->query("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.report = 'static' ORDER BY c.created_at DESC");
if ($res) while ($row = $res->fetch_assoc()) $comments[] = $row;

$conn->close();
$print = isset($_GET['print']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Static Report — Pulsar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body { background:#0f0e0c; color:#f5f0e8; }
.card { background:#1a1917; border:1px solid #333; border-radius:0; }
.card-header { background:#d63b2f; border-radius:0; font-size:.7rem; letter-spacing:.15em; text-transform:uppercase; }
table { font-size:.75rem; }
th { font-size:.6rem; letter-spacing:.1em; text-transform:uppercase; color:#8a8070; border-color:#333 !important; }
td { border-color:#222 !important; color:#f5f0e8; }
tr:hover td { background:#222 !important; }
.badge-static { background:#c8952a; color:#0f0e0c; }
.section-label { font-size:.6rem; letter-spacing:.2em; text-transform:uppercase; color:#d63b2f; }
textarea.form-control { background:#0f0e0c; border:1px solid #333; color:#f5f0e8; border-radius:0; }
textarea.form-control:focus { background:#0f0e0c; color:#f5f0e8; border-color:#d63b2f; box-shadow:none; }
<?php if ($print): ?>
body { background:#fff !important; color:#000 !important; }
nav, .no-print { display:none !important; }
<?php endif; ?>
</style>
</head>
<body>
<?php if (!$print): require_once 'nav.php'; endif; ?>

<div class="container-fluid py-4 px-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <div class="section-label">Report</div>
      <h2 style="font-family:Georgia,serif; font-style:italic; color:#f5f0e8;">Static Data</h2>
    </div>
    <?php if (!$print): ?>
    <div class="no-print d-flex gap-2">
      <a href="?print=1" target="_blank" class="btn btn-sm btn-outline-light" style="border-radius:0; font-size:.75rem;">Export PDF</a>
    </div>
    <?php endif; ?>
  </div>

  <!-- Charts -->
  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header text-white">Browser Languages</div>
        <div class="card-body"><canvas id="langChart"></canvas></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header text-white">Screen Resolutions</div>
        <div class="card-body"><canvas id="screenChart"></canvas></div>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="card mb-4">
    <div class="card-header text-white">Raw Static Data</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-dark table-hover mb-0">
          <thead><tr>
            <th>ID</th><th>Session</th><th>Language</th>
            <th>Screen</th><th>Connection</th><th>User Agent</th><th>Created</th>
          </tr></thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= $r['id'] ?></td>
              <td><?= htmlspecialchars(substr($r['session_id'],0,10)) ?>...</td>
              <td><?= htmlspecialchars($r['language'] ?? '—') ?></td>
              <td><?= $r['screen_width'] ?>x<?= $r['screen_height'] ?></td>
              <td><?= htmlspecialchars($r['connection_type'] ?? '—') ?></td>
              <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($r['user_agent'] ?? '—') ?></td>
              <td><?= $r['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Analyst Comments -->
  <div class="card mb-4">
    <div class="card-header text-white">Analyst Comments</div>
    <div class="card-body">
      <?php if (empty($comments)): ?>
        <p style="color:#8a8070; font-size:.8rem;">No comments yet.</p>
      <?php else: ?>
        <?php foreach ($comments as $c): ?>
          <div class="mb-3 pb-3" style="border-bottom:1px solid #333;">
            <div style="font-size:.65rem; color:#8a8070; margin-bottom:.3rem;">
              <b style="color:#c8952a;"><?= htmlspecialchars($c['username']) ?></b> — <?= $c['created_at'] ?>
            </div>
            <div style="font-size:.85rem;"><?= nl2br(htmlspecialchars($c['comment'])) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if (is_analyst() && !$print): ?>
      <form method="POST" action="/save-comment.php" class="mt-3 no-print">
        <input type="hidden" name="report" value="static">
        <div class="mb-2">
          <textarea name="comment" class="form-control" rows="3" placeholder="Add analyst comment..."></textarea>
        </div>
        <button type="submit" class="btn btn-sm text-white" style="background:#d63b2f; border-radius:0; font-size:.75rem;">Post Comment</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
const langData = <?= json_encode($languages) ?>;
const screenData = <?= json_encode($screens) ?>;

new Chart(document.getElementById('langChart'), {
  type: 'bar',
  data: {
    labels: langData.map(d => d.lang || 'unknown'),
    datasets: [{ label: 'Count', data: langData.map(d => d.count), backgroundColor: '#d63b2f' }]
  },
  options: { plugins: { legend: { labels: { color: '#f5f0e8' } } },
    scales: { x: { ticks: { color: '#8a8070' }, grid: { color: '#222' } }, y: { ticks: { color: '#8a8070' }, grid: { color: '#222' } } } }
});

new Chart(document.getElementById('screenChart'), {
  type: 'doughnut',
  data: {
    labels: screenData.map(d => d.w + 'x' + d.h),
    datasets: [{ data: screenData.map(d => d.count), backgroundColor: ['#d63b2f','#c8952a','#2a7ac8','#2ac87a','#9b59b6','#e74c3c'] }]
  },
  options: { plugins: { legend: { labels: { color: '#f5f0e8' } } } }
});
</script>
</body>
</html>
