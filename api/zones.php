<?php
header('Content-Type: application/json');
$file = __DIR__ . '/../data/zones.json';
if (!file_exists($file)) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}
echo file_get_contents($file);
