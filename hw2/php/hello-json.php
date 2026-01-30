<?php
header("Content-Type: application/json");

$response = [
    "message" => "Hello from PHP!",
    "language" => "PHP",
    "team_member" => "Sanila Silva (Solo)",
    "generated_at" => date("Y-m-d H:i:s"),
    "ip_address" => $_SERVER['REMOTE_ADDR'] ?? "Unknown"
];

echo json_encode($response, JSON_PRETTY_PRINT);
