<?php
// =============================================================
//  api/auth/session.php
//  PDO connection + session bootstrap
//  Auth guards verify identity against DB — not just session.
// =============================================================

declare(strict_types=1);

// ---------- .env loader --------------------------------------
function loadEnv(string $path): void
{
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        [$key, $value] = array_map('trim', explode('=', $line, 2)) + [1 => ''];
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

// Load .env from project root (two levels up from api/auth/)
loadEnv(dirname(__DIR__, 2) . '/.env.local');

// ---------- DB Configuration ---------------------------------
define('DB_HOST',    $_ENV['DB_HOST']    ?? '127.0.0.1');
define('DB_PORT',    $_ENV['DB_PORT']    ?? '3306');
define('DB_NAME',    $_ENV['DB_NAME']    ?? 'agrify');
define('DB_USER',    $_ENV['DB_USER']    ?? 'root');
define('DB_PASS',    $_ENV['DB_PASS']    ?? '');
define('DB_CHARSET', 'utf8mb4');

// ---------- Session ------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 0),
        'path'     => '/',
        'secure'   => ($_ENV['SESSION_SECURE'] ?? 'false') === 'true',
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

// ---------- PDO singleton ------------------------------------
function getDB(): PDO
{
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        error_log('[agrify] DB connection failed: ' . $e->getMessage());
        jsonResponse(['success' => false, 'error' => 'Database connection failed.'], 503);
    }

    return $pdo;
}

// ---------- Auth guards --------------------------------------

/**
 * Require a logged-in, active farm user.
 * Verifies user_id exists and is active in the DB.
 */
function requireUser(): void
{
    if (empty($_SESSION['user_id'])) {
        jsonResponse(['success' => false, 'error' => 'Unauthorized.'], 401);
    }

    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT is_active FROM users WHERE id = :id');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse(['success' => false, 'error' => 'Unauthorized.'], 401);
    }

    if (!(bool) $user['is_active']) {
        jsonResponse(['success' => false, 'error' => 'Account is inactive.'], 403);
    }
}

/**
 * Require a farmer — verifies a row exists in the farmers table.
 * DB check prevents role escalation via session tampering.
 */
function requireFarmer(): void
{
    requireUser();

    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT id FROM farmers WHERE user_id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);

    if (!$stmt->fetch()) {
        jsonResponse(['success' => false, 'error' => 'Forbidden. Farmer access required.'], 403);
    }
}

/**
 * Require a worker — verifies a row exists in the workers table.
 * DB check prevents role escalation via session tampering.
 */
function requireWorker(): void
{
    requireUser();

    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT id FROM workers WHERE user_id = :user_id');
    $stmt->execute([':user_id' => $_SESSION['user_id']]);

    if (!$stmt->fetch()) {
        jsonResponse(['success' => false, 'error' => 'Forbidden. Worker access required.'], 403);
    }
}

/**
 * Require a platform admin — verifies admin_id exists and is active in DB.
 */
function requireAdmin(): void
{
    if (empty($_SESSION['admin_id'])) {
        jsonResponse(['success' => false, 'error' => 'Unauthorized.'], 401);
    }

    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT is_active FROM admins WHERE id = :id');
    $stmt->execute([':id' => $_SESSION['admin_id']]);
    $admin = $stmt->fetch();

    if (!$admin) {
        jsonResponse(['success' => false, 'error' => 'Unauthorized.'], 401);
    }

    if (!(bool) $admin['is_active']) {
        jsonResponse(['success' => false, 'error' => 'Admin account is inactive.'], 403);
    }
}

// ---------- JSON response helper -----------------------------
function jsonResponse(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}