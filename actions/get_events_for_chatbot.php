<?php
header('Content-Type: application/json');
require_once 'conn.php';

try {
    // Get the time filter from request (default to 'upcoming')
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $timeFilter = isset($data['timeFilter']) ? $data['timeFilter'] : 'upcoming';
    
    // Build query based on time filter
    if ($timeFilter === 'past') {
        // Past events
        $sql = "SELECT 
                    event_id,
                    event_name as name,
                    activity as description,
                    date_start,
                    date_end,
                    time_start,
                    time_end,
                    CONCAT(TIME_FORMAT(time_start, '%h:%i %p'), ' - ', TIME_FORMAT(time_end, '%h:%i %p')) as time_range,
                    venue
                FROM events 
                WHERE event_status = 1 
                AND date_end < CURDATE()
                ORDER BY date_start DESC
                LIMIT 20";
    } else {
        // Upcoming events (default)
        $sql = "SELECT 
                    event_id,
                    event_name as name,
                    activity as description,
                    date_start,
                    date_end,
                    time_start,
                    time_end,
                    CONCAT(TIME_FORMAT(time_start, '%h:%i %p'), ' - ', TIME_FORMAT(time_end, '%h:%i %p')) as time_range,
                    venue
                FROM events 
                WHERE event_status = 1 
                AND date_start >= CURDATE()
                ORDER BY date_start ASC";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $events = [];
    while($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['event_id'],
            'name' => $row['name'],
            'description' => $row['description'] ?? 'No description available',
            'date_start' => date('F j, Y', strtotime($row['date_start'])),
            'date_end' => date('F j, Y', strtotime($row['date_end'])),
            'time_range' => $row['time_range'],
            'venue' => $row['venue'] ?? 'Venue TBA'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'count' => count($events),
        'events' => $events,
        'timeFilter' => $timeFilter
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>