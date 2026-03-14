<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: /dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $conn = new mysqli('localhost', 'collector', 'Collector@123!', 'collector_db');
    $stmt = $conn->prepare("SELECT id, username, role, allowed_reports FROM users WHERE username = ? AND password = SHA2(?, 256)");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $conn->close();

    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: /dashboard.php');
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pulsar Analytics — Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #0f0e0c; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
.login-card { background: #1a1917; border: 1px solid #333; border-radius: 0; max-width: 400px; width: 100%; padding: 2.5rem; }
.logo { font-size: 2.5rem; font-style: italic; color: #d63b2f; font-family: Georgia, serif; }
.subtitle { font-size: .65rem; letter-spacing: .2em; text-transform: uppercase; color: #8a8070; }
.form-control { background: #0f0e0c; border: none; border-bottom: 1px solid #555; border-radius: 0; color: #f5f0e8; padding-left: 0; }
.form-control:focus { background: #0f0e0c; color: #f5f0e8; border-color: #d63b2f; box-shadow: none; }
.form-label { font-size: .6rem; letter-spacing: .15em; text-transform: uppercase; color: #8a8070; }
.btn-login { background: #d63b2f; border: none; border-radius: 0; width: 100%; padding: .8rem; font-family: monospace; letter-spacing: .08em; }
.btn-login:hover { background: #b83025; }
</style>
</head>
<body>
<div class="login-card">
    <div class="logo mb-1">Pulsar</div>
    <div class="subtitle mb-4">Analytics Dashboard</div>
    <?php if ($error): ?>
        <div class="alert alert-danger py-2 rounded-0" style="font-size:.8rem;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-login text-white">Login →</button>
    </form>
</div>
</body>
</html>

