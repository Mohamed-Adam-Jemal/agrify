<?php
// =============================================================
//  api/auth/logout.php
//  POST — Farmer / Worker logout
//  Destroys session completely.
// =============================================================

declare(strict_types=1);

require_once __DIR__ . '/session.php';

// ---------- Only accept POST ---------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
}

// ---------- Must be logged in --------------------------------
requireUser();

// ---------- Destroy session ----------------------------------
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
        setcookie(session_name(), '', [
        'expires'  => time() - 3600,
        'path'     => $params['path'],
        'domain'   => $params['domain'],
        'secure'   => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => 'Strict',
    ]);
}

session_destroy();

// ---------- Response -----------------------------------------
jsonResponse(['success' => true, 'message' => 'Logged out successfully.'], 200);