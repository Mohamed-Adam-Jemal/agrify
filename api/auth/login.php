<?php
// =============================================================
//  api/auth/login.php
//  POST — Farmer / Worker login
//  Role detected via DB lookup — not from users table.
//
//  Body (JSON):
//    email     string  required
//    password  string  required
// =============================================================
declare(strict_types=1);
require_once __DIR__ . '/session.php';

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

// ---------- Fetch user ---------------------------------------
$pdo  = getDB();
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

// Wrong email or password — same message to avoid user enumeration
if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(['success' => false, 'error' => 'Invalid email or password.'], 401);
}

// Account inactive
if (!(bool) $user['is_active']) {
    jsonResponse(['success' => false, 'error' => 'Account is inactive. Contact your farm administrator.'], 403);
}

// ---------- Detect role via DB lookup ------------------------
$profile = null;
$role    = null;
$farm_id = null;

// FIX 1 — Check farmers table without JOIN so farmers with no farm are not missed
$stmt = $pdo->prepare('SELECT id AS farmer_id, first_name, last_name, phone FROM farmers WHERE user_id = :user_id');
$stmt->execute([':user_id' => $user['id']]);
$farmerRow = $stmt->fetch();

if ($farmerRow) {
    $role = 'farmer';
    $_SESSION['farmer_id'] = $farmerRow['farmer_id'];

    // Separately fetch their farm (may not exist yet)
    $stmt = $pdo->prepare('SELECT id AS farm_id, name AS farm_name FROM farms WHERE farmer_id = :farmer_id LIMIT 1');
    $stmt->execute([':farmer_id' => $farmerRow['farmer_id']]);
    $farmRow = $stmt->fetch();

    $farm_id = $farmRow['farm_id'] ?? null;
    $profile = array_merge($farmerRow, $farmRow ?: ['farm_id' => null, 'farm_name' => null]);
} else {
    // Check workers table
    $stmt = $pdo->prepare('
        SELECT w.id AS worker_id, w.first_name, w.last_name,
               w.job_title, w.farm_id, fm.name AS farm_name
        FROM workers w
        JOIN farms fm ON fm.id = w.farm_id
        WHERE w.user_id = :user_id
    ');
    $stmt->execute([':user_id' => $user['id']]);
    $workerProfile = $stmt->fetch();

    if ($workerProfile) {
        $role    = 'worker';
        $profile = $workerProfile;
        $farm_id = $workerProfile['farm_id'];
        $_SESSION['worker_id'] = $workerProfile['worker_id'];
    }
}

if (!$role) {
    jsonResponse(['success' => false, 'error' => 'User profile not found.'], 404);
}

// ---------- Update last_login_at -----------------------------
$stmt = $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
$stmt->execute([':id' => $user['id']]);

// FIX 2 — Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// ---------- Start session ------------------------------------
$_SESSION['user_id'] = $user['id'];
$_SESSION['farm_id'] = $farm_id;
$_SESSION['role']    = $role; 
$_SESSION['farm_name'] = $profile['farm_name'] ?? null; 

// ---------- Response -----------------------------------------
jsonResponse([
    'success' => true,
    'message' => 'Login successful.',
    'user'    => [
        'id'        => $user['id'],
        'username'  => $user['username'],
        'email'     => $user['email'],
        'role'      => $role,
        'farm_id'   => $farm_id,
        'farm_name' => $profile['farm_name'] ?? null,
        'profile'   => $profile,
    ],
], 200);