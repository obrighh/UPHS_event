<?php

/**
 * @return string|null Error message, or null when dates are acceptable.
 */
function validate_event_dates_not_past($date_start, $date_end)
{
    if (
        !is_string($date_start) || !is_string($date_end)
        || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_start)
        || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_end)
    ) {
        return 'Invalid date format.';
    }

    $today = date('Y-m-d');
    if ($date_start < $today) {
        return 'Start date cannot be in the past.';
    }
    if ($date_end < $today) {
        return 'End date cannot be in the past.';
    }
    if ($date_end < $date_start) {
        return 'End date must be on or after the start date.';
    }

    return null;
}
