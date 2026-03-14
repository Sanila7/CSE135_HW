<?php
$user = current_user();
$role = $user['role'];
$allowed = explode(',', $user['allowed_reports']);
?>
<nav class="navbar navbar-dark" style="background:#0f0e0c; border-bottom: 2px solid #d63b2f;">
  <div class="container-fluid">
    <a class="navbar-brand" style="font-family:Georgia,serif; font-style:italic; color:#d63b2f; font-size:1.6rem;">Pulsar</a>
    <div class="d-flex align-items-center gap-3">
      <?php if (in_array('static', $allowed)): ?>
        <a href="/report-static.php" class="text-light text-decoration-none" style="font-size:.75rem; letter-spacing:.08em;">STATIC</a>
      <?php endif; ?>
      <?php if (in_array('performance', $allowed)): ?>
        <a href="/report-performance.php" class="text-light text-decoration-none" style="font-size:.75rem; letter-spacing:.08em;">PERFORMANCE</a>
      <?php endif; ?>
      <?php if (in_array('activity', $allowed)): ?>
        <a href="/report-activity.php" class="text-light text-decoration-none" style="font-size:.75rem; letter-spacing:.08em;">ACTIVITY</a>
      <?php endif; ?>
      <?php if ($role === 'superadmin'): ?>
        <a href="/users.php" class="text-warning text-decoration-none" style="font-size:.75rem; letter-spacing:.08em;">USERS</a>
      <?php endif; ?>
      <span style="font-size:.7rem; color:#8a8070;">
        <?= htmlspecialchars($user['username']) ?> 
        <span class="badge" style="background:#d63b2f; font-size:.55rem;"><?= $role ?></span>
      </span>
      <a href="/logout.php" class="btn btn-sm btn-outline-light" style="font-size:.7rem; border-radius:0;">Logout</a>
    </div>
  </div>
</nav>
