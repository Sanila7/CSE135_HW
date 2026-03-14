<?php
require_once 'auth.php';
require_login();
if (!is_analyst()) { header('Location: /403.php'); exit(); }

$report = $_POST['report'] ?? '';
$comment = trim($_POST['comment'] ?? '');

if ($report && $comment) {
    $conn = new mysqli('localhost', 'collector', 'Collector@123!', 'collector_db');
    $stmt = $conn->prepare("INSERT INTO comments (report, user_id, comment) VALUES (?, ?, ?)");
    $uid = $_SESSION['user']['id'];
    $stmt->bind_param("sis", $report, $uid, $comment);
    $stmt->execute();
    $conn->close();
}
header("Location: /report-$report.php");
exit();
?>
