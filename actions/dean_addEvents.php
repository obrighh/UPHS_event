<?php
declare(strict_types=1);

session_start();
require 'conn.php';
require_once __DIR__ . '/events_featured_compat.php';

$user_id = (int) ($_SESSION['id'] ?? 0);
if ($user_id < 1 || !isset($_POST['submit_event'])) {
    header('Location: ../users/dean/html/events.php');
    exit;
}

$event_name = (string) $_POST['event_name'];
$activity = (string) ($_POST['activity'] ?? '');
$venue = (string) $_POST['venue'];
$date_start = (string) $_POST['date_start'];
$date_end = (string) $_POST['date_end'];
$time_start = (string) $_POST['time_start'];
$time_end = (string) $_POST['time_end'];
$status = 1;

$priority = isset($_POST['event_priority']) ? (string) $_POST['event_priority'] : 'minor';
$is_main = $priority === 'main';

$hasFeatured = events_has_featured_column($conn);
$is_featured = ($hasFeatured && $is_main) ? 1 : 0;

if ($hasFeatured && $is_main) {
    $conn->query('UPDATE events SET is_featured = 0 WHERE event_status = 1');
}

if ($hasFeatured) {
    $sql = 'INSERT INTO events (u_id, event_name, activity, venue, date_start, date_end, time_start, time_end, event_status, is_featured)
            VALUES (?,?,?,?,?,?,?,?,?,?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'isssssssii',
        $user_id,
        $event_name,
        $activity,
        $venue,
        $date_start,
        $date_end,
        $time_start,
        $time_end,
        $status,
        $is_featured
    );
} else {
    $sql = 'INSERT INTO events (u_id, event_name, activity, venue, date_start, date_end, time_start, time_end, event_status)
            VALUES (?,?,?,?,?,?,?,?,?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'isssssssi',
        $user_id,
        $event_name,
        $activity,
        $venue,
        $date_start,
        $date_end,
        $time_start,
        $time_end,
        $status
    );
}

$stmt->execute();
$stmt->close();
$conn->close();

echo '<script> alert("New event added!"); </script>';
echo '<script> window.location = "../users/dean/html/events.php"; </script>';
