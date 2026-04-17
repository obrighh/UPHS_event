<?php

declare(strict_types=1);

/**
 * Countdown event selection for the homepage.
 *
 * @param list<array<string, mixed>> $pool
 * @return array<string, mixed>|null
 */
function index_pick_countdown_event(array $pool, DateTime $now, DateTimeZone $tz): ?array
{
    $future = [];
    foreach ($pool as $row) {
        if (empty($row['date_start'])) {
            continue;
        }
        $timePart = isset($row['time_start']) && (string) $row['time_start'] !== ''
            ? trim((string) $row['time_start'])
            : '00:00:00';
        $ts = trim((string) $row['date_start']) . ' ' . $timePart;
        try {
            $dt = new DateTime($ts, $tz);
        } catch (Exception $e) {
            continue;
        }
        if ($dt > $now) {
            $future[] = ['row' => $row, 'dt' => $dt];
        }
    }
    if ($future === []) {
        return null;
    }

    $mainPriority = array_values(array_filter(
        $future,
        static function (array $x): bool {
            return !empty($x['row']['is_featured']);
        }
    ));
    if ($mainPriority !== []) {
        usort($mainPriority, static function (array $a, array $b): int {
            return $a['dt'] <=> $b['dt'];
        });
        return $mainPriority[0]['row'];
    }

    usort($future, static function (array $a, array $b): int {
        return $a['dt'] <=> $b['dt'];
    });

    $earliestDate = $future[0]['dt']->format('Y-m-d');
    $sameDay = array_values(array_filter(
        $future,
        static function (array $x) use ($earliestDate): bool {
            return $x['dt']->format('Y-m-d') === $earliestDate;
        }
    ));

    if (count($sameDay) === 1) {
        return $sameDay[0]['row'];
    }

    $idx = random_int(0, count($sameDay) - 1);
    return $sameDay[$idx]['row'];
}
