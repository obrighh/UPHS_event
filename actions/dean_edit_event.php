<?php
    session_start();
    require 'conn.php';

    if(isset($_POST['update_event'])){
        $event_id = $_POST['update_eventId'];
        
        $event_name = $_POST['update_eventName'];
        $activity = $_POST['update_activity'];
        $venue = $_POST['update_venue'];
        $date_start = $_POST['update_dateStart'];
        $date_end = $_POST['update_dateEnd'];
        $time_start = $_POST['update_timeStart'];
        $time_end = $_POST['update_timeEnd'];

        
        $sql = 
        "UPDATE events
         SET event_name = ?
           , activity = ?
           , venue = ?
           , date_start = ?
           , date_end = ?
           , time_start = ?
           , time_end = ?
         WHERE event_id = ?  
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $event_name, $activity, $venue, $date_start, $date_end, $time_start, $time_end, $event_id);
        $stmt->execute();

        $stmt->close();
        $conn->close();

        echo
        '
            <script> alert("Updated successfully"); </script>
            <script> window.location = "../users/dean/html/events.php"; </script>
        ';
    }
?>