<?php
    session_start();
    require 'conn.php';

    if(isset($_POST['submit_announcement'])){
        $title = $_POST['title'];
        $description = $_POST['description'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $u_id = $_SESSION['id'];

        $sql = 
        "INSERT INTO 
            announcement(u_id, title, description, date, time)
         VALUES (?,?,?,?,?)
        ";

        $stmt = $conn->prepare($sql);
        $stmt -> bind_param("issss", $u_id, $title, $description, $date, $time);

        $stmt -> execute();

        $stmt -> close();
        $conn -> close();
        
        echo
        '
            <script> 
                alert ("Posted announcement successfully!");
            </script>
            <script> 
                window.location = "../users/dean/html/announcement.php";
            </script>

        ';
    }

    
?>