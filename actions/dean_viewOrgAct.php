<?php
$org_id = $_GET['id'];

require 'conn.php';

// Get events
$sql_events = "SELECT 
                event_id,
                event_name,
                activity,
                date_start,
                date_end,
                time_start,
                time_end,
                venue,
                events_date_posted,
                'event' as post_type
              FROM events 
              WHERE org_id = ? AND event_status = 1
              ";

$stmt = $conn->prepare($sql_events);
$stmt->bind_param("i", $org_id);
$stmt->execute();
$result_events = $stmt->get_result();

// Get announcements
$sql_announcements = "SELECT 
                        a_id,
                        title,
                        description,
                        announcement_date_posted,
                        'announcement' as post_type
                      FROM announcement 
                      WHERE org_id = ? OR org_id IS NULL AND a_status = 1
                      ";

$stmt2 = $conn->prepare($sql_announcements);
$stmt2->bind_param("i", $org_id);
$stmt2->execute();
$result_announcements = $stmt2->get_result();

// Combine both into one array
$all_posts = [];

while($row = mysqli_fetch_assoc($result_events)){
    $row['sort_date'] = strtotime($row['events_date_posted']);
    $all_posts[] = $row;
}

while($row = mysqli_fetch_assoc($result_announcements)){
    $row['sort_date'] = strtotime($row['announcement_date_posted']);
    $all_posts[] = $row;
}

// Sort by date (newest first)
usort($all_posts, function($a, $b) {
    return $b['sort_date'] - $a['sort_date'];
});

// Display posts
if(count($all_posts) > 0){
    foreach($all_posts as $row){
        
        if($row['post_type'] == 'event'){
            // Display Event
            $event_name = htmlspecialchars($row['event_name']);
            $activity = htmlspecialchars($row['activity']);
            $venue = htmlspecialchars($row['venue']);
            
            $event_date_posted_formatted = date('F j, Y', strtotime($row['events_date_posted']));
            $date_start_formatted = date('F j, Y', strtotime($row['date_start']));
            $date_end_formatted = date('F j, Y', strtotime($row['date_end']));
            $date = $date_start_formatted . ' - ' . $date_end_formatted;
            
            $time_start_formatted = date('g:i A', strtotime($row['time_start']));
            $time_end_formatted = date('g:i A', strtotime($row['time_end']));
            $time = $time_start_formatted . ' - ' . $time_end_formatted;
            
            echo
            "
            <!-- Event Card -->
            <div class='card mb-4'>
              <div class='card-body'>
                <div class='d-flex align-items-center mb-3'>
                  <div class='avatar me-3'>
                    <img src='../assets/img/avatars/5.png' alt='Avatar' class='rounded-circle' width='40'>
                  </div>
                  <div class='flex-grow-1'>
                    <h6 class='mb-0'>Computer Science Society</h6>
                    <small class='text-muted'>Posted on $event_date_posted_formatted</small>
                  </div>
                </div>
                
                <div class='mb-3'>
                  <span class='badge bg-label-primary mb-2'>Event</span>
                  <h5 class='mb-2'>$event_name</h5>
                  <p class='mb-2'>$activity</p>
                  <div class='text-muted'>
                    <small><i class='bx bx-calendar me-1'></i>$date</small>
                    <small class='ms-3'><i class='bx bx-time me-1'></i>$time</small>
                    <small class='ms-3'><i class='bx bx-map me-1'></i>$venue</small>
                  </div>
                </div>
              </div>
            </div>
            ";
        }
        
        if($row['post_type'] == 'announcement'){
            // Display Announcement
            $title = htmlspecialchars($row['title']);
            $description = htmlspecialchars($row['description']);
            $announcement_date_posted_formatted = date('F j, Y', strtotime($row['announcement_date_posted']));
            
            echo
            "
            <!-- Announcement Card -->
            <div class='card mb-4'>
              <div class='card-body'>
                <div class='d-flex align-items-center mb-3'>
                  <div class='avatar me-3'>
                    <img src='../assets/img/avatars/5.png' alt='Avatar' class='rounded-circle' width='40'>
                  </div>
                  <div class='flex-grow-1'>
                    <h6 class='mb-0'>Computer Science Society</h6>
                    <small class='text-muted'>Posted on $announcement_date_posted_formatted</small>
                  </div>
                </div>
                
                <div class='mb-3'>
                  <span class='badge bg-label-warning mb-2'>Announcement</span>
                  <h5 class='mb-2'>$title</h5>
                  <p class='mb-2'>$description</p>
                </div>
              </div>
            </div>
            ";
        }
    }
}
else{
    echo "
        <div class='text-center py-5'>
            <i class='bx bx-info-circle' style='font-size: 48px; color: #999;'></i>
            <h5 class='mt-3 text-muted'>No Post Available</h5>
            <p class='text-muted'>There are currently no post to display.</p>
        </div>
    ";
}

$stmt->close();
$stmt2->close();
$conn->close();
?>