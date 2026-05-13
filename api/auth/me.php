<?php
declare(strict_types=1);
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
}

requireUser(); // already handles 401 if not logged in

jsonResponse([
  'success' => true,
  'user'    => [
    'id'        => $_SESSION['user_id'],
    'farm_id'   => $_SESSION['farm_id']  ?? null,
    'role'      => $_SESSION['role']     ?? null,
    'farm_name' => $_SESSION['farm_name'] ?? null, // ← add this
  ],
]);