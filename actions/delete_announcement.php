<?php
    require 'conn.php';

    if(isset($_POST['delete_announcement'])){
        $a_id = $_POST['delete_aId'];
        $disable = 0;
        $sql = 
        "UPDATE announcement
         SET a_status = ?
         WHERE a_id = ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt -> bind_param("ii", $disable, $a_id);

        $stmt -> execute();

        $stmt -> close();
        $conn -> close();

        header('Location: ../users/organization/html/announcement.php');
    }        

?>