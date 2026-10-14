<?php
/**
 * Shared layout functions for admin pages.
 */

declare(strict_types=1);

const ORDER_STATUSES = ['new', 'confirmed', 'ready', 'completed', 'cancelled'];

function renderHeader(string $title): void
{
    $name = e($_SESSION['admin_name'] ?? '');

    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>' . e($title) . ' | Butchery Admin</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>';

    if (isset($_SESSION['admin_id'])) {
        echo '<header class="admin-bar">
            <strong>Butchery Admin</strong>
            <nav>
                <a href="index.php">Dashboard</a>
                <a href="../index.html" target="_blank" rel="noopener">View Site</a>
                <span class="admin-user">' . $name . '</span>
                <a href="logout.php">Log out</a>
            </nav>
        </header>';
    }

    echo '<main class="admin-main">';
}

function renderFooter(): void
{
    echo '</main></body></html>';
}

function statusBadge(string $status): string
{
    return '<span class="badge badge-' . e($status) . '">' . e(ucfirst($status)) . '</span>';
}

function formatDate(string $datetime): string
{
    return date('d M Y, H:i', strtotime($datetime));
}
