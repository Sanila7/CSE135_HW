<?php
$team_member = "Sanila Silva (Solo)";
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP Environment Variables</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>PHP Environment Variables</h1>
    <p><strong>Team Member:</strong> <?php echo $team_member; ?></p>
    <p>This page displays the server and request environment variables.</p>

    <table>
        <tr>
            <th>Variable</th>
            <th>Value</th>
        </tr>

        <?php foreach ($_SERVER as $key => $value): ?>
        <tr>
            <td><?php echo htmlspecialchars($key); ?></td>
            <td><?php echo htmlspecialchars($value); ?></td>
        </tr>
        <?php endforeach; ?>

    </table>

    <p><a href="/">Back to Home</a></p>
</body>
</html>
