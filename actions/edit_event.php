<?php
declare(strict_types=1);

session_start();
require 'conn.php';
require_once __DIR__ . '/events_featured_compat.php';

if (!isset($_POST['update_event'])) {
    exit;
}

$user_id = (int) ($_SESSION['id'] ?? 0);
if ($user_id < 1) {
    exit;
}

$event_id = (int) $_POST['update_eventId'];

$verify = $conn->prepare('SELECT event_status FROM events WHERE event_id = ? AND u_id = ?');
$verify->bind_param('ii', $event_id, $user_id);
$verify->execute();
$vrow = $verify->get_result()->fetch_assoc();
$verify->close();

if (!$vrow) {
    echo '<script>alert("Event not found."); history.back();</script>';
    exit;
}

$event_status = (int) $vrow['event_status'];

$event_name = (string) $_POST['update_eventName'];
$activity = (string) $_POST['update_activity'];
$venue = (string) $_POST['update_venue'];
$date_start = (string) $_POST['update_dateStart'];
$date_end = (string) $_POST['update_dateEnd'];
$time_start = (string) $_POST['update_timeStart'];
$time_end = (string) $_POST['update_timeEnd'];

$hasFeatured = events_has_featured_column($conn);

$want_main = $hasFeatured && $event_status === 1
    && isset($_POST['event_priority']) && (string) $_POST['event_priority'] === 'main';
$is_featured = $want_main ? 1 : 0;

if ($hasFeatured && $want_main) {
    $conn->query('UPDATE events SET is_featured = 0 WHERE event_status = 1');
}

if ($hasFeatured) {
    $sql =
        'UPDATE events
         SET event_name = ?
           , activity = ?
           , venue = ?
           , date_start = ?
           , date_end = ?
           , time_start = ?
           , time_end = ?
           , is_featured = ?
         WHERE event_id = ?
           AND u_id = ?
        ';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'sssssssiii',
        $event_name,
        $activity,
        $venue,
        $date_start,
        $date_end,
        $time_start,
        $time_end,
        $is_featured,
        $event_id,
        $user_id
    );
} else {
    $sql =
        'UPDATE events
         SET event_name = ?
           , activity = ?
           , venue = ?
           , date_start = ?
           , date_end = ?
           , time_start = ?
           , time_end = ?
         WHERE event_id = ?
           AND u_id = ?
        ';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'sssssssii',
        $event_name,
        $activity,
        $venue,
        $date_start,
        $date_end,
        $time_start,
        $time_end,
        $event_id,
        $user_id
    );
}
$stmt->execute();
$stmt->close();
$conn->close();

echo
'
    <script> alert("Updated successfully"); </script>
    <script> window.location = "../users/organization/html/events.php?view=requests"; </script>
';
