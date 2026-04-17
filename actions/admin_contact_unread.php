<?php

declare(strict_types=1);

/**
 * Unread website inquiries (requires `is_read` on `contact` — run add_contact_inquiry_columns.sql).
 */
function admin_contact_unread(mysqli $conn): int
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $cached = 0;
    $chk = @$conn->query("SHOW COLUMNS FROM `contact` LIKE 'is_read'");
    if (!$chk || $chk->num_rows === 0) {
        return $cached;
    }
    $r = $conn->query('SELECT COUNT(*) AS c FROM `contact` WHERE `is_read` = 0');
    if ($r && ($row = $r->fetch_assoc())) {
        $cached = (int) $row['c'];
    }

    return $cached;
}

function admin_contact_mark_all_read(mysqli $conn): void
{
    $chk = @$conn->query("SHOW COLUMNS FROM `contact` LIKE 'is_read'");
    if (!$chk || $chk->num_rows === 0) {
        return;
    }
    $conn->query('UPDATE `contact` SET `is_read` = 1 WHERE `is_read` = 0');
}
