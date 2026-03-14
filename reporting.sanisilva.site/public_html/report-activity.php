<?php
require_once 'auth.php';
require_login();
if (!can_access_report('activity')) {
    header('Location: /403.php'); exit();
}

$conn = new mysqli('localhost', 'collector', 'Collector@123!', 'collector_db');

// Event type counts for chart
$event_counts = [];
$result = $conn->query("SELECT event, COUNT(*) as count FROM events WHERE type='activity' AND event IS NOT NULL GROUP BY event ORDER BY count DESC LIMIT 8");
while ($row = $result->fetch_assoc()) $event_counts[] = $row;

// Activity over time (per day)
$daily = [];
$result = $conn->query("SELECT DATE(created_at) as day, COUNT(*) as count FROM events WHERE type='activity' GROUP BY day ORDER BY day DESC LIMIT 14");
while ($row = $result->fetch_assoc()) $daily[] = $row;
$daily = array_reverse($daily);

// Table
$rows = [];
$result = $conn->query("SELECT id, session_id, event, page, created_at FROM events WHERE type='activity' ORDER BY id DESC LIMIT 50");
while ($row = $result->fetch_assoc()) $rows[] = $row;

// Comments
$comments = [];
$res = $conn->query("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.report = 'activity' ORDER BY c.created_at DESC");
if ($res) while ($row = $res->fetch_assoc()) $comments[] = $row;

$conn->close();
$print = isset($_GET['print']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Activity Report — Pulsar</title>
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
      <h2 style="font-family:Georgia,serif; font-style:italic;">Activity Data</h2>
    </div>
    <?php if (!$print): ?>
    <a href="?print=1" target="_blank" class="btn btn-sm btn-outline-light no-print" style="border-radius:0; font-size:.75rem;">Export PDF</a>
    <?php endif; ?>
  </div>

  <!-- Charts -->
  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header text-white">Top Activity Events</div>
        <div class="card-body"><canvas id="eventChart"></canvas></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header text-white">Activity Over Time (Last 14 Days)</div>
        <div class="card-body"><canvas id="dailyChart"></canvas></div>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="card mb-4">
    <div class="card-header text-white">Raw Activity Data</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-dark table-hover mb-0">
          <thead><tr>
            <th>ID</th><th>Session</th><th>Event</th><th>Page</th><th>Created</th>
          </tr></thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= $r['id'] ?></td>
              <td><?= htmlspecialchars(substr($r['session_id'],0,10)) ?>...</td>
              <td><span class="badge" style="background:#2ac87a; color:#0f0e0c;"><?= htmlspecialchars($r['event'] ?? '—') ?></span></td>
              <td style="max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($r['page'] ?? '—') ?></td>
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
        <input type="hidden" name="report" value="activity">
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
const eventData = <?= json_encode($event_counts) ?>;
const dailyData = <?= json_encode($daily) ?>;

new Chart(document.getElementById('eventChart'), {
  type: 'bar',
  data: {
    labels: eventData.map(d => d.event),
    datasets: [{ label: 'Count', data: eventData.map(d => d.count), backgroundColor: '#2ac87a' }]
  },
  options: { plugins: { legend: { labels: { color: '#f5f0e8' } } },
    scales: { x: { ticks: { color: '#8a8070' }, grid: { color: '#222' } }, y: { ticks: { color: '#8a8070' }, grid: { color: '#222' } } } }
});

new Chart(document.getElementById('dailyChart'), {
  type: 'line',
  data: {
    labels: dailyData.map(d => d.day),
    datasets: [{ label: 'Events', data: dailyData.map(d => d.count), borderColor: '#2ac87a', backgroundColor: 'rgba(42,200,122,0.1)', tension: 0.3, fill: true }]
  },
  options: { plugins: { legend: { labels: { color: '#f5f0e8' } } },
    scales: { x: { ticks: { color: '#8a8070' }, grid: { color: '#222' } }, y: { ticks: { color: '#8a8070' }, grid: { color: '#222' } } } }
});
</script>
</body>
</html>
