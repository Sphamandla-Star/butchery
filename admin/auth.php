<?php
/**
 * Admin session handling, login protection, and CSRF protection.
 * Include this at the top of every admin page.
 */

declare(strict_types=1);

require_once __DIR__ . '/../php/db.php';
require_once __DIR__ . '/layout.php';

date_default_timezone_set('Africa/Johannesburg');

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

/**
 * Redirects to the login page if no admin is logged in.
 */
function requireLogin(): void
{
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Returns a CSRF token for forms. Creates one if it doesn't exist yet.
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Stops the request if the form's CSRF token doesn't match.
 */
function verifyCsrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    $expected  = $_SESSION['csrf_token'] ?? '';

    if ($expected === '' || !hash_equals($expected, $submitted)) {
        http_response_code(403);
        exit('Invalid or expired form. Please go back, refresh the page, and try again.');
    }
}
