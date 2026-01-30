<?php
// Set response type
header("Content-Type: text/html");

// Basic request info
$method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
$host = $_SERVER['HTTP_HOST'] ?? 'Unknown';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$time = date("Y-m-d H:i:s");
$content_type = $_SERVER['CONTENT_TYPE'] ?? '';

// Read input data
$data = [];

// GET
if ($method === 'GET') {
    $data = $_GET;
}

// POST (form or JSON)
elseif ($method === 'POST') {
    if (strpos($content_type, 'application/json') !== false) {
        $raw = file_get_contents("php://input");
        $data = json_decode($raw, true) ?? [];
    } else {
        $data = $_POST;
    }
}

// PUT / DELETE
elseif ($method === 'PUT' || $method === 'DELETE') {
    $raw = file_get_contents("php://input");

    if (strpos($content_type, 'application/json') !== false) {
        $data = json_decode($raw, true) ?? [];
    } else {
        parse_str($raw, $data);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP Echo</title>
</head>
<body>
    <h1>PHP Echo Response</h1>

    <p><strong>Team Member:</strong> Sanila Silva (Solo)</p>
    <p><strong>Method:</strong> <?php echo htmlspecialchars($method); ?></p>
    <p><strong>Host:</strong> <?php echo htmlspecialchars($host); ?></p>
    <p><strong>Generated at:</strong> <?php echo $time; ?></p>
    <p><strong>User Agent:</strong> <?php echo htmlspecialchars($user_agent); ?></p>
    <p><strong>IP Address:</strong> <?php echo htmlspecialchars($ip); ?></p>

    <h3>Echoed Data</h3>
    <pre><?php echo json_encode($data, JSON_PRETTY_PRINT); ?></pre>

    <p><a href="/echo-form.html">Back to Echo Form</a></p>
    <p><a href="/">Back to Home</a></p>
</body>
</html>
