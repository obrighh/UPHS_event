<?php
    session_start();
    require 'conn.php';

    if(isset($_POST['update_event'])){
        $rv = $_POST['return_view'] ?? 'approved';
        if (!in_array($rv, ['approved', 'declined', 'pending'], true)) {
            $rv = 'approved';
        }
        $adminEventsBack = '../users/admin/html/events.php?view=' . urlencode($rv);

        $event_id = $_POST['update_eventId'];
        $event_name = $_POST['update_eventName'];
        $activity = $_POST['update_activity'];
        $venue = $_POST['update_venue'];
        $date_start = $_POST['update_dateStart'];
        $date_end = $_POST['update_dateEnd'];
        $time_start = $_POST['update_timeStart'];
        $time_end = $_POST['update_timeEnd'];
        $current_image = $_POST['current_image'] ?? '';
        
        $event_image = $current_image; // Keep current image by default
        $upload_dir = '../uploads/events/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Check if new image is uploaded
        if(isset($_FILES['update_eventImage']) && $_FILES['update_eventImage']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $_FILES['update_eventImage']['type'];
            
            if(in_array($file_type, $allowed_types)) {
                // Delete old image if exists and is not empty
                if($current_image && file_exists($upload_dir . $current_image)) {
                    unlink($upload_dir . $current_image);
                }
                
                // Upload new image
                $file_extension = pathinfo($_FILES['update_eventImage']['name'], PATHINFO_EXTENSION);
                $new_filename = 'event_' . time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if(move_uploaded_file($_FILES['update_eventImage']['tmp_name'], $upload_path)) {
                    $event_image = $new_filename;
                } else {
                    echo '<script>alert("Error uploading new image!");</script>';
                    echo '<script>window.location = ' . json_encode($adminEventsBack) . ';</script>';
                    exit();
                }
            } else {
                echo '<script>alert("Invalid image type. Please upload JPEG, PNG, GIF, or WEBP.");</script>';
                echo '<script>window.location = ' . json_encode($adminEventsBack) . ';</script>';
                exit();
            }
        }
        
        // Update database with image
        $sql = 
        "UPDATE events
         SET event_name = ?
           , activity = ?
           , venue = ?
           , date_start = ?
           , date_end = ?
           , time_start = ?
           , time_end = ?
           , event_image = ?
         WHERE event_id = ?  
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $event_name, $activity, $venue, $date_start, $date_end, $time_start, $time_end, $event_image, $event_id);
        $stmt->execute();

        $stmt->close();
        $conn->close();

        echo '<script> alert("Updated successfully"); </script>'
            . '<script> window.location = ' . json_encode($adminEventsBack) . ';</script>';
    }
?>