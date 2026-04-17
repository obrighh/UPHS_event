<?php
    require 'conn.php';

    if(isset($_POST['delete_event'])){
        $rv = $_POST['return_view'] ?? 'approved';
        if (!in_array($rv, ['approved', 'declined', 'pending'], true)) {
            $rv = 'approved';
        }
        $adminEventsBack = '../users/admin/html/events.php?view=' . urlencode($rv);

        $event_id = $_POST['delete_eventId'];
        $disable = 0;
        $sql = 
        "UPDATE events
         SET event_status = ?
         WHERE event_id = ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt -> bind_param("ii", $disable, $event_id);

        $stmt -> execute();

        $stmt -> close();
        $conn -> close();

        header('Location: ' . $adminEventsBack);
    }        

?>