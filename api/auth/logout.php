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
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

// ---------- Response -----------------------------------------
jsonResponse(['success' => true, 'message' => 'Logged out successfully.'], 200);