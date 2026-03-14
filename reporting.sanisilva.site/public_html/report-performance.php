<?php
require_once 'auth.php';
require_login();
if (!can_access_report('performance')) {
    header('Location: /403.php'); exit();
}

$conn = new mysqli('localhost', 'collector', 'Collector@123!', 'collector_db');

// Avg load time per session
$loadtimes = [];
$result = $conn->query("SELECT session_id, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.total_load_time_ms')) as load_time, created_at FROM events WHERE type='performance' AND JSON_EXTRACT(payload, '$.total_load_time_ms') IS NOT NULL ORDER BY id DESC LIMIT 20");
while ($row = $result->fetch_assoc()) $loadtimes[] = $row;

// Load time buckets for chart
$buckets = ['0-500ms' => 0, '500-1000ms' => 0, '1000-2000ms' => 0, '2000ms+' => 0];
foreach ($loadtimes as $l) {
    $t = (int)$l['load_time'];
    if ($t < 500) $buckets['0-500ms']++;
    elseif ($t < 1000) $buckets['500-1000ms']++;
    elseif ($t < 2000) $buckets['1000-2000ms']++;
    else $buckets['2000ms+']++;
}

// All performance rows
$rows = [];
$result = $conn->query("SELECT id, session_id, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.total_load_time_ms')) as load_time_ms, JSON_UNQUOTE(JSON_EXTRACT(payload, '$.page_load_start')) as load_start, created_at FROM events WHERE type='performance' ORDER BY id DESC LIMIT 50");
while ($row = $result->fetch_assoc()) $rows[] = $row;

// Comments
$comments = [];
$res = $conn->query("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.report = 'performance' ORDER BY c.created_at DESC");
if ($res) while ($row = $res->fetch_assoc()) $comments[] = $row;

$conn->close();
$print = isset($_GET['print']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Performance Report — Pulsar</title>
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
      <h2 style="font-family:Georgia,serif; font-style:italic;">Performance Data</h2>
    </div>
    <?php if (!$print): ?>
    <a href="?print=1" target="_blank" class="btn btn-sm btn-outline-light no-print" style="border-radius:0; font-size:.75rem;">Export PDF</a>
    <?php endif; ?>
  </div>

  <!-- Stats row -->
  <?php
    $avg = count($loadtimes) ? array_sum(array_column($loadtimes, 'load_time')) / count($loadtimes) : 0;
    $min = count($loadtimes) ? min(array_column($loadtimes, 'load_time')) : 0;
    $max = count($loadtimes) ? max(array_column($loadtimes, 'load_time')) : 0;
  ?>
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card text-center p-3">
        <div style="font-size:.6rem; letter-spacing:.15em; text-transform:uppercase; color:#8a8070;">Avg Load Time</div>
        <div style="font-size:2rem; font-family:Georgia,serif; color:#d63b2f;"><?= round($avg) ?><span style="font-size:.9rem;">ms</span></div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center p-3">
        <div style="font-size:.6rem; letter-spacing:.15em; text-transform:uppercase; color:#8a8070;">Min Load Time</div>
        <div style="font-size:2rem; font-family:Georgia,serif; color:#2ac87a;"><?= round($min) ?><span style="font-size:.9rem;">ms</span></div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center p-3">
        <div style="font-size:.6rem; letter-spacing:.15em; text-transform:uppercase; color:#8a8070;">Max Load Time</div>
        <div style="font-size:2rem; font-family:Georgia,serif; color:#c8952a;"><?= round($max) ?><span style="font-size:.9rem;">ms</span></div>
      </div>
    </div>
  </div>

  <!-- Charts -->
  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header text-white">Load Time Distribution</div>
        <div class="card-body"><canvas id="bucketChart"></canvas></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header text-white">Load Time Per Session (Recent 20)</div>
        <div class="card-body"><canvas id="lineChart"></canvas></div>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="card mb-4">
    <div class="card-header text-white">Raw Performance Data</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-dark table-hover mb-0">
          <thead><tr>
            <th>ID</th><th>Session</th><th>Load Time (ms)</th><th>Created</th>
          </tr></thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= $r['id'] ?></td>
              <td><?= htmlspecialchars(substr($r['session_id'],0,10)) ?>...</td>
              <td><?= $r['load_time_ms'] ?? '—' ?></td>
              <td><?= $r['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Comments -->
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
        <input type="hidden" name="report" value="performance">
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
const buckets = <?= json_encode(array_values($buckets)) ?>;
const bucketLabels = <?= json_encode(array_keys($buckets)) ?>;
const sessions = <?= json_encode(array_reverse($loadtimes)) ?>;

new Chart(document.getElementById('bucketChart'), {
  type: 'bar',
  data: {
    labels: bucketLabels,
    datasets: [{ label: 'Sessions', data: buckets, backgroundColor: '#2a7ac8' }]
  },
  options: { plugins: { legend: { labels: { color: '#f5f0e8' } } },
    scales: { x: { ticks: { color: '#8a8070' }, grid: { color: '#222' } }, y: { ticks: { color: '#8a8070' }, grid: { color: '#222' } } } }
});

new Chart(document.getElementById('lineChart'), {
  type: 'line',
  data: {
    labels: sessions.map((d, i) => i + 1),
    datasets: [{ label: 'Load Time (ms)', data: sessions.map(d => d.load_time), borderColor: '#d63b2f', backgroundColor: 'rgba(214,59,47,0.1)', tension: 0.3, fill: true }]
  },
  options: { plugins: { legend: { labels: { color: '#f5f0e8' } } },
    scales: { x: { ticks: { color: '#8a8070' }, grid: { color: '#222' } }, y: { ticks: { color: '#8a8070' }, grid: { color: '#222' } } } }
});
</script>
</body>
</html>
