<?php
// =============================================================
//  api/auth/register.php
//  POST — Farmer registration
//  Creates user + farmer + farm in a single transaction.
//
//  Body (JSON):
//    username       string  required
//    email          string  required
//    password       string  required (min 8 chars)
//    first_name     string  optional
//    last_name      string  optional
//    phone          string  optional
//    farm_name      string  required
//    farm_location  string  required
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
$first_name    = trim($body['first_name']    ?? '');
$last_name     = trim($body['last_name']     ?? '');
$phone         = trim($body['phone']         ?? '');
$farm_name     = trim($body['farm_name']     ?? '');
$farm_location = trim($body['farm_location'] ?? '');

// ---------- Validation ---------------------------------------
$errors = [];

if ($username === '')       $errors[] = 'Username is required.';
if ($email === '')          $errors[] = 'Email is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
if ($farm_name === '')      $errors[] = 'Farm name is required.';
if ($farm_location === '')  $errors[] = 'Farm location is required.';

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

// Transaction — all three inserts must succeed together
try {
    $pdo->beginTransaction();

    // 1. Create user
    $stmt = $pdo->prepare('
        INSERT INTO users (username, email, password)
        VALUES (:username, :email, :password)
    ');
    $stmt->execute([
        ':username' => $username,
        ':email'    => $email,
        ':password' => password_hash($password, PASSWORD_BCRYPT),
    ]);
    $user_id = (int) $pdo->lastInsertId();

    // 2. Create farmer profile
    $stmt = $pdo->prepare('
        INSERT INTO farmers (user_id, first_name, last_name, phone)
        VALUES (:user_id, :first_name, :last_name, :phone)
    ');
    $stmt->execute([
        ':user_id'    => $user_id,
        ':first_name' => $first_name ?: null,
        ':last_name'  => $last_name  ?: null,
        ':phone'      => $phone      ?: null,
    ]);
    $farmer_id = (int) $pdo->lastInsertId();

    // 3. Create farm linked to farmer
    $stmt = $pdo->prepare('
        INSERT INTO farms (farmer_id, name, location)
        VALUES (:farmer_id, :name, :location)
    ');
    $stmt->execute([
        ':farmer_id' => $farmer_id,
        ':name'      => $farm_name,
        ':location'  => $farm_location,
    ]);
    $farm_id = (int) $pdo->lastInsertId();

    $pdo->commit();

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('[agrify] Register failed: ' . $e->getMessage());
    jsonResponse(['success' => false, 'error' => 'Registration failed. Please try again.'], 500);
}

// ---------- Start session ------------------------------------
$_SESSION['user_id']   = $user_id;
$_SESSION['farmer_id'] = $farmer_id;
$_SESSION['farm_id']   = $farm_id;
$_SESSION['role']      = 'farmer';

// ---------- Response -----------------------------------------
jsonResponse([
    'success' => true,
    'message' => 'Registration successful.',
    'user'    => [
        'id'        => $user_id,
        'username'  => $username,
        'email'     => $email,
        'role'      => 'farmer',
        'farmer_id' => $farmer_id,
        'farm_id'   => $farm_id,
    ],
], 201);