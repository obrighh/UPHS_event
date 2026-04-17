<?php
declare(strict_types=1);

require_once __DIR__ . '/event_public_config.php';
require_once __DIR__ . '/events_featured_compat.php';

/**
 * Optional variables before include (e.g. from index.php):
 * @var int    $event_public_retention_days  Override retention (default from constant).
 * @var string $event_public_scope           'upcoming' | 'all' — upcoming = not yet ended.
 * @var string $event_filter_month           '' or 'YYYY-MM'
 * @var string $event_filter_venue           partial match on venue
 */

$retentionDays = isset($event_public_retention_days)
    ? max(30, min(3650, (int) $event_public_retention_days))
    : EVENT_PUBLIC_RETENTION_DAYS_DEFAULT;

$tz = new DateTimeZone('Asia/Manila');
$cutoff = (new DateTime('now', $tz))->modify('-' . $retentionDays . ' days')->format('Y-m-d');

$scope = isset($event_public_scope) ? (string) $event_public_scope : 'upcoming';
if (!in_array($scope, ['upcoming', 'all'], true)) {
    $scope = 'upcoming';
}

$month = isset($event_filter_month) ? trim((string) $event_filter_month) : '';
if ($month !== '' && !preg_match('/^\d{4}-\d{2}$/', $month)) {
    $month = '';
}

$venue = isset($event_filter_venue) ? trim((string) $event_filter_venue) : '';
if (strlen($venue) > 200) {
    $venue = substr($venue, 0, 200);
}

$where = 'event_status = 1 AND COALESCE(date_end, date_start) >= ?';
$types = 's';
$params = [$cutoff];

if ($scope === 'upcoming') {
    $where .= ' AND date_end >= CURDATE()';
}

if ($month !== '') {
    $where .= " AND DATE_FORMAT(date_start, '%Y-%m') = ?";
    $types .= 's';
    $params[] = $month;
}

if ($venue !== '') {
    $where .= ' AND venue LIKE ?';
    $types .= 's';
    $params[] = '%' . $venue . '%';
}

$featSql = events_has_featured_column($conn)
    ? ', COALESCE(is_featured, 0) AS is_featured'
    : '';

$sql = "SELECT event_id, event_name, activity AS description, date_start, date_end,
               time_start, time_end,
               CONCAT(date_start, ' - ', date_end) AS date,
               CONCAT(time_start, ' - ', time_end) AS time,
               venue, event_image
               {$featSql}
        FROM events
        WHERE {$where}
        ORDER BY date_start ASC, time_start ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    if ($featSql === '') {
        $row['is_featured'] = 0;
    }
    $events[] = $row;
}
$stmt->close();
