<?php
/**
 * Ends the admin session.
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
