<?php
    session_start();
    require 'conn.php';

    if(isset($_POST['send_password'])){
        $new_pass = $_POST['new_pass'];
        $confirm_pass = $_POST['confirm_pass'];
        $email = $_SESSION['user'];

        if($confirm_pass == $new_pass){
            $hashedPassword = password_hash($confirm_pass, PASSWORD_DEFAULT);
            $sql=
            "UPDATE accounts
             SET password = ?
             WHERE email = ?
            ";

            $stmt = $conn->prepare($sql);
            $stmt -> bind_param("ss", $hashedPassword, $email);
            $stmt -> execute();

            echo 
            '
                <script>
                    alert("Changed successfully!");
                </script>
            ';
            echo 
            '
                <script>
                    window.location = "../login.php ";
                </script>
            ';
            
        }
    }
?>