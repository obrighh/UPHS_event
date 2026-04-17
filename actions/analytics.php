<?php
session_start();
require 'conn.php';

$current_month = date('m');
$current_year = date('Y');
$last_month = date('m', strtotime('-1 month'));
$last_month_year = date('Y', strtotime('-1 month'));

$sql_total_events = "SELECT COUNT(*) as total FROM events WHERE event_status = 1";
$result_total_events = $conn->query($sql_total_events);
$total_events = $result_total_events->fetch_assoc()['total'];

$sql_current_month_events = "SELECT COUNT(*) as total FROM events 
                              WHERE event_status = 1 
                              AND MONTH(date_start) = $current_month 
                              AND YEAR(date_start) = $current_year";
$result_current_month_events = $conn->query($sql_current_month_events);
$current_month_events = $result_current_month_events->fetch_assoc()['total'];

$sql_last_month_events = "SELECT COUNT(*) as total FROM events 
                          WHERE event_status = 1 
                          AND MONTH(date_start) = $last_month 
                          AND YEAR(date_start) = $last_month_year";
$result_last_month_events = $conn->query($sql_last_month_events);
$last_month_events = $result_last_month_events->fetch_assoc()['total'];

$events_percentage = 0;
if($last_month_events > 0) {
    $events_percentage = (($current_month_events - $last_month_events) / $last_month_events) * 100;
} elseif($current_month_events > 0) {
    $events_percentage = 100;
}

$sql_total_announcements = "SELECT COUNT(*) as total FROM announcement WHERE a_status = 1";
$result_total_announcements = $conn->query($sql_total_announcements);
$total_announcements = $result_total_announcements->fetch_assoc()['total'];

$sql_current_month_announcements = "SELECT COUNT(*) as total FROM announcement 
                                     WHERE a_status = 1 
                                     AND MONTH(announcement_date_posted) = $current_month 
                                     AND YEAR(announcement_date_posted) = $current_year";
$result_current_month_announcements = $conn->query($sql_current_month_announcements);
$current_month_announcements = $result_current_month_announcements->fetch_assoc()['total'];

$sql_last_month_announcements = "SELECT COUNT(*) as total FROM announcement
                                  WHERE a_status = 1 
                                  AND MONTH(announcement_date_posted) = $last_month 
                                  AND YEAR(announcement_date_posted) = $last_month_year";
$result_last_month_announcements = $conn->query($sql_last_month_announcements);
$last_month_announcements = $result_last_month_announcements->fetch_assoc()['total'];

$announcements_percentage = 0;
if($last_month_announcements > 0) {
    $announcements_percentage = (($current_month_announcements - $last_month_announcements) / $last_month_announcements) * 100;
} elseif($current_month_announcements > 0) {
    $announcements_percentage = 100;
}

// $sql_total_activities = "SELECT COUNT(*) as total FROM logbook WHERE logbook_status = 1";
// $result_total_activities = $conn->query($sql_total_activities);
// $total_activities = $result_total_activities->fetch_assoc()['total'];

// $sql_current_month_activities = "SELECT COUNT(*) as total FROM logbook 
//                                   WHERE logbook_status = 1 
//                                   AND MONTH(date) = $current_month 
//                                   AND YEAR(date) = $current_year";
// $result_current_month_activities = $conn->query($sql_current_month_activities);
// $current_month_activities = $result_current_month_activities->fetch_assoc()['total'];

// $sql_last_month_activities = "SELECT COUNT(*) as total FROM logbook 
//                                WHERE logbook_status = 1 
//                                AND MONTH(date) = $last_month 
//                                AND YEAR(date) = $last_month_year";
// $result_last_month_activities = $conn->query($sql_last_month_activities);
// $last_month_activities = $result_last_month_activities->fetch_assoc()['total'];

// $activities_percentage = 0;
// if($last_month_activities > 0) {
//     $activities_percentage = (($current_month_activities - $last_month_activities) / $last_month_activities) * 100;
// } elseif($current_month_activities > 0) {
//     $activities_percentage = 100;
// }

$sql_total_users = "SELECT COUNT(*) as total FROM accounts WHERE user_status = 1";
$result_total_users = $conn->query($sql_total_users);
$total_users_count = $result_total_users->fetch_assoc()['total'];

$users_by_month = [];
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

for($i = 1; $i <= 12; $i++) {
    $sql_month_users = "SELECT COUNT(*) as total FROM accounts 
                        WHERE user_status = 1 
                        AND MONTH(date_created) <= $i 
                        AND YEAR(date_created) = $current_year";
    $result_month_users = $conn->query($sql_month_users);
    $month_count = $result_month_users->fetch_assoc()['total'];
    $users_by_month[] = $month_count;
}

$users_chart_data = json_encode($users_by_month);
$months_labels = json_encode($months);

$sql_recent_users = "SELECT user_id, f_name, m_name, l_name, username, email, sch_id, date_created 
                     FROM accounts 
                     WHERE user_status = 1 
                     ORDER BY date_created DESC 
                     LIMIT 10";
$result_users = $conn->query($sql_recent_users);

?>