<?php
    require 'conn.php';

    if(isset($_POST['accept_event'])){
        $event_id = $_POST['accept_eventId'];
        $able = 1;
        $current_date = date('Y-m-d');
        $sql = 
        "UPDATE events
         SET 
            event_status = ?
           ,events_date_posted = ? 
           ,decline_reason = NULL
        WHERE event_id = ?
        ";

        $stmt = $conn->prepare($sql);
        $stmt-> bind_param("isi", $able, $current_date, $event_id);

        $stmt -> execute();

        echo
        '
            <script> window.location = "../users/admin/html/events.php?view=pending" </script>
        ';
    }
?>