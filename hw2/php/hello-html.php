<?php
$language = "PHP";
$team_member = "Sanila Silva (Solo)";
$generated_at = date("Y-m-d H:i:s");
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hello HTML - PHP</title>
</head>
<body>
    <h1>Hello from <?php echo $language; ?>!</h1>

    <p><strong>Team Member:</strong> <?php echo $team_member; ?></p>
    <p><strong>Language:</strong> <?php echo $language; ?></p>
    <p><strong>Generated at:</strong> <?php echo $generated_at; ?></p>
    <p><strong>Your IP address:</strong> <?php echo $ip_address; ?></p>

    <p><a href="/">Back to Home</a></p>
</body>
</html>
