<?php
declare(strict_types=1);

/**
 * True if `events.is_featured` exists (migration applied). Cached per request.
 */
function events_has_featured_column(mysqli $conn): bool
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $res = @$conn->query("SHOW COLUMNS FROM `events` LIKE 'is_featured'");
    $cached = $res && $res->num_rows > 0;
    return $cached;
}
