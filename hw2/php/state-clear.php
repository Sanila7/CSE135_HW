<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP State Cleared</title>
</head>
<body>
    <h1>State Cleared</h1>
    <p>The session data has been removed.</p>

    <p><a href="state-form.php">Start Again</a></p>
    <p><a href="/">Back to Home</a></p>
</body>
</html>
