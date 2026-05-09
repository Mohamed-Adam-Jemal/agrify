<?php
// =============================================================
//  api/admin/auth/logout.php
//  POST — Admin logout
//  Destroys admin session completely.
// =============================================================

declare(strict_types=1);

require_once __DIR__ . '/../../auth/session.php';

// ---------- Only accept POST ---------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed.'], 405);
}

// ---------- Must be logged in as admin -----------------------
requireAdmin();

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
jsonResponse(['success' => true, 'message' => 'Admin logged out successfully.'], 200);