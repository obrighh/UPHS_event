<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require __DIR__ . '/../actions/event_date_validate.php';

class EventDateValidationTest extends TestCase
{
    private string $today;
    private string $tomorrow;
    private string $yesterday;
    private string $nextWeek;

    protected function setUp(): void
    {
        $this->today = date('Y-m-d');
        $this->tomorrow = date('Y-m-d', strtotime('+1 day'));
        $this->yesterday = date('Y-m-d', strtotime('-1 day'));
        $this->nextWeek = date('Y-m-d', strtotime('+7 days'));
    }

    public function test_valid_future_dates_returns_null(): void
    {
        $result = validate_event_dates_not_past($this->tomorrow, $this->nextWeek);
        $this->assertNull($result);
    }

    public function test_past_start_date_returns_error(): void
    {
        $result = validate_event_dates_not_past($this->yesterday, $this->tomorrow);
        $this->assertEquals('Start date cannot be in the past.', $result);
    }

    public function test_past_end_date_returns_error(): void
    {
        $result = validate_event_dates_not_past($this->tomorrow, $this->yesterday);
        $this->assertEquals('End date cannot be in the past.', $result);
    }

    public function test_end_date_before_start_date_returns_error(): void
    {
        $result = validate_event_dates_not_past($this->nextWeek, $this->tomorrow);
        $this->assertEquals('End date must be on or after the start date.', $result);
    }

    public function test_invalid_date_format_returns_error(): void
    {
        $result = validate_event_dates_not_past('not-a-date', $this->tomorrow);
        $this->assertEquals('Invalid date format.', $result);
    }

    public function test_same_start_and_end_date_returns_null(): void
    {
        $result = validate_event_dates_not_past($this->tomorrow, $this->tomorrow);
        $this->assertNull($result);
    }
}