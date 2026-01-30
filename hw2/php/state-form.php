<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP State Form</title>
</head>
<body>
    <h1>PHP State Demo</h1>
    <p><strong>Team Member:</strong> Sanila Silva (Solo)</p>

    <form method="POST" action="state-view.php">
        <label>Enter data to save:</label><br>
        <input type="text" name="saved_data" required>
        <br><br>
        <button type="submit">Save Data</button>
    </form>

    <p><a href="state-view.php">View Saved Data</a></p>
    <p><a href="/">Back to Home</a></p>
</body>
</html>
