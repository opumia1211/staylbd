<?php
// Temporary layout debugging script

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $logFile = __DIR__ . '/../storage/logs/layout_diagnostics.txt';
    file_put_contents($logFile, $input . "\n", FILE_APPEND | LOCK_EX);
    echo json_encode(['status' => 'success']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Only POST allowed']);
