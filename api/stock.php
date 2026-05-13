<?php
require __DIR__ . '/auth/session.php';

$pdo    = getDB();
$farmId = $_SESSION['farm_id'];

$stmt = $pdo->prepare('
    SELECT *
    FROM stock
    WHERE farm_id = :farm_id
    ORDER BY category, name
');
$stmt->execute([':farm_id' => $farmId]);

jsonResponse($stmt->fetchAll());