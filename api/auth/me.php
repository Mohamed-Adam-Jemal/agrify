<?php
declare(strict_types=1);
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
}

requireUser(); // already handles 401 if not logged in

$pdo = getDB();
$stmt = $pdo->prepare(
    'SELECT u.email,
            COALESCE(f.first_name, w.first_name) AS first_name,
            COALESCE(f.last_name, w.last_name) AS last_name
     FROM users u
     LEFT JOIN farmers f ON f.user_id = u.id
     LEFT JOIN workers w ON w.user_id = u.id
     WHERE u.id = :id'
);
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

$fullName = trim(sprintf('%s %s', $user['first_name'] ?? '', $user['last_name'] ?? ''));

jsonResponse([
  'success' => true,
  'user'    => [
    'id'        => $_SESSION['user_id'],
    'email'     => $user['email'] ?? null,
    'first_name'=> $user['first_name'] ?? null,
    'last_name' => $user['last_name'] ?? null,
    'full_name' => $fullName !== '' ? $fullName : null,
    'farm_id'   => $_SESSION['farm_id']  ?? null,
    'role'      => $_SESSION['role']     ?? null,
    'farm_name' => $_SESSION['farm_name'] ?? null,
  ],
]);