<?php
require_once 'auth.php';
require_login();
$user = current_user();

$conn = new mysqli('localhost', 'collector', 'Collector@123!', 'collector_db');

$activity_counts = [];
$result = $conn->query("SELECT event, COUNT(*) as count FROM events WHERE type='activity' AND event IS NOT NULL GROUP BY event ORDER BY count DESC LIMIT 8");
while ($row = $result->fetch_assoc()) $activity_counts[] = $row;

$type_counts = [];
$result = $conn->query("SELECT type, COUNT(*) as count FROM events GROUP BY type");
while ($row = $result->fetch_assoc()) $type_counts[] = $row;

$total = $conn->query("SELECT COUNT(*) as c FROM events")->fetch_assoc()['c'];
$sessions = $conn->query("SELECT COUNT(DISTINCT session_id) as c FROM events")->fetch_assoc()['c'];
$today = $conn->query("SELECT COUNT(*) as c FROM events WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['c'];

$events = [];
$result = $conn->query("SELECT id, session_id, type, event, page, created_at FROM events ORDER BY id DESC LIMIT 20");
while ($row = $result->fetch_assoc()) $events[] = $row;

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Pulsar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body { background:#0f0e0c; color:#f5f0e8; }
.card { background:#1a1917; border:1px solid #333; border-radius:0; }
.card-header { background:#d63b2f; border-radius:0; font-size:.7rem; letter-spacing:.15em; text-transform:uppercase; }
.stat-card { background:#1a1917; border:1px solid #333; padding:1.5rem; text-align:center; }
.stat-num { font-size:2.5rem; font-family:Georgia,serif; color:#d63b2f; line-height:1; }
.stat-label { font-size:.6rem; letter-spacing:.15em; text-transform:uppercase; color:#8a8070; margin-top:.3rem; }
table { font-size:.75rem; }
th { font-size:.6rem; letter-spacing:.1em; text-transform:uppercase; color:#8a8070; border-color:#333 !important; }
td { border-color:#222 !important; color:#f5f0e8; }
tr:hover td { background:#222 !important; }
.section-label { font-size:.6rem; letter-spacing:.2em; text-transform:uppercase; color:#d63b2f; }
</style>
</head>
<body>
<?php require_once 'nav.php'; ?>

<div class="container-fluid py-4 px-4">
  <div class="mb-4">
    <div class="section-label">Overview</div>
    <h2 style="font-family:Georgia,serif; font-style:italic;">Analytics Dashboard</h2>
  </div>

  <!-- Stats -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-num"><?= number_format($total) ?></div>
        <div class="stat-label">Total Events</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-num"><?= number_format($sessions) ?></div>
        <div class="stat-label">Unique Sessions</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-num"><?= number_format($today) ?></div>
        <div class="stat-label">Events Today</div>
      </div>
    </div>
  </div>

  <!-- Charts -->
  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header text-white">Events by Type</div>
        <div class="card-body"><canvas id="typeChart"></canvas></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header text-white">Top Activity Events</div>
        <div class="card-body"><canvas id="activityChart"></canvas></div>
      </div>
    </div>
  </div>

  <!-- Quick links -->
  <div class="row g-3 mb-4">
    <?php if (can_access_report('static')): ?>
    <div class="col-md-4">
      <a href="/report-static.php" class="text-decoration-none">
        <div class="stat-card" style="border-color:#c8952a;">
          <div style="font-size:.6rem; letter-spacing:.15em; text-transform:uppercase; color:#c8952a;">Report</div>
          <div style="font-size:1.2rem; font-family:Georgia,serif; margin-top:.3rem;">Static Data →</div>
        </div>
      </a>
    </div>
    <?php endif; ?>
    <?php if (can_access_report('performance')): ?>
    <div class="col-md-4">
      <a href="/report-performance.php" class="text-decoration-none">
        <div class="stat-card" style="border-color:#2a7ac8;">
          <div style="font-size:.6rem; letter-spacing:.15em; text-transform:uppercase; color:#2a7ac8;">Report</div>
          <div style="font-size:1.2rem; font-family:Georgia,serif; margin-top:.3rem; color:#f5f0e8;">Performance Data →</div>
        </div>
      </a>
    </div>
    <?php endif; ?>
    <?php if (can_access_report('activity')): ?>
    <div class="col-md-4">
      <a href="/report-activity.php" class="text-decoration-none">
        <div class="stat-card" style="border-color:#2ac87a;">
          <div style="font-size:.6rem; letter-spacing:.15em; text-transform:uppercase; color:#2ac87a;">Report</div>
          <div style="font-size:1.2rem; font-family:Georgia,serif; margin-top:.3rem; color:#f5f0e8;">Activity Data →</div>
        </div>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <!-- Recent Events Table -->
  <div class="card">
    <div class="card-header text-white">Recent Events</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-dark table-hover mb-0">
          <thead><tr>
            <th>ID</th><th>Session</th><th>Type</th><th>Event</th><th>Page</th><th>Created</th>
          </tr></thead>
          <tbody>
            <?php foreach ($events as $e): ?>
            <tr>
              <td><?= $e['id'] ?></td>
              <td><?= htmlspecialchars(substr($e['session_id'],0,10)) ?>...</td>
              <td><span class="badge" style="background:<?= $e['type']==='static'?'#c8952a':($e['type']==='performance'?'#2a7ac8':'#2ac87a') ?>; color:<?= $e['type']==='performance'?'#fff':'#0f0e0c' ?>;"><?= $e['type'] ?></span></td>
              <td><?= htmlspecialchars($e['event'] ?? '—') ?></td>
              <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($e['page'] ?? '—') ?></td>
              <td><?= $e['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
const typeData = <?= json_encode($type_counts) ?>;
const activityData = <?= json_encode($activity_counts) ?>;

new Chart(document.getElementById('typeChart'), {
  type: 'doughnut',
  data: {
    labels: typeData.map(d => d.type),
    datasets: [{ data: typeData.map(d => d.count), backgroundColor: ['#d63b2f','#c8952a','#2a7ac8','#2ac87a'] }]
  },
  options: { plugins: { legend: { labels: { color: '#f5f0e8' } } } }
});

new Chart(document.getElementById('activityChart'), {
  type: 'bar',
  data: {
    labels: activityData.map(d => d.event),
    datasets: [{ label: 'Count', data: activityData.map(d => d.count), backgroundColor: '#d63b2f' }]
  },
  options: { plugins: { legend: { labels: { color: '#f5f0e8' } } },
    scales: { x: { ticks: { color: '#8a8070' }, grid: { color: '#222' } }, y: { ticks: { color: '#8a8070' }, grid: { color: '#222' } } } }
});
</script>
</body>
</html>
