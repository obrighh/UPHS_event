<?php
    require 'conn.php';

    if(isset($_POST['delete_user'])){
        $user_id = $_POST['delete_userId'];
        $disable = 0;
        $sql = 
        "UPDATE accounts
         SET user_status = ?
         WHERE user_id = ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt -> bind_param("ii", $disable, $user_id);

        $stmt -> execute();

        $stmt -> close();
        $conn -> close();

        header('Location: ../users/admin/html/manageAccounts.php');
    }        

?>