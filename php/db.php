<?php
/**
 * Database connection and shared helper functions.
 * Included by submit_order.php and send_message.php.
 */

declare(strict_types=1);

// ---------- Database settings (XAMPP defaults) ----------
// Change DB_PASS if you have set a password for MySQL.
define('DB_HOST', 'localhost');
define('DB_NAME', 'butchery');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a PDO connection. Reuses the same connection if called again.
 */
function getDb(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    return $pdo;
}

/**
 * Trims a user-supplied value and limits its length.
 */
function cleanText(mixed $value, int $maxLength = 255): string
{
    $text = is_string($value) ? trim($value) : '';
    return mb_substr($text, 0, $maxLength);
}

/**
 * Escapes text for safe display in HTML.
 */
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Sends a JSON response with the given HTTP status code and exits.
 */
function jsonResponse(int $status, array $data): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
