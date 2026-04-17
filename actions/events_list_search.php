<?php
declare(strict_types=1);

function events_list_parse_query(): string
{
    if (!isset($_GET['q']) || !is_string($_GET['q'])) {
        return '';
    }
    $q = trim($_GET['q']);
    if (strlen($q) > 200) {
        return substr($q, 0, 200);
    }
    return $q;
}

/**
 * When $q is non-empty, appends AND (event_name LIKE ? OR activity LIKE ? OR venue LIKE ?).
 * Mutates $types and $params.
 */
function events_list_append_search_fragment(string $q, string &$types, array &$params): string
{
    if ($q === '') {
        return '';
    }
    $like = '%' . $q . '%';
    $types .= 'sss';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    return ' AND (event_name LIKE ? OR activity LIKE ? OR venue LIKE ?)';
}
