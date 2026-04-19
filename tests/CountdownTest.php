<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

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

class CountdownTest extends TestCase
{
    private DateTime $now;
    private DateTimeZone $tz;

    protected function setUp(): void
    {
        $this->tz = new DateTimeZone('Asia/Manila');
        $this->now = new DateTime('2026-04-18 10:00:00', $this->tz);
    }

    public function test_featured_event_is_selected_over_earlier_non_featured(): void
    {
        $pool = [
            build_event(['event_id' => 1, 'event_name' => 'Main Summit', 'date_start' => '2026-04-20', 'time_start' => '09:00:00', 'is_featured' => 1]),
            build_event(['event_id' => 2, 'event_name' => 'Community Meet', 'date_start' => '2026-04-19', 'time_start' => '08:00:00', 'is_featured' => 0]),
            build_event(['event_id' => 3, 'event_name' => 'Featured Talk', 'date_start' => '2026-04-19', 'time_start' => '11:00:00', 'is_featured' => 1]),
        ];
        $result = index_pick_countdown_event($pool, $this->now, $this->tz);
        $this->assertEquals(3, $result['event_id']);
    }

    public function test_earliest_upcoming_event_selected_when_no_featured(): void
    {
        $pool = [
            build_event(['event_id' => 10, 'event_name' => 'First Workshop', 'date_start' => '2026-04-19', 'time_start' => '11:00:00', 'is_featured' => 0]),
            build_event(['event_id' => 11, 'event_name' => 'Later Workshop', 'date_start' => '2026-04-19', 'time_start' => '13:00:00', 'is_featured' => 0]),
            build_event(['event_id' => 12, 'event_name' => 'Next Week Event', 'date_start' => '2026-04-25', 'time_start' => '09:00:00', 'is_featured' => 0]),
        ];
        $result = index_pick_countdown_event($pool, $this->now, $this->tz);
        $this->assertEquals(10, $result['event_id']);
    }

    public function test_same_day_earliest_selection_returns_one_of_earliest_events(): void
    {
        $pool = [
            build_event(['event_id' => 20, 'event_name' => 'Morning Session', 'date_start' => '2026-04-21', 'time_start' => '07:00:00', 'is_featured' => 0]),
            build_event(['event_id' => 21, 'event_name' => 'Afternoon Session', 'date_start' => '2026-04-21', 'time_start' => '14:00:00', 'is_featured' => 0]),
            build_event(['event_id' => 22, 'event_name' => 'Future Event', 'date_start' => '2026-04-23', 'time_start' => '10:00:00', 'is_featured' => 0]),
        ];
        $result = index_pick_countdown_event($pool, $this->now, $this->tz);
        $this->assertContains($result['event_id'], [20, 21]);
    }

    public function test_past_events_are_ignored(): void
    {
        $pool = [
            build_event(['event_id' => 30, 'event_name' => 'Past Event', 'date_start' => '2026-04-17', 'time_start' => '08:00:00', 'is_featured' => 1]),
            build_event(['event_id' => 31, 'event_name' => 'Upcoming Event', 'date_start' => '2026-04-20', 'time_start' => '09:00:00', 'is_featured' => 0]),
        ];
        $result = index_pick_countdown_event($pool, $this->now, $this->tz);
        $this->assertEquals(31, $result['event_id']);
    }

    public function test_returns_null_when_no_future_events(): void
    {
        $pool = [
            build_event(['event_id' => 40, 'event_name' => 'Expired Session', 'date_start' => '2026-04-17', 'time_start' => '14:00:00', 'is_featured' => 0]),
        ];
        $result = index_pick_countdown_event($pool, $this->now, $this->tz);
        $this->assertNull($result);
    }
}