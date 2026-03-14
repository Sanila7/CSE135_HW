<?php
require_once 'auth.php';
require_role('superadmin');

$conn = new mysqli('localhost', 'collector', 'Collector@123!', 'collector_db');
$message = '';

// Handle add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $u = $_POST['username'];
        $p = $_POST['password'];
        $r = $_POST['role'];
        $reports = implode(',', $_POST['reports'] ?? ['static','performance','activity']);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, allowed_reports) VALUES (?, SHA2(?,256), ?, ?)");
        $stmt->bind_param("ssss", $u, $p, $r, $reports);
        if ($stmt->execute()) $message = "User '$u' created successfully.";
        else $message = "Error: " . $conn->error;
    } elseif ($_POST['action'] === 'delete') {
        $id = (int)$_POST['user_id'];
        if ($id !== $_SESSION['user']['id']) {
            $conn->query("DELETE FROM users WHERE id=$id");
            $message = "User deleted.";
        } else {
            $message = "You cannot delete yourself.";
        }
    } elseif ($_POST['action'] === 'update_role') {
        $id = (int)$_POST['user_id'];
        $role = $_POST['role'];
        $reports = implode(',', $_POST['reports'] ?? ['static']);
        $stmt = $conn->prepare("UPDATE users SET role=?, allowed_reports=? WHERE id=?");
        $stmt->bind_param("ssi", $role, $reports, $id);
        $stmt->execute();
        $message = "User updated.";
    }
}

$users = [];
$result = $conn->query("SELECT id, username, role, allowed_reports, created_at FROM users ORDER BY id");
while ($row = $result->fetch_assoc()) $users[] = $row;
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Management — Pulsar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#0f0e0c; color:#f5f0e8; }
.card { background:#1a1917; border:1px solid #333; border-radius:0; }
.card-header { background:#d63b2f; border-radius:0; font-size:.7rem; letter-spacing:.15em; text-transform:uppercase; }
table { font-size:.8rem; }
th { font-size:.6rem; letter-spacing:.1em; text-transform:uppercase; color:#8a8070; border-color:#333 !important; }
td { border-color:#222 !important; color:#f5f0e8; vertical-align:middle; }
tr:hover td { background:#222 !important; }
.section-label { font-size:.6rem; letter-spacing:.2em; text-transform:uppercase; color:#d63b2f; }
.form-control, .form-select { background:#0f0e0c; border:1px solid #444; color:#f5f0e8; border-radius:0; font-size:.8rem; }
.form-control:focus, .form-select:focus { background:#0f0e0c; color:#f5f0e8; border-color:#d63b2f; box-shadow:none; }
.form-label { font-size:.6rem; letter-spacing:.12em; text-transform:uppercase; color:#8a8070; }
.form-check-label { font-size:.75rem; color:#f5f0e8; }
</style>
</head>
<body>
<?php require_once 'nav.php'; ?>

<div class="container-fluid py-4 px-4">
  <div class="mb-4">
    <div class="section-label">Admin</div>
    <h2 style="font-family:Georgia,serif; font-style:italic;">User Management</h2>
  </div>

  <?php if ($message): ?>
    <div class="alert rounded-0" style="background:#2ac87a20; border:1px solid #2ac87a; color:#2ac87a; font-size:.8rem;"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <!-- Add User -->
  <div class="card mb-4">
    <div class="card-header text-white">Add New User</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
              <option value="analyst">Analyst</option>
              <option value="viewer">Viewer</option>
              <option value="superadmin">Super Admin</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Allowed Reports</label>
            <div class="d-flex gap-3 mt-1">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="reports[]" value="static" checked>
                <label class="form-check-label">Static</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="reports[]" value="performance" checked>
                <label class="form-check-label">Performance</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="reports[]" value="activity" checked>
                <label class="form-check-label">Activity</label>
              </div>
            </div>
          </div>
          <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn text-white w-100" style="background:#d63b2f; border-radius:0; font-size:.75rem;">Add</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Users Table -->
  <div class="card">
    <div class="card-header text-white">All Users</div>
    <div class="card-body p-0">
      <table class="table table-dark table-hover mb-0">
        <thead><tr>
          <th>ID</th><th>Username</th><th>Role</th><th>Allowed Reports</th><th>Created</th><th>Actions</th>
        </tr></thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td>
              <form method="POST" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="action" value="update_role">
                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                <select name="role" class="form-select form-select-sm" style="width:130px;">
                  <option value="superadmin" <?= $u['role']==='superadmin'?'selected':'' ?>>Super Admin</option>
                  <option value="analyst" <?= $u['role']==='analyst'?'selected':'' ?>>Analyst</option>
                  <option value="viewer" <?= $u['role']==='viewer'?'selected':'' ?>>Viewer</option>
                </select>
            </td>
            <td>
                <div class="d-flex gap-2">
                  <?php foreach (['static','performance','activity'] as $rep): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="reports[]" value="<?= $rep ?>"
                      <?= strpos($u['allowed_reports'], $rep) !== false ? 'checked' : '' ?>>
                    <label class="form-check-label"><?= ucfirst($rep) ?></label>
                  </div>
                  <?php endforeach; ?>
                </div>
            </td>
            <td><?= $u['created_at'] ?></td>
            <td>
                <button type="submit" class="btn btn-sm text-white" style="background:#2a7ac8; border-radius:0; font-size:.7rem;">Save</button>
              </form>
              <?php if ($u['id'] !== $_SESSION['user']['id']): ?>
              <form method="POST" class="d-inline">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                <button type="submit" class="btn btn-sm text-white" style="background:#d63b2f; border-radius:0; font-size:.7rem;"
                  onclick="return confirm('Delete <?= htmlspecialchars($u['username']) ?>?')">Delete</button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
