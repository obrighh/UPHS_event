<?php
    session_start();
    require 'conn.php';

    if(isset($_POST['update_announcement'])){
        $a_id = $_POST['update_aId'];
        
        $title = $_POST['update_title'];
        $description = $_POST['update_description'];
        $date = $_POST['update_date'];
        $time = $_POST['update_time'];

        $sql = 
        "UPDATE announcement

         SET 
             title = ?
           , description = ?
           , date = ?
           , time = ?

         WHERE a_id = ?  
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $title, $description, $date, $time, $a_id);
        
        $stmt->execute();

        $stmt->close();
        $conn->close();

        echo
        '
            <script> alert("Updated successfully"); </script>
            <script> window.location = "../users/admin/html/announcement.php"; </script>
        ';
    }
?>