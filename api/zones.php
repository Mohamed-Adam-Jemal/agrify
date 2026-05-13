<?php
require __DIR__ . '/auth/session.php';

$pdo    = getDB();
$farmId = $_SESSION['farm_id'];

$stmt = $pdo->prepare('
    SELECT *
    FROM zones
    WHERE farm_id = :farm_id
      AND is_active = 1
    ORDER BY farm_id
');
$stmt->execute([':farm_id' => $farmId]);

jsonResponse($stmt->fetchAll());