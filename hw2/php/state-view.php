<?php
session_start();

// Save incoming data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saved_data'])) {
    $_SESSION['saved_data'] = $_POST['saved_data'];
}

$saved = $_SESSION['saved_data'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP State View</title>
</head>
<body>
    <h1>Saved State</h1>
    <p><strong>Team Member:</strong> Sanila Silva (Solo)</p>

    <p>
        <strong>Saved Value:</strong>
        <?php echo $saved ? htmlspecialchars($saved) : "(nothing saved yet)"; ?>
    </p>

    <p><a href="state-form.php">Back to Form</a></p>
    <p><a href="state-clear.php">Clear State</a></p>
    <p><a href="/">Back to Home</a></p>
</body>
</html>
