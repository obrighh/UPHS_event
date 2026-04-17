<?php

declare(strict_types=1);

require __DIR__ . '/../actions/countdown_helper.php';

function build_event(array $data): array
{
    return array_merge([
        'event_id' => 0,
        'event_name' => '',
        'date_start' => '',
        'time_start' => '00:00:00',
        'is_featured' => 0,
    ], $data);
}

function assert_equals($name, $actual, $expected): void
{
    if ($actual === $expected) {
        echo "PASS: {$name}\n";
        return;
    }
    echo "FAIL: {$name} - expected {$expected}, got {$actual}\n";
    exit(1);
}

$now = new DateTime('2026-04-18 10:00:00', new DateTimeZone('Asia/Manila'));
$tz = new DateTimeZone('Asia/Manila');

// Case 1: featured event should win when there are future featured events.
$pool1 = [
    build_event(['event_id' => 1, 'event_name' => 'Main Summit', 'date_start' => '2026-04-20', 'time_start' => '09:00:00', 'is_featured' => 1]),
    build_event(['event_id' => 2, 'event_name' => 'Community Meet', 'date_start' => '2026-04-19', 'time_start' => '08:00:00', 'is_featured' => 0]),
    build_event(['event_id' => 3, 'event_name' => 'Featured Talk', 'date_start' => '2026-04-19', 'time_start' => '11:00:00', 'is_featured' => 1]),
];
$result = index_pick_countdown_event($pool1, $now, $tz);
assert_equals('featured event selected', $result['event_id'], 3);

// Case 2: earliest upcoming event should be selected when no featured event exists.
$pool2 = [
    build_event(['event_id' => 10, 'event_name' => 'First Workshop', 'date_start' => '2026-04-19', 'time_start' => '11:00:00', 'is_featured' => 0]),
    build_event(['event_id' => 11, 'event_name' => 'Later Workshop', 'date_start' => '2026-04-19', 'time_start' => '13:00:00', 'is_featured' => 0]),
    build_event(['event_id' => 12, 'event_name' => 'Next Week Event', 'date_start' => '2026-04-25', 'time_start' => '09:00:00', 'is_featured' => 0]),
];
$result = index_pick_countdown_event($pool2, $now, $tz);
assert_equals('earliest upcoming event selected', $result['event_id'], 10);

// Case 3: when multiple earliest events share the same date, selection must still be one of them.
$pool3 = [
    build_event(['event_id' => 20, 'event_name' => 'Morning Session', 'date_start' => '2026-04-21', 'time_start' => '07:00:00', 'is_featured' => 0]),
    build_event(['event_id' => 21, 'event_name' => 'Afternoon Session', 'date_start' => '2026-04-21', 'time_start' => '14:00:00', 'is_featured' => 0]),
    build_event(['event_id' => 22, 'event_name' => 'Future Event', 'date_start' => '2026-04-23', 'time_start' => '10:00:00', 'is_featured' => 0]),
];
$result = index_pick_countdown_event($pool3, $now, $tz);
if (!in_array($result['event_id'], [20, 21], true)) {
    echo "FAIL: earliest same-day selection should be one of the earliest same-day events, got {$result['event_id']}\n";
    exit(1);
}

echo "PASS: same-day earliest selection returns one of the earliest same-day events\n";

// Case 4: past events are ignored.
$pool4 = [
    build_event(['event_id' => 30, 'event_name' => 'Past Event', 'date_start' => '2026-04-17', 'time_start' => '08:00:00', 'is_featured' => 1]),
    build_event(['event_id' => 31, 'event_name' => 'Upcoming Event', 'date_start' => '2026-04-20', 'time_start' => '09:00:00', 'is_featured' => 0]),
];
$result = index_pick_countdown_event($pool4, $now, $tz);
assert_equals('past events ignored', $result['event_id'], 31);

// Case 5: returns null when there are no future events.
$pool5 = [
    build_event(['event_id' => 40, 'event_name' => 'Expired Session', 'date_start' => '2026-04-17', 'time_start' => '14:00:00', 'is_featured' => 0]),
];
$result = index_pick_countdown_event($pool5, $now, $tz);
if ($result !== null) {
    echo "FAIL: no upcoming events should return null\n";
    exit(1);
}

echo "PASS: no upcoming events returns null\n";

echo "\nAll countdown selection tests passed.\n";
