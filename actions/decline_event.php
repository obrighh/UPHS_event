<?php
    require 'conn.php';

    if(isset($_POST['decline_event'])){
        $event_id = (int) $_POST['decline_eventId'];
        $decline_reason = isset($_POST['decline_reason']) ? trim((string) $_POST['decline_reason']) : '';

        if ($decline_reason === '') {
            echo '<script>alert("Please enter a reason for declining this event."); history.back();</script>';
            exit;
        }
        if (strlen($decline_reason) > 2000) {
            echo '<script>alert("Decline reason is too long (maximum 2000 characters)."); history.back();</script>';
            exit;
        }

        $able = 2;
        $current_date = date('Y-m-d');
        $sql =
        "UPDATE events
         SET 
            event_status = ?
           ,events_date_posted = ? 
           ,decline_reason = ?
        WHERE event_id = ?
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $able, $current_date, $decline_reason, $event_id);

        $stmt->execute();

        echo
        '
            <script> window.location = "../users/admin/html/events.php?view=pending" </script>
        ';
    }
?>