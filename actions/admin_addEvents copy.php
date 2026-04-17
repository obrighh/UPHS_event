<?php
    session_start();
    require 'conn.php';


    $user_id = $_SESSION['id'];

    $event_name = $_POST['event_name'];
    $activity = $_POST['activity'];
    $venue = $_POST['venue'];
    $date_start = $_POST['date_start'];
    $date_end = $_POST['date_end'];
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
    $status = 1;

    if(isset($_POST['submit_event'])){
        $sql = 
        "INSERT INTO events (u_id, event_name, activity, venue, date_start, date_end, time_start, time_end, event_status)
        VALUES (?,?,?,?,?,?,?,?,?)
        ";

        $stmt = $conn -> prepare($sql);
        $stmt->bind_param("isssssssi", $user_id, $event_name, $activity, $venue, $date_start, $date_end, $time_start, $time_end, $status);
        $stmt->execute();

        $stmt->close();
        $conn->close();

        echo
        '
            <script> alert("New event added!"); </script>
            <script> window.location = "../users/admin/html/events.php"; </script>
        ';
    }
?>