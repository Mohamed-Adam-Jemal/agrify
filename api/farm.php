<?php
require __DIR__ . '/auth/session.php';

$pdo    = getDB();
$farmId = $_SESSION['farm_id'];

$stmt = $pdo->prepare('
    SELECT
        f.*,
        fa.first_name,
        fa.last_name,
        fa.phone AS farmer_phone
    FROM farms f
    JOIN farmers fa ON fa.id = f.farmer_id
    WHERE f.id = :farm_id
      AND f.is_active = 1
');
$stmt->execute([':farm_id' => $farmId]);

jsonResponse($stmt->fetchAll());