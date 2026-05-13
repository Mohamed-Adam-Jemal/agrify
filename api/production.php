<?php
require __DIR__ . '/auth/session.php';

$pdo    = getDB();
$farmId = $_SESSION['farm_id'];

$stmt = $pdo->prepare('
    SELECT
        p.*,
        z.name AS zone_name,
        z.area AS zone_area
    FROM production p
    LEFT JOIN zones z ON z.id = p.zone_id
    WHERE p.farm_id = :farm_id
    ORDER BY p.planting_date DESC
');
$stmt->execute([':farm_id' => $farmId]);

jsonResponse($stmt->fetchAll());