<?php
// =============================================================
//  api/auth/register.php
//  POST — Farmer registration
//  Creates a farm and a farmer account in one transaction.
//
//  Required body (JSON):
//    username     string
//    email        string
//    password     string  (min 8 chars)
//    farm_name    string
//    farm_location string
// =============================================================

declare(strict_types=1);

require_once __DIR__ . '/session.php';

// ---------- Only accept POST ---------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
}

// ---------- Parse JSON body ----------------------------------
$body = json_decode(file_get_contents('php://input'), true);

$username      = trim($body['username']      ?? '');
$email         = trim($body['email']         ?? '');
$password      =      $body['password']      ?? '';
$farm_name     = trim($body['farm_name']     ?? '');
$farm_location = trim($body['farm_location'] ?? '');

// ---------- Validation ---------------------------------------
$errors = [];

if ($username === '')      $errors[] = 'Username is required.';
if ($email === '')         $errors[] = 'Email is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
if ($farm_name === '')     $errors[] = 'Farm name is required.';
if ($farm_location === '') $errors[] = 'Farm location is required.';

if (!empty($errors)) {
    jsonResponse(['success' => false, 'errors' => $errors], 422);
}

// ---------- DB operations ------------------------------------
$pdo = getDB();

// Check duplicates
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email OR username = :username');
$stmt->execute([':email' => $email, ':username' => $username]);
if ($stmt->fetch()) {
    jsonResponse(['success' => false, 'error' => 'Email or username already taken.'], 409);
}

// Wrap in a transaction — farm + user must both succeed or both fail
try {
    $pdo->beginTransaction();

    // 1. Create the farm
    $stmt = $pdo->prepare('
        INSERT INTO farms (name, location)
        VALUES (:name, :location)
    ');
    $stmt->execute([
        ':name'     => $farm_name,
        ':location' => $farm_location,
    ]);
    $farm_id = (int) $pdo->lastInsertId();

    // 2. Create the farmer user
    $stmt = $pdo->prepare('
        INSERT INTO users (farm_id, username, email, password, role)
        VALUES (:farm_id, :username, :email, :password, :role)
    ');
    $stmt->execute([
        ':farm_id'  => $farm_id,
        ':username' => $username,
        ':email'    => $email,
        ':password' => password_hash($password, PASSWORD_BCRYPT),
        ':role'     => 'farmer',
    ]);
    $user_id = (int) $pdo->lastInsertId();

    $pdo->commit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('[agrify] Register failed: ' . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'Registration failed. Please try again.'], 500);
}

// ---------- Start session ------------------------------------
$_SESSION['user_id'] = $user_id;
$_SESSION['farm_id'] = $farm_id;
$_SESSION['role']    = 'farmer';

// ---------- Response -----------------------------------------
jsonResponse([
    'success' => true,
    'message' => 'Registration successful.',
    'user'    => [
        'id'       => $user_id,
        'username' => $username,
        'email'    => $email,
        'role'     => 'farmer',
        'farm_id'  => $farm_id,
    ],
], 201);