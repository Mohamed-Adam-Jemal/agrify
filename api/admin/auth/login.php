<?php
// =============================================================
//  api/admin/auth/login.php
//  POST — Admin login
//  Admin accounts are created manually via DB — no registration.
//
//  Body (JSON):
//    email     string  required
//    password  string  required
// =============================================================

declare(strict_types=1);

require_once __DIR__ . '/../../auth/session.php';

// ---------- Only accept POST ---------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
}

// ---------- Parse JSON body ----------------------------------
$body     = json_decode(file_get_contents('php://input'), true);
$email    = trim($body['email']    ?? '');
$password =      $body['password'] ?? '';

// ---------- Validation ---------------------------------------
$errors = [];
if ($email === '')    $errors[] = 'Email is required.';
if ($password === '') $errors[] = 'Password is required.';

if (!empty($errors)) {
    jsonResponse(['success' => false, 'errors' => $errors], 422);
}

// ---------- Fetch admin --------------------------------------
$pdo  = getDB();
$stmt = $pdo->prepare('SELECT * FROM admins WHERE email = :email');
$stmt->execute([':email' => $email]);
$admin = $stmt->fetch();

// Wrong email or password — same message to avoid enumeration
if (!$admin || !password_verify($password, $admin['password'])) {
    jsonResponse(['success' => false, 'error' => 'Invalid email or password.'], 401);
}

// Account inactive
if (!(bool) $admin['is_active']) {
    jsonResponse(['success' => false, 'error' => 'Admin account is inactive.'], 403);
}

// ---------- Update last_login_at and last_login_ip -----------
$stmt = $pdo->prepare('
    UPDATE admins
    SET last_login_at = NOW(),
        last_login_ip = :ip
    WHERE id = :id
');
$stmt->execute([
    ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ':id' => $admin['id'],
]);

// ---------- Start admin session ------------------------------
$_SESSION['admin_id'] = $admin['id'];

// ---------- Response -----------------------------------------
jsonResponse([
    'success' => true,
    'message' => 'Admin login successful.',
    'admin'   => [
        'id'       => $admin['id'],
        'username' => $admin['username'],
        'email'    => $admin['email'],
    ],
], 200);